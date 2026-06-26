@extends('layouts.public')

@section('content')
<div class="bg-white">
    <div class="relative bg-gradient-to-br from-emerald-700 via-emerald-600 to-emerald-800">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-40"></div>
        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:py-24 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">FAQ SPMB</h1>
            <p class="text-emerald-100 text-sm sm:text-base max-w-xl mx-auto">Pertanyaan yang sering diajukan seputar Sistem Penerimaan Murid Baru</p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 py-12 sm:py-16">
        @if($faqs->count())
            <div class="space-y-3">
                @foreach($faqs as $faq)
                    <div x-data="{ open: false }" class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left bg-white hover:bg-gray-50 transition-colors">
                            <span class="text-sm font-semibold text-gray-800 pr-4">{{ $faq->question }}</span>
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="border-t border-gray-100">
                            <div class="px-5 py-4 bg-gray-50/50">
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $faq->answer }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 rounded-2xl border border-gray-200 p-12 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <p class="text-gray-400 font-medium">FAQ belum tersedia.</p>
                <p class="text-gray-300 text-sm mt-1">Silakan hubungi kami jika ada pertanyaan.</p>
            </div>
        @endif

        <div class="mt-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-8 text-center border border-emerald-200">
            <h3 class="text-lg font-bold text-emerald-800 mb-2">Masih punya pertanyaan?</h3>
            <p class="text-sm text-emerald-600 mb-6">Hubungi tim SPMB kami untuk informasi lebih lanjut</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('spmb.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                    Daftar SPMB
                </a>
                <a href="{{ route('spmb.status.form') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-white text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-50 transition-colors border border-emerald-200">
                    Cek Status
                </a>
                <a href="https://wa.me/62{{ preg_replace('/^0/', '', $schoolSetting->phone ?? '') }}"
                   target="_blank"
                   class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-emerald-100 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-200 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Konsultasi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection