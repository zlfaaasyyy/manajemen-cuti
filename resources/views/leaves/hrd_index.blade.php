<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Persetujuan Final Cuti (HRD)') }}
        </h2>
    </x-slot>

    <!-- CONTAINER UTAMA: Menginisialisasi state Alpine.js -->
    <div class="py-12" x-data="{ selectedIds: [], selectAll: false }" style="background-color: #F8F8F8;">
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Notifikasi Sukses/Error -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-sm flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <p class="font-bold">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-sm flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="font-bold">{{ session('error') }}</p>
                </div>
            @endif

            <!-- TOOLBAR BULK ACTION (FIXED BOTTOM) -->
            <div x-show="selectedIds.length > 0" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full"
                 class="fixed bottom-0 left-0 right-0 shadow-[0_-8px_15px_-3px_rgba(0,0,0,0.1)] border-t p-4 z-50 flex justify-between items-center px-4 md:px-24 rounded-t-[30px]" 
                 style="background-color: #473C33; border-top-color: #ABC270;">
                
                <div class="text-white font-extrabold text-lg flex items-center">
                    <span class="bg-white text-stone-800 text-xs px-2 py-1 rounded-full mr-2" x-text="selectedIds.length"></span>
                    Pengajuan Dipilih
                </div>
                
                <div class="space-x-3 flex">
                    <!-- Tombol Bulk Reject -->
                    <button x-on:click="$dispatch('open-modal', 'bulk-reject-modal')" 
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 md:px-6 rounded-xl shadow flex items-center transition" style="border-radius: 12px;">
                        <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span class="hidden md:inline">Tolak Massal</span>
                    </button>
                    
                    <!-- Tombol Bulk Approve (HIJAU LUMUT #ABC270) -->
                    <button x-on:click="$dispatch('open-modal', 'bulk-approve-modal')" 
                            class="text-white font-bold py-2 px-4 md:px-6 rounded-xl shadow flex items-center transition" style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7); border-radius: 12px;">
                        <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span class="hidden md:inline">Approve Massal</span>
                    </button>
                </div>
            </div>

            <!-- HEADER LIST & CHECKBOX SELECT ALL -->
            <div class="flex flex-col md:flex-row justify-between items-end mb-6 space-y-4 md:space-y-0">
                <div>
                    <h3 class="text-xl font-extrabold text-stone-800">
                        Menunggu Persetujuan Final: 
                        <span class="px-3 py-1 rounded-full text-lg font-bold" style="background-color: #FFFBE8; color: #FEC868; border: 1px solid #FEC868;">
                            {{ $pendingRequests->count() }} Pengajuan
                        </span>
                    </h3>
                    <p class="text-sm text-gray-500 mt-2">Silakan review pengajuan di bawah ini sebelum memberikan keputusan final.</p>
                </div>
                
                <!-- Tombol Select All -->
                @if($pendingRequests->count() > 0)
                <div>
                    <label class="inline-flex items-center space-x-2 cursor-pointer bg-white px-4 py-2 rounded-xl shadow border hover:bg-gray-50 transition select-none" style="border-radius: 12px;">
                        <input type="checkbox" class="rounded border-gray-300 text-stone-800 shadow-sm focus:ring-amber-500 w-5 h-5"
                            x-model="selectAll"
                            @change="selectedIds = selectAll ? [{{ $pendingRequests->pluck('id')->implode(',') }}] : []">
                        <span class="text-sm font-bold text-stone-800">Pilih Semua</span>
                    </label>
                </div>
                @endif
            </div>

            <!-- GRID KARTU PENGAJUAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pb-24">
                @forelse($pendingRequests as $request)
                <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] border border-gray-100 flex flex-col transition hover:shadow-3xl duration-300"
                     :class="{ 'ring-2 ring-amber-400 bg-amber-50 scale-[1.01]': selectedIds.includes({{ $request->id }}) }"
                     style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-radius: 24px;">
                    
                    <!-- CHECKBOX INDIVIDU (Pojok Kiri Atas) -->
                    <div class="absolute top-3 left-3 z-10">
                        <input type="checkbox" value="{{ $request->id }}" x-model="selectedIds"
                            class="w-6 h-6 rounded border-gray-300 text-amber-500 shadow-sm focus:ring-amber-500 cursor-pointer bg-white/90 backdrop-blur">
                    </div>

                    <!-- Label Asal Pengajuan (Pojok Kanan Atas) -->
                    <div class="absolute top-3 right-3">
                        @if($request->status == 'approved_leader')
                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200 shadow-sm" style="background-color: #FFFBE8;">
                                ✓ Via Leader
                            </span>
                        @else
                            <span class="bg-stone-100 text-stone-700 text-xs font-bold px-3 py-1 rounded-full border border-stone-200 shadow-sm" style="background-color: #F8F8F8;">
                                ⚡ Direct
                            </span>
                        @endif
                    </div>

                    <!-- INFO USER -->
                    <div class="p-5 flex items-center space-x-4 border-b mt-8" style="background-color: #F8F8F8; border-color: #F0F0F0;">
                        <div class="flex-shrink-0">
                            @if($request->user->foto_profil)
                                <img class="h-14 w-14 rounded-full object-cover border-2 border-white shadow-sm" src="{{ Storage::url($request->user->foto_profil) }}" alt="{{ $request->user->name }}">
                            @else
                                <div class="h-14 w-14 rounded-full flex items-center justify-center text-white font-bold text-xl border-2 border-white shadow-sm" style="background-color: #ABC270;">
                                    {{ substr($request->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="text-md font-extrabold text-stone-800 truncate" title="{{ $request->user->name }}">{{ $request->user->name }}</h4>
                            <p class="text-xs text-gray-500 truncate">{{ $request->user->divisi->nama ?? 'Tanpa Divisi' }}</p>
                        </div>
                    </div>

                    <!-- Bagian Tengah: Detail Cuti -->
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <!-- Jenis Cuti -->
                            <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full border 
                                {{ $request->jenis_cuti == 'sakit' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200' }}"
                                style="{{ $request->jenis_cuti == 'tahunan' ? 'background-color: #ECF2E1; color: #ABC270; border-color: #ABC270;' : '' }}">
                                {{ $request->jenis_cuti }}
                            </span>
                            <span class="text-sm font-extrabold text-stone-800">{{ $request->total_hari }} Hari Kerja</span>
                        </div>
                        
                        <div class="mb-4 text-sm text-gray-600 p-3 rounded-xl border border-amber-200" style="background-color: #FFFBE8;">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs text-gray-500">Periode:</span>
                                <span class="font-semibold text-amber-700">{{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y') }}</span>
                            </div>
                        </div>

                        <p class="text-xs font-bold text-gray-500 mb-1">Alasan Karyawan:</p>
                        <div class="text-sm text-stone-800 italic mb-4 flex-1">
                            "{{ Str::limit($request->alasan, 80) }}"
                        </div>
                        
                        <!-- INFORMASI TAMBAHAN ATASAN (ORANGE MUDA #FDA769) -->
                        @if ($request->status == 'approved_leader')
                        <div class="mt-2 p-3 rounded-lg text-xs" style="background-color: #FFF3E6; border: 1px solid #FDA769;">
                            <p class="font-bold mb-1 flex items-center" style="color: #FDA769;">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2-4v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                Approval Leader
                            </p>
                            <p class="text-gray-700">
                                Disetujui oleh: <span class="font-semibold">{{ $request->user->divisi->ketuaDivisi->name ?? 'N/A' }}</span>
                            </p>
                            <p class="text-gray-500">
                                Catatan: "{{ $request->catatan_leader ?? 'Tidak ada catatan.' }}"
                            </p>
                        </div>
                        @endif

                    </div>

                    <!-- Bagian Bawah: Tombol Aksi HRD -->
                    <div class="p-5 border-t" style="border-color: #F0F0F0; background-color: #F8F8F8;">
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Tombol Final Reject -->
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'hrd-reject-modal-{{ $request->id }}')" 
                                class="w-full py-3 px-4 bg-white border border-red-500 text-red-600 rounded-xl hover:bg-red-50 transition text-sm font-bold flex justify-center items-center group shadow-md" style="border-radius: 12px;">
                                Tolak Final
                            </button>

                            <!-- Tombol Final Approve (ORANGE TERAKOTA #FDA769) -->
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'hrd-approve-modal-{{ $request->id }}')" 
                                class="w-full py-3 px-4 text-white rounded-xl transition text-sm font-bold shadow-lg flex justify-center items-center group" style="background-color: #FDA769; box-shadow: 0 4px 8px -2px rgba(253, 167, 105, 0.7); border-radius: 12px;">
                                Approve Final
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ================= MODAL APPROVE FINAL ================= -->
                <x-modal name="hrd-approve-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('hrd.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        
                        <h2 class="text-xl font-bold text-stone-800 flex items-center">
                            <span class="p-2 rounded-full mr-3" style="background-color: #ABC270; color: white;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Persetujuan Cuti Final
                        </h2>
                        <p class="mt-3 text-sm text-gray-600">
                            Anda akan menyetujui pengajuan cuti <strong>{{ $request->user->name }}</strong>. Surat izin cuti dapat diunduh setelah ini.
                        </p>

                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl">Batal</button>
                            <button type="submit" class="ms-3 px-4 py-2 text-sm font-bold text-white rounded-xl transition" style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7);">Approve Final</button>
                        </div>
                    </form>
                </x-modal>

                <!-- ================= MODAL REJECT FINAL ================= -->
                <x-modal name="hrd-reject-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('hrd.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        
                        <h2 class="text-xl font-bold text-red-600 flex items-center">
                            <span class="bg-red-100 text-red-600 p-2 rounded-full mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </span>
                            Tolak Pengajuan Final
                        </h2>
                        <p class="mt-3 text-sm text-gray-600">
                            Pengajuan akan ditolak secara permanen. Kuota cuti (jika cuti tahunan) akan dikembalikan.
                        </p>

                        <div class="mt-4">
                            <label class="block text-sm font-bold text-gray-700">
                                Alasan Penolakan Final <span class="text-red-500">*</span> (Min. 10 Karakter)
                            </label>
                            <textarea name="catatan" class="w-full border-gray-300 rounded-xl shadow-sm mt-1 focus:ring-red-500 focus:border-red-500" 
                                      rows="3" required minlength="10" placeholder="Wajib diisi: Kenapa pengajuan ini ditolak oleh HRD?" style="border-radius: 12px;"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl">Batal</button>
                            <x-danger-button class="ms-3">Tolak Final</x-danger-button>
                        </div>
                    </form>
                </x-modal>
                
                <!-- ================= MODAL BULK APPROVE ================= -->
                <x-modal name="bulk-approve-modal" focusable>
                    <form method="POST" action="{{ route('hrd.leaves.bulk_action') }}" class="p-6">
                        @csrf
                        <input type="hidden" name="bulk_action" value="approve">
                        <input type="hidden" name="ids" :value="selectedIds.join(',')">
                        
                        <h2 class="text-xl font-bold text-stone-800 flex items-center">
                            <span class="p-2 rounded-full mr-3" style="background-color: #ABC270; color: white;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Approve Massal (<span x-text="selectedIds.length"></span> Pengajuan)
                        </h2>
                        <p class="mt-3 text-sm text-gray-600">
                            Anda akan menyetujui secara final semua pengajuan yang dipilih. Tindakan ini memotong kuota cuti tahunan dan membuat surat izin siap diunduh.
                        </p>

                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl">Batal</button>
                            <button type="submit" class="ms-3 px-4 py-2 text-sm font-bold text-white rounded-xl transition" style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7);">Lakukan Approve Massal</button>
                        </div>
                    </form>
                </x-modal>

                <!-- ================= MODAL BULK REJECT ================= -->
                <x-modal name="bulk-reject-modal" focusable>
                    <form method="POST" action="{{ route('hrd.leaves.bulk_action') }}" class="p-6">
                        @csrf
                        <input type="hidden" name="bulk_action" value="reject">
                        <input type="hidden" name="ids" :value="selectedIds.join(',')">
                        
                        <h2 class="text-xl font-bold text-red-600 flex items-center">
                            <span class="bg-red-100 text-red-600 p-2 rounded-full mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </span>
                            Tolak Massal (<span x-text="selectedIds.length"></span> Pengajuan)
                        </h2>
                        <div class="mt-4">
                            <label class="block text-sm font-bold text-gray-700">
                                Alasan Penolakan Massal <span class="text-red-500">*</span> (Min. 10 Karakter)
                            </label>
                            <textarea name="bulk_catatan" class="w-full border-gray-300 rounded-xl shadow-sm mt-1 focus:ring-red-500 focus:border-red-500" 
                                      rows="3" required minlength="10" placeholder="Wajib diisi: Contoh: Kuota Cuti Bersama Habis." style="border-radius: 12px;"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl">Batal</button>
                            <x-danger-button class="ms-3">Lakukan Tolak Massal</x-danger-button>
                        </div>
                    </form>
                </x-modal>

                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center border-2 border-dashed border-gray-300 rounded-xl" style="background-color: #FFFFFF; box-shadow: 0 10px 20px rgba(0,0,0,0.03);">
                    <div class="p-4 rounded-full mb-4" style="background-color: #ABC270; opacity: 0.5;">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-extrabold text-stone-800">Semua Proses Selesai!</h3>
                    <p class="text-sm text-gray-500 mt-1">Tidak ada pengajuan yang menunggu persetujuan final HRD saat ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>