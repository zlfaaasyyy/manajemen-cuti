<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Manajemen Divisi') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <div class="flex flex-wrap justify-between items-center mb-6">
                    <h3 class="font-extrabold text-xl text-stone-800 mb-4 sm:mb-0">Daftar Divisi Aktif</h3>
                    <a href="{{ route('divisi.create') }}" 
                        class="px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition flex items-center hover:opacity-90" 
                        style="background-color: #FDA769; box-shadow: 0 4px 6px rgba(253, 167, 105, 0.4);">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Divisi
                    </a>
                </div>

                <div class="mb-8 p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <form method="GET" action="{{ route('divisi.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        
                        <div>
                            <label for="search" class="block text-xs font-medium text-gray-700 mb-1">Cari Divisi / Ketua</label>
                            <input type="text" name="search" id="search" value="{{ $request->search }}" placeholder="Nama Divisi atau Ketua..."
                                class="w-full border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>

                        <div>
                            <label for="sort" class="block text-xs font-medium text-gray-700 mb-1">Sortir Berdasarkan</label>
                            <select name="sort" id="sort" class="w-full border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-amber-500">
                                <option value="nama_asc" {{ $request->sort == 'nama_asc' ? 'selected' : '' }}>Nama Divisi (A-Z)</option>
                                <option value="nama_desc" {{ $request->sort == 'nama_desc' ? 'selected' : '' }}>Nama Divisi (Z-A)</option>
                                <option value="anggota_banyak" {{ $request->sort == 'anggota_banyak' ? 'selected' : '' }}>Jumlah Anggota (Terbanyak)</option>
                                <option value="anggota_sedikit" {{ $request->sort == 'anggota_sedikit' ? 'selected' : '' }}>Jumlah Anggota (Tersedikit)</option>
                                <option value="terbaru" {{ $request->sort == 'terbaru' ? 'selected' : '' }}>Tanggal Dibentuk (Terbaru)</option>
                                <option value="terlama" {{ $request->sort == 'terlama' ? 'selected' : '' }}>Tanggal Dibentuk (Terlama)</option>
                            </select>
                        </div>

                        <div class="flex space-x-2">
                            <button type="submit" class="px-4 py-2 bg-amber-500 text-white font-semibold text-sm rounded-lg hover:bg-amber-600 transition w-full">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Filter
                            </button>
                            <a href="{{ route('divisi.index') }}" class="px-4 py-2 bg-white text-gray-700 font-semibold text-sm rounded-lg border border-gray-300 hover:bg-gray-100 transition w-full text-center">
                                Reset
                            </a>
                        </div>

                    </form>
                </div>
                <div class="overflow-hidden rounded-[24px] border border-gray-100 shadow-sm">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead style="background-color: #F8F8F8;">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Divisi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Ketua Divisi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total Anggota</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            {{-- PERBAIKAN: Menggunakan $divisis dari controller --}}
                            @forelse($divisis as $divisi)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-stone-800">
                                    <a href="{{ route('divisi.show', $divisi->id) }}" class="hover:text-amber-600 transition">
                                        {{ $divisi->nama }}
                                    </a>
                                    <p class="text-xs text-gray-500 mt-1">Dibuat: {{ $divisi->created_at->format('d M Y') }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{-- PERBAIKAN: Menggunakan relasi ketuaDivisi --}}
                                    {{ $divisi->ketuaDivisi->name ?? 'Belum Ditunjuk' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-800">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700">
                                        {{ $divisi->users_count }} Anggota
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <a href="{{ route('divisi.show', $divisi->id) }}" class="text-amber-600 hover:text-amber-800 font-bold">Detail</a>
                                    
                                    <a href="{{ route('divisi.edit', $divisi->id) }}" class="text-blue-600 hover:text-blue-800 font-bold">Edit</a>
                                    
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-divisi-{{ $divisi->id }}')" class="text-red-600 hover:text-red-800 font-bold">
                                        Hapus
                                    </button>
                                </td>
                            </tr>

                            <x-modal name="delete-divisi-{{ $divisi->id }}" focusable>
                                <form method="POST" action="{{ route('divisi.destroy', $divisi->id) }}" class="p-6">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <h2 class="text-lg font-medium text-red-600">
                                        Hapus Divisi {{ $divisi->nama }}?
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Peringatan: Menghapus divisi ini akan **MENGELUARKAN** semua {{ $divisi->users_count }} anggotanya. Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                    
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                        <x-danger-button class="ms-3">Ya, Hapus Divisi</x-danger-button>
                                    </div>
                                </form>
                            </x-modal>

                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data divisi yang ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>