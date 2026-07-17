<x-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Kelola User</h2>
                <p class="text-sm text-gray-500 mt-1">Manajemen akun admin dashboard</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah User
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        @if($users->count())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 w-12">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Level Akses</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Dibuat</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($users as $i => $user)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($user->roles as $role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    {{ $role->name === 'superadmin' ? 'bg-amber-100 text-amber-800' : '' }}
                                                    {{ $role->name === 'admin' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                    {{ $role->name === 'guru' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $role->name === 'kepala_sekolah' ? 'bg-purple-100 text-purple-800' : '' }}
                                                    {{ $role->name === 'staf_tata_usaha' ? 'bg-slate-100 text-slate-800' : '' }}
                                                    {{ $role->name === 'staf_keuangan' ? 'bg-cyan-100 text-cyan-800' : '' }}
                                                    {{ $role->name === 'staf_kesiswaan' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                                    {{ $role->name === 'staf_sarpras' ? 'bg-rose-100 text-rose-800' : '' }}
                                                    {{ !in_array($role->name, ['superadmin','admin','guru','kepala_sekolah','staf_tata_usaha','staf_keuangan','staf_kesiswaan','staf_sarpras']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                                    {{ $role->display_name }}
                                                </span>
                                            @empty
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                                    {{ $user->role ? ucfirst($user->role) : 'Tanpa role' }}
                                                </span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.users.edit', $user) }}"
                                               class="px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                                Edit
                                            </a>
                                            @if($user->id === Auth::id())
                                                <span class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                                    Diri Sendiri
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center">
                <p class="text-gray-400 font-medium">Belum ada user.</p>
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 mt-4 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Tambah User</a>
            </div>
        @endif
    </div>
</x-admin-layout>
