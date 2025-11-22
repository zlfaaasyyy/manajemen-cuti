<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah User Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- NAMA -->
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 dark:text-white">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2 text-gray-700" required>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 dark:text-white">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2 text-gray-700" required>
                        </div>

                        <!-- ROLE -->
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 dark:text-white">Role</label>
                            <select name="role" class="w-full border rounded px-3 py-2 text-gray-700 capitalize">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- DIVISI (OPSIONAL) -->
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 dark:text-white">Divisi (Opsional)</label>
                            <select name="divisi_id" class="w-full border rounded px-3 py-2 text-gray-700">
                                <!-- OPSI KOSONG: Ini kuncinya -->
                                <option value="">-- Tidak Ada / Belum Ditentukan --</option>
                                
                                @foreach($divisis as $divisi)
                                    <option value="{{ $divisi->id }}" {{ old('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                        {{ $divisi->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Bisa dikosongkan jika divisi belum dibuat.</p>
                        </div>

                        <!-- PASSWORD -->
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 dark:text-white">Password</label>
                            <input type="password" name="password" class="w-full border rounded px-3 py-2 text-gray-700" required>
                        </div>

                        <!-- KONFIRMASI PASSWORD -->
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2 dark:text-white">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2 text-gray-700" required>
                        </div>

                        <!-- KUOTA CUTI -->
                        <div class="mb-4 col-span-2">
                            <label class="block text-sm font-bold mb-2 dark:text-white">Kuota Cuti Awal</label>
                            <input type="number" name="kuota_cuti" value="{{ old('kuota_cuti', 12) }}" class="w-full border rounded px-3 py-2 text-gray-700">
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mr-2">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan User</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>