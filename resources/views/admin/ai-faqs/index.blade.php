<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">FAQ AI</h2>
                <p class="text-sm text-gray-500 mt-1">Kelola pertanyaan dan jawaban untuk chatbot Asisten SPMB</p>
            </div>
            <a href="{{ route('admin.ai-faqs.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah FAQ AI
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.ai-faqs.index') }}" class="mb-4">
            <div class="flex flex-col md:flex-row gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari pertanyaan, jawaban, kata kunci..."
                    class="w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 focus:outline-none"
                >
                <select name="category" class="rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600 text-sm">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-lg bg-green-700 text-white text-sm font-medium hover:bg-green-800 transition-colors">
                    Cari
                </button>
                @if(request('search') || request('category') || request('status'))
                    <a href="{{ route('admin.ai-faqs.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition-colors text-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        @if(request('search'))
            <div class="text-sm text-gray-600 mb-4">
                Hasil pencarian untuk: <strong>"{{ request('search') }}"</strong>
                — ditemukan {{ $faqs->total() }} hasil
            </div>
        @endif

        @if($faqs->count())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 w-12">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Pertanyaan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kata Kunci</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kategori</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 w-20">Urutan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Diperbarui</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($faqs as $i => $faq)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-500">{{ $faqs->firstItem() + $i }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900 max-w-xs truncate">{{ $faq->question }}</td>
                                    <td class="px-4 py-3 text-gray-500 max-w-[120px] truncate text-xs">{{ $faq->keywords ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if($faq->category)
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $faq->category }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($faq->is_active)
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                                        @else
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $faq->sort_order }}</td>
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $faq->updated_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.ai-faqs.edit', $faq) }}"
                                               class="px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.ai-faqs.destroy', $faq) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus FAQ ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="px-4">
                {{ $faqs->withQueryString()->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center">
                @if(request('search'))
                    <p class="text-gray-400 font-medium">Tidak ditemukan FAQ dengan kata kunci "<strong>{{ request('search') }}</strong>".</p>
                    <a href="{{ route('admin.ai-faqs.index') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Reset Pencarian</a>
                @else
                    <p class="text-gray-400 font-medium">Belum ada FAQ AI.</p>
                    <a href="{{ route('admin.ai-faqs.create') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Tambah FAQ AI</a>
                @endif
            </div>
        @endif
    </div>
</x-admin-layout>
