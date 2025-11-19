<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tambah User Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Nama Lengkap</label>
                        <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Username</label>
                            <input type="text" name="username" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Email</label>
                            <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Role</label>
                        <select name="role" class="w-full border rounded px-3 py-2">
                            <option value="user">Karyawan (User)</option>
                            <option value="ketua_divisi">Ketua Divisi</option>
                            <option value="hrd">HRD</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Divisi</label>
                        <select name="divisi_id" class="w-full border rounded px-3 py-2">
                            <option value="">-- Tidak Ada Divisi --</option>
                            @foreach($divisis as $divisi)
                                <option value="{{ $divisi->id }}">{{ $divisi->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Password</label>
                        <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan User</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>