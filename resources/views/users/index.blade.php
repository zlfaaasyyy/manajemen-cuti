<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('users.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Tambah User
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full border-collapse border border-gray-200 text-left">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="border px-4 py-2">Nama</th>
                            <th class="border px-4 py-2">Role</th>
                            <th class="border px-4 py-2">Divisi</th>
                            <th class="border px-4 py-2">Kuota Cuti</th>
                            <th class="border px-4 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="border px-4 py-2">
                                {{ $user->name }} <br>
                                <span class="text-xs text-gray-500">{{ $user->email }}</span>
                            </td>
                            <td class="border px-4 py-2 capitalize">{{ str_replace('_', ' ', $user->role) }}</td>
                            <td class="border px-4 py-2">{{ $user->divisi ? $user->divisi->nama : '-' }}</td>
                            <td class="border px-4 py-2">{{ $user->kuota_cuti }} Hari</td>
                            <td class="border px-4 py-2 text-center space-x-2">
                                <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-500 hover:underline">Edit</a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>