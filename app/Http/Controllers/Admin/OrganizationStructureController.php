<?php

namespace App\Http\Controllers\Admin;

use App\Models\OrganizationStructure;
use App\Models\WebsitePage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class OrganizationStructureController extends Controller
{
    public function index()
    {
        $structures = OrganizationStructure::orderBy('level')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
        $websitePage = WebsitePage::firstOrCreate(
            ['page_key' => 'struktur-organisasi'],
            [
                'title' => 'Struktur Organisasi',
                'subtitle' => 'Bagan Organisasi SMA Persis Serang',
                'content' => 'Struktur organisasi SMA Persis Serang disusun untuk mendukung pengelolaan sekolah berbasis pendidikan, pembinaan akhlak, dan sistem boarding school. Melalui pembagian tugas yang jelas, setiap bidang dapat bekerja secara tertib, terarah, dan bertanggung jawab.',
                'meta_description' => 'Struktur organisasi SMA Persis Serang.',
                'is_active' => true,
            ]
        );

        return view('admin.organization-structures.index', compact('structures', 'websitePage'));
    }

    public function updatePage(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
        ]);

        WebsitePage::updateOrCreate(
            ['page_key' => 'struktur-organisasi'],
            [
                'title' => $data['title'],
                'subtitle' => $data['subtitle'] ?? null,
                'content' => $data['content'] ?? null,
                'meta_description' => $data['content'] ?? null,
                'is_active' => true,
            ]
        );

        return redirect()->route('admin.organization-structures.index')
            ->with('success', 'Judul dan teks pengantar berhasil diperbarui.');
    }

    public function create()
    {
        $organizationStructure = new OrganizationStructure([
            'level' => 1,
            'sort_order' => 0,
            'is_active' => true,
        ]);
        $parentOptions = $this->parentOptions();
        $membersText = '';

        return view('admin.organization-structures.create', compact(
            'organizationStructure', 'parentOptions', 'membersText'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        OrganizationStructure::create($data);

        return redirect()->route('admin.organization-structures.index')
            ->with('success', 'Data struktur organisasi berhasil ditambahkan.');
    }

    public function edit(OrganizationStructure $organizationStructure)
    {
        $parentOptions = $this->parentOptions($organizationStructure);
        $membersText = implode(PHP_EOL, $organizationStructure->members ?? []);

        return view('admin.organization-structures.edit', compact(
            'organizationStructure', 'parentOptions', 'membersText'
        ));
    }

    public function update(Request $request, OrganizationStructure $organizationStructure)
    {
        $data = $this->validatedData($request, $organizationStructure);

        $organizationStructure->update($data);

        return redirect()->route('admin.organization-structures.index')
            ->with('success', 'Data struktur organisasi berhasil diperbarui.');
    }

    public function destroy(OrganizationStructure $organizationStructure)
    {
        $organizationStructure->delete();

        return redirect()->route('admin.organization-structures.index')
            ->with('success', 'Data struktur organisasi berhasil dihapus.');
    }

    private function validatedData(Request $request, ?OrganizationStructure $organizationStructure = null): array
    {
        $data = $request->validate([
            'structure_key' => [
                'required',
                'string',
                'max:100',
                Rule::unique('organization_structures', 'structure_key')->ignore($organizationStructure),
            ],
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'members_text' => 'nullable|string',
            'parent_key' => 'nullable|string|max:100',
            'sort_order' => 'required|integer|min:0',
            'level' => 'required|integer|min:1|max:10',
            'card_type' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $data['members'] = collect(preg_split('/\r\n|\r|\n/', $data['members_text'] ?? ''))
            ->map(fn($line) => trim($line))
            ->filter()
            ->values()
            ->all();
        $data['is_active'] = $request->boolean('is_active');

        unset($data['members_text']);

        return $data;
    }

    private function parentOptions(?OrganizationStructure $organizationStructure = null)
    {
        return OrganizationStructure::when($organizationStructure?->exists, function ($query) use ($organizationStructure) {
                $query->where('structure_key', '!=', $organizationStructure->structure_key);
            })
            ->orderBy('level')
            ->orderBy('sort_order')
            ->get();
    }
}
