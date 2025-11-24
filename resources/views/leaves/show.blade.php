<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Pengajuan Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- STATUS HEADER -->
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $leaveRequest->user->name }}</h3>
                        <p class="text-sm text-gray-500">Diajukan pada: {{ $leaveRequest->created_at->format('d F Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-4 py-2 text-sm font-bold rounded-full 
                        {{ $leaveRequest->status == 'approved' ? 'bg-green-100 text-green-800' : 
                          ($leaveRequest->status == 'pending' ? 'bg-gray-100 text-gray-800' : 
                          ($leaveRequest->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                          ($leaveRequest->status == 'cancelled' ? 'bg-black text-white' : 'bg-yellow-100 text-yellow-800'))) }}">
                            {{ ucfirst(str_replace('_', ' ', $leaveRequest->status)) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- INFORMASI CUTI -->
                    <div>
                        <h4 class="font-bold text-gray-700 dark:text-gray-300 mb-3 border-b pb-1">Informasi Cuti</h4>
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li><span class="font-bold w-32 inline-block">Jenis Cuti:</span> {{ ucfirst($leaveRequest->jenis_cuti) }}</li>
                            <li><span class="font-bold w-32 inline-block">Tanggal:</span> {{ \Carbon\Carbon::parse($leaveRequest->tanggal_mulai)->format('d M') }} s/d {{ \Carbon\Carbon::parse($leaveRequest->tanggal_selesai)->format('d M Y') }}</li>
                            <li><span class="font-bold w-32 inline-block">Durasi:</span> {{ $leaveRequest->total_hari }} Hari Kerja</li>
                            <li><span class="font-bold w-32 inline-block">Alasan:</span> "{{ $leaveRequest->alasan }}"</li>
                            <li><span class="font-bold w-32 inline-block">Alamat Cuti:</span> {{ $leaveRequest->alamat_selama_cuti }}</li>
                            <li><span class="font-bold w-32 inline-block">No. Darurat:</span> {{ $leaveRequest->nomor_darurat }}</li>
                        </ul>

                        @if($leaveRequest->jenis_cuti == 'sakit' && $leaveRequest->bukti_sakit)
                            <div class="mt-4">
                                <p class="font-bold text-sm mb-2 text-gray-700 dark:text-gray-300">Bukti Surat Dokter:</p>
                                <a href="{{ Storage::url($leaveRequest->bukti_sakit) }}" target="_blank">
                                    <img src="{{ Storage::url($leaveRequest->bukti_sakit) }}" class="h-32 rounded border hover:opacity-75" alt="Surat Dokter">
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- TIMELINE & CATATAN -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="font-bold text-gray-700 dark:text-white mb-3 border-b pb-1">Timeline & Catatan</h4>
                        
                        <ol class="relative border-l border-gray-200 dark:border-gray-600 ml-2">                  
                            <!-- 1. Diajukan -->
                            <li class="mb-6 ml-4">
                                <div class="absolute w-3 h-3 bg-blue-600 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <time class="mb-1 text-xs font-normal text-gray-400">{{ $leaveRequest->created_at->format('d M Y H:i') }}</time>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pengajuan Dibuat</h3>
                            </li>

                            <!-- 2. Proses Ketua Divisi -->
                            @if($leaveRequest->status == 'approved_leader' || $leaveRequest->status == 'approved')
                            <li class="mb-6 ml-4">
                                <div class="absolute w-3 h-3 bg-green-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Disetujui Ketua Divisi</h3>
                                @if($leaveRequest->catatan_leader)
                                    <p class="text-xs italic text-gray-500">"{{ $leaveRequest->catatan_leader }}"</p>
                                @endif
                            </li>
                            @endif

                            <!-- 3. Final HRD -->
                            @if($leaveRequest->status == 'approved')
                            <li class="mb-6 ml-4">
                                <div class="absolute w-3 h-3 bg-green-600 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Disetujui HRD (Final)</h3>
                            </li>
                            @endif

                            <!-- 4. Penolakan -->
                            @if($leaveRequest->status == 'rejected')
                            <li class="mb-6 ml-4">
                                <div class="absolute w-3 h-3 bg-red-600 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <h3 class="text-sm font-semibold text-red-600">Pengajuan Ditolak</h3>
                                <p class="mb-4 text-sm font-normal text-gray-500 bg-white p-2 border rounded">
                                    {{ $leaveRequest->catatan_penolakan }}
                                </p>
                            </li>
                            @endif

                            <!-- 5. Pembatalan -->
                            @if($leaveRequest->status == 'cancelled')
                            <li class="mb-6 ml-4">
                                <div class="absolute w-3 h-3 bg-black rounded-full mt-1.5 -left-1.5 border border-white"></div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Dibatalkan User</h3>
                                <p class="mb-4 text-sm font-normal text-gray-500">
                                    Alasan: {{ $leaveRequest->alasan_pembatalan ?? '-' }}
                                </p>
                            </li>
                            @endif
                        </ol>
                    </div>
                </div>

                <!-- TOMBOL AKSI -->
                <div class="mt-8 border-t pt-4 flex justify-end space-x-3">
                    <a href="{{ route('leaves.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Kembali</a>

                    <!-- Tombol Download (Jika Approved) -->
                    @if($leaveRequest->status == 'approved')
                        <a href="{{ route('leaves.pdf', $leaveRequest->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold">Unduh Surat Izin (PDF)</a>
                    @endif

                    <!-- Tombol Batal (Jika Pending) -->
                    @if($leaveRequest->status == 'pending' && Auth::id() == $leaveRequest->user_id)
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'cancel-modal')" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold">
                            Batalkan Pengajuan
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL PEMBATALAN -->
    <x-modal name="cancel-modal" focusable>
        <form method="POST" action="{{ route('leaves.cancel', $leaveRequest->id) }}" class="p-6">
            @csrf
            @method('DELETE')
            
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Batalkan Pengajuan Cuti?
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Kuota cuti tahunan akan dikembalikan otomatis. Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alasan Pembatalan <span class="text-red-500">*</span></label>
                <textarea name="alasan_pembatalan" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-900 dark:text-gray-300 mt-1" rows="3" required placeholder="Contoh: Jadwal acara berubah"></textarea>
            </div>
            
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Tutup</x-secondary-button>
                <x-danger-button class="ms-3">Ya, Batalkan</x-danger-button>
            </div>
        </form>
    </x-modal>

</x-app-layout>