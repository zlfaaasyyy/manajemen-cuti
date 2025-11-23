<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Divisi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- TOOLBAR: SEARCH & SORT & ADD -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                    
                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('divisi.index') }}" class="flex w-full md:w-auto space-x-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari divisi / ketua..." class="border rounded px-3 py-2 text-sm w-full md:w-64 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                        
                        <select name="sort" class="border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600" onchange="this.form.submit()">
                            <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                            <option value="anggota_banyak" {{ request('sort') == 'anggota_banyak' ? 'selected' : '' }}>Anggota Terbanyak</option>
                            <option value="anggota_sedikit" {{ request('sort') == 'anggota_sedikit' ? 'selected' : '' }}>Anggota Sedikit</option>
                            <option value="terbaru" {{ request('sort') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                        </select>
                        
                        <button type="submit" class="bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </button>
                    </form>

                    <!-- Tombol Tambah -->
                    <a href="{{ route('divisi.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Divisi
                    </a>
                </div>

                <!-- TABEL DATA -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Divisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ketua Divisi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Anggota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($divisi as $d)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $d->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $d->ketuaDivisi->name ?? 'Belum Ada' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $d->users_count }} Orang
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <!-- Detail -->
                                    <a href="{{ route('divisi.show', $d->id) }}" class="text-green-600 hover:text-green-900 font-bold" title="Lihat Anggota">
                                        Detail
                                    </a>
                                    
                                    <!-- Edit -->
                                    <a href="{{ route('divisi.edit', $d->id) }}" class="text-blue-600 hover:text-blue-900 font-bold">Edit</a>
                                    
                                    <!-- Hapus (Trigger Modal) -->
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-divisi-{{ $d->id }}')" class="text-red-600 hover:text-red-900 font-bold">
                                        Hapus
                                    </button>
                                </td>
                            </tr>

                            <!-- MODAL KONFIRMASI HAPUS (Double Confirmation) -->
                            <x-modal name="delete-divisi-{{ $d->id }}" focusable>
                                <form method="POST" action="{{ route('divisi.destroy', $d->id) }}" class="p-6">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Hapus Divisi {{ $d->nama }}?
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        Peringatan: Menghapus divisi ini akan membuat semua anggotanya kehilangan status divisi (menjadi null). Aksi ini tidak dapat dibatalkan.
                                    </p>
                                    
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                        <x-danger-button class="ms-3">Ya, Hapus Divisi</x-danger-button>
                                    </div>
                                </form>
                            </x-modal>

                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada divisi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>