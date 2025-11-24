<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Verifikasi Final (HRD)') }}
        </h2>
    </x-slot>

    <!-- CONTAINER UTAMA: Menginisialisasi state Alpine.js -->
    <div class="py-12" x-data="{ selectedIds: [], selectAll: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Notifikasi Sukses/Error -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 font-bold rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 font-bold rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- TOOLBAR BULK ACTION (Muncul jika ada item yang dipilih) -->
            <div x-show="selectedIds.length > 0" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full"
                 class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] border-t dark:border-gray-700 p-4 z-50 flex justify-between items-center px-4 md:px-24">
                
                <div class="text-gray-800 dark:text-white font-bold text-lg flex items-center">
                    <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full mr-2" x-text="selectedIds.length"></span>
                    Pengajuan Dipilih
                </div>
                
                <div class="space-x-3 flex">
                    <!-- Tombol Bulk Reject -->
                    <button x-on:click="$dispatch('open-modal', 'bulk-reject-modal')" 
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 md:px-6 rounded shadow flex items-center transition">
                        <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span class="hidden md:inline">Tolak Terpilih</span>
                    </button>
                    
                    <!-- Tombol Bulk Approve -->
                    <button x-on:click="$dispatch('open-modal', 'bulk-approve-modal')" 
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 md:px-6 rounded shadow flex items-center transition">
                        <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="hidden md:inline">Approve Terpilih</span>
                    </button>
                </div>
            </div>

            <!-- HEADER LIST & CHECKBOX SELECT ALL -->
            <div class="flex flex-col md:flex-row justify-between items-end mb-6 space-y-4 md:space-y-0">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Menunggu Persetujuan Final: <span class="font-bold text-pink-600">{{ $pendingRequests->count() }} Pengajuan</span>
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Silakan review pengajuan di bawah ini sebelum memberikan keputusan final.</p>
                </div>
                
                <!-- Tombol Select All -->
                @if($pendingRequests->count() > 0)
                <div>
                    <label class="inline-flex items-center space-x-2 cursor-pointer bg-white dark:bg-gray-700 px-4 py-2 rounded shadow border dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition select-none">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 w-5 h-5"
                            x-model="selectAll"
                            @change="selectedIds = selectAll ? [{{ $pendingRequests->pluck('id')->implode(',') }}] : []">
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Pilih Semua</span>
                    </label>
                </div>
                @endif
            </div>

            <!-- GRID KARTU PENGAJUAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-24">
                @forelse($pendingRequests as $request)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 flex flex-col relative transition duration-200 group hover:shadow-xl"
                     :class="{ 'ring-2 ring-blue-500 bg-blue-50 dark:bg-gray-700 scale-[1.01]': selectedIds.includes({{ $request->id }}) }">
                    
                    <!-- CHECKBOX INDIVIDU (Pojok Kiri Atas) -->
                    <div class="absolute top-3 left-3 z-10">
                        <input type="checkbox" value="{{ $request->id }}" x-model="selectedIds"
                            class="w-6 h-6 rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 cursor-pointer bg-white/90 backdrop-blur">
                    </div>

                    <!-- Label Asal Pengajuan (Pojok Kanan Atas) -->
                    <div class="absolute top-3 right-3">
                        @if($request->status == 'approved_leader')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded-full border border-yellow-200 shadow-sm" title="Sudah disetujui Ketua Divisi">
                                ✓ Via Leader
                            </span>
                        @else
                            <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2 py-1 rounded-full border border-purple-200 shadow-sm" title="Langsung ke HRD (Ketua Divisi)">
                                ⚡ Direct
                            </span>
                        @endif
                    </div>

                    <!-- INFO USER -->
                    <div class="p-5 flex items-center space-x-4 bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600 mt-8">
                        @if($request->user->foto_profil)
                            <img class="h-12 w-12 rounded-full object-cover border-2 border-white shadow" src="{{ Storage::url($request->user->foto_profil) }}" alt="{{ $request->user->name }}">
                        @else
                            <div class="h-12 w-12 rounded-full bg-pink-500 flex items-center justify-center text-white font-bold text-lg border-2 border-white shadow">
                                {{ substr($request->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="overflow-hidden">
                            <h4 class="text-md font-bold text-gray-900 dark:text-white truncate">{{ $request->user->name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ $request->user->role === 'ketua_divisi' ? 'Ketua Divisi' : 'Staff' }} 
                                ({{ $request->user->divisi->nama ?? '-' }})
                            </p>
                        </div>
                    </div>

                    <!-- INFO CUTI -->
                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-center mb-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full border 
                                {{ $request->jenis_cuti == 'sakit' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                                {{ ucfirst($request->jenis_cuti) }}
                            </span>
                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $request->total_hari }} Hari</span>
                        </div>
                        
                        <div class="mb-4 text-sm text-gray-600 dark:text-gray-300 space-y-1">
                            <p class="flex justify-between">
                                <span>Mulai:</span> 
                                <span class="font-medium">{{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M Y') }}</span>
                            </p>
                            <p class="flex justify-between">
                                <span>Selesai:</span> 
                                <span class="font-medium">{{ \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y') }}</span>
                            </p>
                        </div>

                        <!-- ALASAN -->
                        <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg text-sm text-gray-600 dark:text-gray-400 italic mb-3 border dark:border-gray-700 relative">
                            <span class="absolute top-1 left-2 text-gray-300 text-2xl font-serif">"</span>
                            <p class="px-2 z-10 relative">{{ Str::limit($request->alasan, 80) }}</p>
                        </div>

                        <!-- CATATAN LEADER (Jika Ada) -->
                        @if($request->catatan_leader)
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-2 rounded border border-yellow-200 dark:border-yellow-800 mb-2">
                                <p class="text-xs font-bold text-yellow-700 dark:text-yellow-400 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                    Note Ketua Divisi:
                                </p>
                                <p class="text-xs text-yellow-600 dark:text-yellow-300 mt-1">{{ $request->catatan_leader }}</p>
                            </div>
                        @endif
                        
                        <!-- Link Detail -->
                        <div class="text-right mt-3">
                             <a href="{{ route('leaves.show', $request->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline flex items-center justify-end">
                                Detail Lengkap 
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                             </a>
                        </div>
                    </div>

                    <!-- TOMBOL AKSI INDIVIDU -->
                    <div class="p-4 border-t dark:border-gray-600 bg-gray-50 dark:bg-gray-800 grid grid-cols-2 gap-3">
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $request->id }}')" 
                            class="w-full py-2 px-4 bg-white border border-red-500 text-red-500 rounded-lg hover:bg-red-50 transition text-sm font-bold flex justify-center items-center">
                            Tolak
                        </button>

                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'approve-modal-{{ $request->id }}')" 
                            class="w-full py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-bold shadow-md flex justify-center items-center">
                            Setujui
                        </button>
                    </div>
                </div>

                <!-- ================= MODAL INDIVIDU APPROVE ================= -->
                <x-modal name="approve-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('hrd.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center">
                            <span class="bg-green-100 text-green-600 p-2 rounded-full mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                            Setujui Final Pengajuan?
                        </h2>
                        
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Pengajuan atas nama <strong>{{ $request->user->name }}</strong> akan disetujui secara final. Surat izin cuti akan otomatis digenerate dan kuota akan terpotong (jika cuti tahunan).
                        </p>
                        
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            <x-primary-button class="ms-3 bg-green-600 hover:bg-green-700">Ya, Final Approve</x-primary-button>
                        </div>
                    </form>
                </x-modal>

                <!-- ================= MODAL INDIVIDU REJECT ================= -->
                <x-modal name="reject-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('hrd.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        
                        <h2 class="text-lg font-medium text-red-600 flex items-center">
                            <span class="bg-red-100 text-red-600 p-2 rounded-full mr-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </span>
                            Tolak Pengajuan?
                        </h2>
                        
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Pengajuan <strong>{{ $request->user->name }}</strong> akan ditolak dan kuota akan dikembalikan.
                        </p>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Alasan Penolakan <span class="text-red-500">*</span> (Min. 10 Karakter)
                            </label>
                            <textarea name="catatan" class="w-full border-gray-300 rounded-md shadow-sm mt-1 dark:bg-gray-900 dark:text-white focus:border-red-500 focus:ring-red-500" 
                                      rows="3" required minlength="10" placeholder="Contoh: Kuota cuti bersamaan sudah penuh..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Wajib diisi agar karyawan tahu alasannya.</p>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            <x-danger-button class="ms-3">Tolak Pengajuan</x-danger-button>
                        </div>
                    </form>
                </x-modal>

                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                        <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak Ada Pengajuan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Saat ini tidak ada pengajuan cuti yang menunggu verifikasi HRD.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- ================= MODAL BULK APPROVE ================= -->
        <x-modal name="bulk-approve-modal" focusable>
            <form method="POST" action="{{ route('hrd.leaves.bulk_action') }}" class="p-6">
                @csrf
                <!-- Hidden Input untuk mengirim ID yang dipilih -->
                <input type="hidden" name="ids" :value="selectedIds.join(',')">
                <input type="hidden" name="bulk_action" value="approve">
                
                <h2 class="text-lg font-medium text-green-700 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Konfirmasi Approve Masal
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Anda akan menyetujui <span x-text="selectedIds.length" class="font-bold text-gray-900 dark:text-white text-lg"></span> pengajuan yang dipilih secara sekaligus.
                </p>
                
                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                    <x-primary-button class="ms-3 bg-green-600 hover:bg-green-700">Ya, Approve Semua</x-primary-button>
                </div>
            </form>
        </x-modal>

        <!-- ================= MODAL BULK REJECT ================= -->
        <x-modal name="bulk-reject-modal" focusable>
            <form method="POST" action="{{ route('hrd.leaves.bulk_action') }}" class="p-6">
                @csrf
                <input type="hidden" name="ids" :value="selectedIds.join(',')">
                <input type="hidden" name="bulk_action" value="reject">
                
                <h2 class="text-lg font-medium text-red-600 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Konfirmasi Tolak Masal
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Anda akan menolak <span x-text="selectedIds.length" class="font-bold text-gray-900 dark:text-white text-lg"></span> pengajuan yang dipilih.
                </p>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Catatan Penolakan Masal <span class="text-red-500">*</span> (Min. 10 Karakter)
                    </label>
                    <textarea name="bulk_catatan" class="w-full border-gray-300 rounded-md shadow-sm mt-1 dark:bg-gray-900 dark:text-white focus:border-red-500 focus:ring-red-500" 
                              rows="3" required minlength="10" placeholder="Berikan alasan yang sama untuk semua penolakan ini..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Catatan ini akan disimpan di semua pengajuan yang ditolak.</p>
                </div>

                <div class="mt-6 flex justify-end">
                    <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                    <x-danger-button class="ms-3">Ya, Reject Semua</x-danger-button>
                </div>
            </form>
        </x-modal>

    </div>
</x-app-layout>