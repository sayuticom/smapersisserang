<x-admin-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.organization-structures.index') }}" class="text-sm text-[#0F6B3A] hover:underline">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit Struktur: {{ $organizationStructure->label }}</h2>
        </div>

        <form action="{{ route('admin.organization-structures.update', $organizationStructure) }}" method="POST"
              class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5">
            @csrf
            @method('PUT')

            @include('admin.organization-structures._form')

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-[#0F6B3A] text-white font-medium rounded-lg hover:bg-[#0A4F2B] transition-colors">
                    Simpan
                </button>
                <a href="{{ route('admin.organization-structures.index') }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
