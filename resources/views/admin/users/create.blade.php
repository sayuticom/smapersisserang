<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Tambah User</h2>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" autocomplete="off" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       autocomplete="off"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                       autocomplete="new-email"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password"
                       autocomplete="new-password"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required minlength="8">
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       autocomplete="new-password"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Level Akses <span class="text-red-500">*</span></label>
                <div class="space-y-2">
                    @foreach($roles as $role)
                        @if($role->name === 'superadmin' && !auth()->user()->isSuperadmin())
                            @continue
                        @endif
                        <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors
                            {{ in_array($role->id, old('roles', [])) ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300' }}">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                   {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm font-medium text-gray-700">{{ $role->display_name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                @error('roles.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</a>
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
