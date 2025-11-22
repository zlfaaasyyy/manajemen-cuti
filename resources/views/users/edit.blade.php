<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Error Handling -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- NAMA -->
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2 text-gray-700" required>
                        </div>
                        
                        <!-- EMAIL -->
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2 text-gray-700" required>
                        </div>

                        <!-- ROLE -->
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Role</label>
                            <select name="role" class="w-full border rounded px-3 py-2 text-gray-700 capitalize">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- DIVISI (Bisa dikosongkan) -->
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Divisi</label>
                            <select name="divisi_id" class="w-full border rounded px-3 py-2 text-gray-700">
                                <!-- OPSI KOSONG (PENTING) -->
                                <option value="">-- Tidak Ada / Belum Ditentukan --</option>

                                <!-- Loop variabel $divisi (sesuai controller edit) -->
                                @foreach($divisi as $d)
                                    <option value="{{ $d->id }}" {{ old('divisi_id', $user->divisi_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- KUOTA CUTI -->
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Kuota Cuti</label>
                            <input type="number" name="kuota_cuti" value="{{ old('kuota_cuti', $user->kuota_cuti) }}" min="0" class="w-full border rounded px-3 py-2 text-gray-700" required>
                            <p class="text-xs text-gray-500 mt-1">Akan otomatis jadi 0 jika role diubah ke HRD/Admin.</p>
                        </div>
                        
                        <!-- GANTI PASSWORD (OPSIONAL) -->
                        <div class="col-span-2 border-t pt-4 mt-2">
                            <p class="text-sm font-bold text-gray-500 mb-2">Ganti Password (Isi jika ingin mengubah)</p>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs mb-1 dark:text-gray-300">Password Baru</label>
                                    <input type="password" name="password" class="w-full border rounded px-3 py-2 text-gray-700">
                                </div>
                                <div>
                                    <label class="block text-xs mb-1 dark:text-gray-300">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2 text-gray-700">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TOMBOL -->
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mr-2">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>