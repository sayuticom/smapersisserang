<x-admin-layout>
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">&larr; Kembali</a>
            <h2 class="text-2xl font-bold text-gray-900 mt-2">Edit User</h2>
        </div>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-5">
            @csrf @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="password" id="password"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" minlength="8">
                <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti password.</p>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Level Akses</label>
                @if(Auth::id() === $user->id)
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <div class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                        {{ $user->isSuperadmin() ? 'Superadmin' : 'Admin' }}
                        <span class="text-xs text-gray-400 ml-2">(Tidak dapat mengubah level diri sendiri)</span>
                    </div>
                @else
                    <select name="role" id="role"
                            class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    </select>
                @endif
                @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Batal</a>
                <button type="submit"
                        class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
