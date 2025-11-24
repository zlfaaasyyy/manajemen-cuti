<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Persetujuan Final Cuti (HRD)') }}
        </h2>
    </x-slot>

    <!-- CONTAINER UTAMA: Hapus Alpine Data untuk Bulk Action -->
    <div class="py-12" style="background-color: #F8F8F8;">
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

            <!-- Hapus TOOLBAR BULK ACTION (Fixed Bottom) -->
            <!-- Hapus checkox Select All -->

            <!-- HEADER LIST (Dibuat lebih sederhana) -->
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h3 class="text-xl font-extrabold text-stone-800">
                        Menunggu Persetujuan Final: 
                        <span class="px-3 py-1 rounded-full text-lg font-bold" style="background-color: #FFFBE8; color: #FEC868; border: 1px solid #FEC868;">
                            {{ $pendingRequests->count() }} Pengajuan
                        </span>
                    </h3>
                    <p class="text-sm text-gray-500 mt-2">Silakan review pengajuan di bawah ini sebelum memberikan keputusan final.</p>
                </div>
            </div>

            <!-- GRID KARTU PENGAJUAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($pendingRequests as $request)
                <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] border border-gray-100 flex flex-col transition hover:shadow-3xl duration-300"
                     style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-radius: 24px;">
                    
                    <!-- Hapus CHECKBOX INDIVIDU -->

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
                                Catatan: "<strong>{{ $request->catatan_leader ?? 'Tidak ada catatan.' }}</strong>"
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
                
                <!-- HAPUS MODAL BULK APPROVE -->
                <!-- HAPUS MODAL BULK REJECT -->

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