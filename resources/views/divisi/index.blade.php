<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Divisi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4 flex justify-end">
                <a href="{{ route('divisi.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Tambah Divisi
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <table class="min-w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700">
                                <th class="border border-gray-300 px-4 py-2 text-left">Nama Divisi</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Ketua Divisi</th>
                                <th class="border border-gray-300 px-4 py-2 text-left">Deskripsi</th>
                                <th class="border border-gray-300 px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($divisis as $divisi)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $divisi->nama }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    {{ $divisi->ketua ? $divisi->ketua->name : '-' }}
                                </td>
                                <td class="border border-gray-300 px-4 py-2">{{ $divisi->deskripsi }}</td>
                                <td class="border border-gray-300 px-4 py-2 text-center">
                                    <form action="{{ route('divisi.destroy', $divisi->id) }}" method="POST" onsubmit="return confirm('Yakin hapus divisi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>