<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Verifikasi Pengajuan (Leader)') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Pesan Sukses -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-sm flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Header Statistik -->
            <div class="mb-6 flex items-center justify-between">
                <h3 class="text-xl font-extrabold text-stone-800">
                    Menunggu Persetujuan: 
                    <span class="px-3 py-1 rounded-full text-lg font-bold" style="background-color: #FFFBE8; color: #FEC868; border: 1px solid #FEC868;">
                        {{ $pendingRequests->count() }} Pengajuan
                    </span>
                </h3>
            </div>

            <!-- GRID CARD LAYOUT -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($pendingRequests as $request)
                <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] border border-gray-100 flex flex-col transition hover:shadow-3xl duration-300" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                    
                    <!-- Bagian Atas: Info User (Warna Aksen #473C33) -->
                    <div class="p-5 flex items-center space-x-4 border-b" style="background-color: #F8F8F8; border-color: #F0F0F0;">
                        <!-- Foto Profil (Warna Aksen #ABC270) -->
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
                            <p class="text-[10px] text-gray-400 mt-1">Diajukan: {{ $request->created_at->diffForHumans() }}</p>
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
                        
                        <!-- Periode Cuti (Warna Sekunder #FEC868) -->
                        <div class="mb-4 text-sm text-gray-600 p-3 rounded-xl border border-amber-200" style="background-color: #FFFBE8;">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs text-gray-500">Periode:</span>
                                <span class="font-semibold text-amber-700">{{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y') }}</span>
                            </div>
                        </div>

                        <p class="text-xs font-bold text-gray-500 mb-1">Alasan Karyawan:</p>
                        <div class="text-sm text-stone-800 italic mb-3 flex-1">
                            "{{ Str::limit($request->alasan, 100) }}"
                        </div>

                        <!-- Link Surat Dokter -->
                        @if($request->jenis_cuti == 'sakit' && $request->bukti_sakit)
                            <a href="{{ Storage::url($request->bukti_sakit) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-orange-600 hover:text-orange-800 hover:underline mt-auto">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Lihat Bukti Dokter
                            </a>
                        @endif
                    </div>

                    <!-- Bagian Bawah: Tombol Aksi -->
                    <div class="p-5 border-t" style="border-color: #F0F0F0; background-color: #F8F8F8;">
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Tombol Reject -->
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'reject-modal-{{ $request->id }}')" 
                                class="w-full py-3 px-4 bg-white border border-red-500 text-red-600 rounded-xl hover:bg-red-50 transition text-sm font-bold flex justify-center items-center group shadow-md" style="border-radius: 12px;">
                                <svg class="w-4 h-4 mr-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak
                            </button>

                            <!-- Tombol Approve (HIJAU LUMUT #ABC270) -->
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'approve-modal-{{ $request->id }}')" 
                                class="w-full py-3 px-4 text-white rounded-xl transition text-sm font-bold shadow-lg flex justify-center items-center group" style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7); border-radius: 12px;">
                                <svg class="w-4 h-4 mr-1 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Setujui
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ================= MODAL APPROVE (Catatan Opsional) ================= -->
                <x-modal name="approve-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('leader.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        
                        <h2 class="text-xl font-bold text-stone-800 flex items-center">
                            <span class="p-2 rounded-full mr-3" style="background-color: #ABC270; color: white;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Konfirmasi Persetujuan
                        </h2>
                        <p class="mt-3 text-sm text-gray-600">
                            Anda akan menyetujui pengajuan cuti <strong>{{ $request->user->name }}</strong>. Status akan diperbarui dan diteruskan ke HRD.
                        </p>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Catatan Tambahan (Opsional)</label>
                            <textarea name="catatan" class="w-full border-gray-300 rounded-xl shadow-sm mt-1 focus:ring-green-500 focus:border-green-500" rows="2" placeholder="Contoh: Pekerjaan sudah didelegasikan..." style="border-radius: 12px;"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl">Batal</button>
                            <button type="submit" class="ms-3 px-4 py-2 text-sm font-bold text-white rounded-xl transition" style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7);">Ya, Setujui</button>
                        </div>
                    </form>
                </x-modal>

                <!-- ================= MODAL REJECT (Alasan Wajib) ================= -->
                <x-modal name="reject-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('leader.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        
                        <h2 class="text-xl font-bold text-red-600 flex items-center">
                            <span class="bg-red-100 text-red-600 p-2 rounded-full mr-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </span>
                            Tolak Pengajuan
                        </h2>
                        <p class="mt-3 text-sm text-gray-600">
                            Pengajuan <strong>{{ $request->user->name }}</strong> akan ditolak. Kuota cuti (jika ada) akan dikembalikan.
                        </p>

                        <div class="mt-4">
                            <label class="block text-sm font-bold text-gray-700">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="catatan" class="w-full border-gray-300 rounded-xl shadow-sm mt-1 focus:ring-red-500 focus:border-red-500" 
                                      rows="3" required placeholder="Wajib diisi: Kenapa pengajuan ini ditolak?" style="border-radius: 12px;"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Karyawan akan melihat alasan ini.</p>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-xl">Batal</button>
                            <x-danger-button class="ms-3">Tolak Pengajuan</x-danger-button>
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
                    <h3 class="text-lg font-extrabold text-stone-800">Semua Bersih!</h3>
                    <p class="text-sm text-gray-500 mt-1">Tidak ada pengajuan cuti yang menunggu verifikasi Anda saat ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>