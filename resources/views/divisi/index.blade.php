<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Manajemen Divisi') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <!-- Container Lebar -->
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

            <!-- MAIN CARD: Tabel Data -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <!-- TOOLBAR: Tombol Tambah -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-extrabold text-xl text-stone-800">Daftar Divisi Aktif</h3>
                    <a href="{{ route('divisi.create') }}" 
                        class="px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition flex items-center hover:opacity-90" 
                        style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7); border: none; font-size: 15px;">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Divisi
                    </a>
                </div>

                <!-- TABEL DATA -->
                <div class="overflow-x-auto">
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
                                    <!-- Tombol Detail (Orange) -->
                                    <a href="{{ route('divisi.show', $divisi->id) }}" class="text-amber-600 hover:text-amber-800 font-bold">Detail</a>
                                    
                                    <!-- Tombol Edit (Biru) -->
                                    <a href="{{ route('divisi.edit', $divisi->id) }}" class="text-blue-600 hover:text-blue-800 font-bold">Edit</a>
                                    
                                    <!-- Tombol Hapus (Merah) -->
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-divisi-{{ $divisi->id }}')" class="text-red-600 hover:text-red-800 font-bold">
                                        Hapus
                                    </button>
                                </td>
                            </tr>

                            <!-- MODAL KONFIRMASI HAPUS -->
                            <x-modal name="delete-divisi-{{ $divisi->id }}" focusable>
                                <form method="POST" action="{{ route('divisi.destroy', $divisi->id) }}" class="p-6">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <h2 class="text-lg font-medium text-gray-900">
                                        Hapus Divisi {{ $divisi->nama }}?
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Peringatan: Menghapus divisi ini akan menghapus semua anggotanya, dan menonaktifkan akun mereka. Tindakan ini tidak dapat dibatalkan.
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