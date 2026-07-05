<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SarprasAsset;
use App\Models\SarprasRoom;
use App\Models\SarprasNeed;
use App\Models\SarprasMaintenance;
use App\Models\SarprasProcurement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SarprasController extends Controller
{
    public function dashboard()
    {
        $totalAssets = SarprasAsset::count();
        $totalRooms = SarprasRoom::count();
        $pendingNeeds = SarprasNeed::whereIn('status', ['diajukan', 'disetujui'])->count();
        $pendingMaintenances = SarprasMaintenance::whereIn('status', ['dilaporkan', 'dicek', 'proses_perbaikan'])->count();
        $procurementsInProgress = SarprasProcurement::whereIn('status', ['rencana', 'proses'])->count();

        $assetsByCategory = SarprasAsset::selectRaw('category, count(*) as total')
            ->groupBy('category')->pluck('total', 'category');

        $assetsByCondition = SarprasAsset::selectRaw('`condition`, count(*) as total')
            ->groupBy('condition')->pluck('total', 'condition');

        $recentMaintenances = SarprasMaintenance::with('asset', 'room')
            ->latest()->take(5)->get();

        $recentNeeds = SarprasNeed::latest()->take(5)->get();

        return view('admin.sarpras.dashboard', compact(
            'totalAssets', 'totalRooms', 'pendingNeeds', 'pendingMaintenances',
            'procurementsInProgress', 'assetsByCategory', 'assetsByCondition',
            'recentMaintenances', 'recentNeeds'
        ));
    }

    // ─── ASSETS ───────────────────────────────────────────────────────────

    public function assetsIndex(Request $request)
    {
        $query = SarprasAsset::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('inventory_code', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $assets = $query->latest()->paginate(20)->withQueryString();

        return view('admin.sarpras.assets.index', compact('assets'));
    }

    public function assetsCreate()
    {
        return view('admin.sarpras.assets.create');
    }

    public function assetsStore(Request $request)
    {
        $conditions = implode(',', array_keys(config('sarpras.asset_conditions')));

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'inventory_code' => 'nullable|string|max:50|unique:sarpras_assets,inventory_code',
            'category' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'condition' => 'required|in:' . $conditions,
            'procurement_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'source_fund' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('sarpras/assets', 'public');
        }

        $data['created_by'] = auth()->id();
        $data['quantity'] = $data['quantity'] ?? 1;

        SarprasAsset::create($data);

        return redirect()->route('admin.sarpras.assets.index')
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    public function assetsShow(SarprasAsset $asset)
    {
        $asset->load('maintenances', 'creator');
        return view('admin.sarpras.assets.show', compact('asset'));
    }

    public function assetsEdit(SarprasAsset $asset)
    {
        return view('admin.sarpras.assets.edit', compact('asset'));
    }

    public function assetsUpdate(Request $request, SarprasAsset $asset)
    {
        $conditions = implode(',', array_keys(config('sarpras.asset_conditions')));

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'inventory_code' => 'nullable|string|max:50|unique:sarpras_assets,inventory_code,' . $asset->id,
            'category' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'condition' => 'required|in:' . $conditions,
            'procurement_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'source_fund' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            if ($asset->photo_path) {
                Storage::disk('public')->delete($asset->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('sarpras/assets', 'public');
        }

        $data['quantity'] = $data['quantity'] ?? 1;

        $asset->update($data);

        return redirect()->route('admin.sarpras.assets.index')
            ->with('success', 'Aset berhasil diperbarui.');
    }

    public function assetsDestroy(SarprasAsset $asset)
    {
        if ($asset->photo_path) {
            Storage::disk('public')->delete($asset->photo_path);
        }
        $asset->delete();

        return redirect()->route('admin.sarpras.assets.index')
            ->with('success', 'Aset berhasil dihapus.');
    }

    // ─── ROOMS ────────────────────────────────────────────────────────────

    public function roomsIndex(Request $request)
    {
        $query = SarprasRoom::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('room_type')) {
            $query->where('room_type', $request->room_type);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        $rooms = $query->latest()->paginate(20)->withQueryString();

        return view('admin.sarpras.rooms.index', compact('rooms'));
    }

    public function roomsCreate()
    {
        return view('admin.sarpras.rooms.create');
    }

    public function roomsStore(Request $request)
    {
        $conditions = implode(',', array_keys(config('sarpras.room_conditions')));

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'room_type' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'person_in_charge' => 'nullable|string|max:255',
            'condition' => 'required|in:' . $conditions,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'needs_note' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('sarpras/rooms', 'public');
        }

        SarprasRoom::create($data);

        return redirect()->route('admin.sarpras.rooms.index')
            ->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function roomsEdit(SarprasRoom $room)
    {
        return view('admin.sarpras.rooms.edit', compact('room'));
    }

    public function roomsUpdate(Request $request, SarprasRoom $room)
    {
        $conditions = implode(',', array_keys(config('sarpras.room_conditions')));

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'room_type' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'person_in_charge' => 'nullable|string|max:255',
            'condition' => 'required|in:' . $conditions,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'needs_note' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            if ($room->photo_path) {
                Storage::disk('public')->delete($room->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('sarpras/rooms', 'public');
        }

        $room->update($data);

        return redirect()->route('admin.sarpras.rooms.index')
            ->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function roomsDestroy(SarprasRoom $room)
    {
        if ($room->photo_path) {
            Storage::disk('public')->delete($room->photo_path);
        }
        $room->delete();

        return redirect()->route('admin.sarpras.rooms.index')
            ->with('success', 'Ruangan berhasil dihapus.');
    }

    // ─── NEEDS ────────────────────────────────────────────────────────────

    public function needsIndex(Request $request)
    {
        $query = SarprasNeed::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $needs = $query->latest()->paginate(20)->withQueryString();

        return view('admin.sarpras.needs.index', compact('needs'));
    }

    public function needsCreate()
    {
        return view('admin.sarpras.needs.create');
    }

    public function needsStore(Request $request)
    {
        $priorities = implode(',', array_keys(config('sarpras.need_priorities')));
        $statuses = implode(',', array_keys(config('sarpras.need_statuses')));

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity_needed' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'estimated_cost' => 'nullable|numeric|min:0',
            'priority' => 'required|in:' . $priorities,
            'status' => 'nullable|in:' . $statuses,
            'description' => 'nullable|string',
        ]);

        SarprasNeed::create($data);

        return redirect()->route('admin.sarpras.needs.index')
            ->with('success', 'Kebutuhan berhasil ditambahkan.');
    }

    public function needsEdit(SarprasNeed $need)
    {
        return view('admin.sarpras.needs.edit', compact('need'));
    }

    public function needsUpdate(Request $request, SarprasNeed $need)
    {
        $priorities = implode(',', array_keys(config('sarpras.need_priorities')));
        $statuses = implode(',', array_keys(config('sarpras.need_statuses')));

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'quantity_needed' => 'nullable|integer|min:1',
            'unit' => 'nullable|string|max:50',
            'estimated_cost' => 'nullable|numeric|min:0',
            'priority' => 'required|in:' . $priorities,
            'status' => 'nullable|in:' . $statuses,
            'description' => 'nullable|string',
        ]);

        $need->update($data);

        return redirect()->route('admin.sarpras.needs.index')
            ->with('success', 'Kebutuhan berhasil diperbarui.');
    }

    public function needsDestroy(SarprasNeed $need)
    {
        $need->delete();

        return redirect()->route('admin.sarpras.needs.index')
            ->with('success', 'Kebutuhan berhasil dihapus.');
    }

    // ─── MAINTAINENCES ────────────────────────────────────────────────────

    public function maintenancesIndex(Request $request)
    {
        $query = SarprasMaintenance::with('asset', 'room');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $maintenances = $query->latest()->paginate(20)->withQueryString();

        return view('admin.sarpras.maintenances.index', compact('maintenances'));
    }

    public function maintenancesCreate()
    {
        $assets = SarprasAsset::pluck('name', 'id');
        $rooms = SarprasRoom::pluck('name', 'id');
        return view('admin.sarpras.maintenances.create', compact('assets', 'rooms'));
    }

    public function maintenancesStore(Request $request)
    {
        $statuses = implode(',', array_keys(config('sarpras.maintenance_statuses')));

        $data = $request->validate([
            'asset_id' => 'nullable|exists:sarpras_assets,id',
            'room_id' => 'nullable|exists:sarpras_rooms,id',
            'title' => 'required|string|max:255',
            'damage_description' => 'required|string',
            'reported_by' => 'nullable|string|max:255',
            'reported_at' => 'nullable|date',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:' . $statuses,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'follow_up_note' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('sarpras/maintenances', 'public');
        }

        SarprasMaintenance::create($data);

        return redirect()->route('admin.sarpras.maintenances.index')
            ->with('success', 'Laporan perbaikan berhasil ditambahkan.');
    }

    public function maintenancesEdit(SarprasMaintenance $maintenance)
    {
        $assets = SarprasAsset::pluck('name', 'id');
        $rooms = SarprasRoom::pluck('name', 'id');
        return view('admin.sarpras.maintenances.edit', compact('maintenance', 'assets', 'rooms'));
    }

    public function maintenancesUpdate(Request $request, SarprasMaintenance $maintenance)
    {
        $statuses = implode(',', array_keys(config('sarpras.maintenance_statuses')));

        $data = $request->validate([
            'asset_id' => 'nullable|exists:sarpras_assets,id',
            'room_id' => 'nullable|exists:sarpras_rooms,id',
            'title' => 'required|string|max:255',
            'damage_description' => 'required|string',
            'reported_by' => 'nullable|string|max:255',
            'reported_at' => 'nullable|date',
            'estimated_cost' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:' . $statuses,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'follow_up_note' => 'nullable|string',
        ]);

        if ($request->hasFile('photo')) {
            if ($maintenance->photo_path) {
                Storage::disk('public')->delete($maintenance->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('sarpras/maintenances', 'public');
        }

        $maintenance->update($data);

        return redirect()->route('admin.sarpras.maintenances.index')
            ->with('success', 'Laporan perbaikan berhasil diperbarui.');
    }

    public function maintenancesDestroy(SarprasMaintenance $maintenance)
    {
        if ($maintenance->photo_path) {
            Storage::disk('public')->delete($maintenance->photo_path);
        }
        $maintenance->delete();

        return redirect()->route('admin.sarpras.maintenances.index')
            ->with('success', 'Laporan perbaikan berhasil dihapus.');
    }

    // ─── PROCUREMENTS ────────────────────────────────────────────────────

    public function procurementsIndex(Request $request)
    {
        $query = SarprasProcurement::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $procurements = $query->latest()->paginate(20)->withQueryString();

        return view('admin.sarpras.procurements.index', compact('procurements'));
    }

    public function procurementsCreate()
    {
        return view('admin.sarpras.procurements.create');
    }

    public function procurementsStore(Request $request)
    {
        $statuses = implode(',', array_keys(config('sarpras.procurement_statuses')));

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'procurement_date' => 'nullable|date',
            'source_fund' => 'nullable|string|max:255',
            'total_cost' => 'nullable|numeric|min:0',
            'vendor_name' => 'nullable|string|max:255',
            'receipt' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'nullable|in:' . $statuses,
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('sarpras/procurements', 'public');
        }

        SarprasProcurement::create($data);

        return redirect()->route('admin.sarpras.procurements.index')
            ->with('success', 'Pengadaan berhasil ditambahkan.');
    }

    public function procurementsEdit(SarprasProcurement $procurement)
    {
        return view('admin.sarpras.procurements.edit', compact('procurement'));
    }

    public function procurementsUpdate(Request $request, SarprasProcurement $procurement)
    {
        $statuses = implode(',', array_keys(config('sarpras.procurement_statuses')));

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'procurement_date' => 'nullable|date',
            'source_fund' => 'nullable|string|max:255',
            'total_cost' => 'nullable|numeric|min:0',
            'vendor_name' => 'nullable|string|max:255',
            'receipt' => 'nullable|image|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'nullable|in:' . $statuses,
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt')) {
            if ($procurement->receipt_path) {
                Storage::disk('public')->delete($procurement->receipt_path);
            }
            $data['receipt_path'] = $request->file('receipt')->store('sarpras/procurements', 'public');
        }

        $procurement->update($data);

        return redirect()->route('admin.sarpras.procurements.index')
            ->with('success', 'Pengadaan berhasil diperbarui.');
    }

    public function procurementsDestroy(SarprasProcurement $procurement)
    {
        if ($procurement->receipt_path) {
            Storage::disk('public')->delete($procurement->receipt_path);
        }
        $procurement->delete();

        return redirect()->route('admin.sarpras.procurements.index')
            ->with('success', 'Pengadaan berhasil dihapus.');
    }

    // ─── LAPORAN ───────────────────────────────────────────────────────────

    public function laporan()
    {
        $totalAssets = SarprasAsset::count();
        $totalRooms = SarprasRoom::count();
        $totalNeeds = SarprasNeed::count();
        $totalMaintenances = SarprasMaintenance::count();
        $totalProcurements = SarprasProcurement::count();

        $assetsByCondition = SarprasAsset::selectRaw('`condition`, count(*) as total')
            ->groupBy('condition')->pluck('total', 'condition');

        $assetsByCategory = SarprasAsset::selectRaw('category, count(*) as total')
            ->groupBy('category')->pluck('total', 'category');

        $roomsByCondition = SarprasRoom::selectRaw('`condition`, count(*) as total')
            ->groupBy('condition')->pluck('total', 'condition');

        $needsByStatus = SarprasNeed::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $maintenancesByStatus = SarprasMaintenance::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $procurementsByStatus = SarprasProcurement::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $recentProcurements = SarprasProcurement::latest()->take(10)->get();
        $recentMaintenances = SarprasMaintenance::with('asset')->latest()->take(10)->get();

        return view('admin.sarpras.laporan', compact(
            'totalAssets', 'totalRooms', 'totalNeeds', 'totalMaintenances', 'totalProcurements',
            'assetsByCondition', 'assetsByCategory', 'roomsByCondition',
            'needsByStatus', 'maintenancesByStatus', 'procurementsByStatus',
            'recentProcurements', 'recentMaintenances'
        ));
    }
}
