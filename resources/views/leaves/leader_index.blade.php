<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Verifikasi Pengajuan (Leader)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Pesan Sukses -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Berhasil!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Header Statistik -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Menunggu Persetujuan: <span class="font-bold text-blue-600">{{ $pendingRequests->count() }} Pengajuan</span>
                </h3>
            </div>

            <!-- GRID CARD LAYOUT (Sesuai Soal) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($pendingRequests as $request)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col">
                    
                    <!-- Bagian Atas: Info User -->
                    <div class="p-5 flex items-center space-x-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                        <!-- Foto Profil (Jika ada fitur foto) -->
                        @if($request->user->foto_profil)
                            <img class="h-12 w-12 rounded-full object-cover" src="{{ Storage::url($request->user->foto_profil) }}" alt="{{ $request->user->name }}">
                        @else
                            <div class="h-12 w-12 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($request->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h4 class="text-md font-bold text-gray-900 dark:text-white">{{ $request->user->name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $request->user->divisi->nama ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Bagian Tengah: Detail Cuti -->
                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-center mb-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $request->jenis_cuti == 'sakit' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($request->jenis_cuti) }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $request->total_hari }} Hari</span>
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                            <span class="font-semibold">Tanggal:</span><br>
                            {{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y') }}
                        </p>

                        <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg text-sm text-gray-600 dark:text-gray-400 italic mb-3">
                            "{{ $request->alasan }}"
                        </div>

                        @if($request->jenis_cuti == 'sakit' && $request->bukti_sakit)
                            <a href="{{ Storage::url($request->bukti_sakit) }}" target="_blank" class="text-blue-600 hover:underline text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Lihat Surat Dokter
                            </a>
                        @endif
                    </div>

                    <!-- Bagian Bawah: Tombol Aksi (Modal Trigger) -->
                    <div class="p-4 border-t dark:border-gray-600 bg-gray-50 dark:bg-gray-800 grid grid-cols-2 gap-3">
                        <!-- Tombol Reject -->
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $request->id }}')" 
                            class="w-full py-2 px-4 bg-white border border-red-500 text-red-500 rounded-lg hover:bg-red-50 transition text-sm font-bold">
                            Tolak
                        </button>

                        <!-- Tombol Approve -->
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'approve-modal-{{ $request->id }}')" 
                            class="w-full py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-bold shadow-md">
                            Setujui
                        </button>
                    </div>
                </div>

                <!-- ================= MODAL APPROVE ================= -->
                <x-modal name="approve-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('leader.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Setujui Pengajuan {{ $request->user->name }}?
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Apakah Anda yakin ingin menyetujui pengajuan ini dan meneruskannya ke HRD?
                        </p>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan (Opsional)</label>
                            <textarea name="catatan" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300 mt-1" rows="2"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            <x-primary-button class="ms-3 bg-green-600 hover:bg-green-700">Ya, Setujui</x-primary-button>
                        </div>
                    </form>
                </x-modal>

                <!-- ================= MODAL REJECT ================= -->
                <x-modal name="reject-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('leader.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        
                        <h2 class="text-lg font-medium text-red-600">
                            Tolak Pengajuan {{ $request->user->name }}?
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Pengajuan akan dibatalkan dan kuota dikembalikan ke karyawan.
                        </p>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan Penolakan <span class="text-red-500">*</span></label>
                            <textarea name="catatan" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300 mt-1" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            <x-danger-button class="ms-3">Tolak Pengajuan</x-danger-button>
                        </div>
                    </form>
                </x-modal>

                @empty
                <div class="col-span-full text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Tidak ada pengajuan</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Saat ini belum ada pengajuan cuti dari bawahan Anda.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>