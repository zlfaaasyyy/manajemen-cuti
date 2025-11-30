<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Rincian Pengajuan Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
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
                <div class="flex justify-between items-start border-b pb-4 mb-6" style="border-color: #f0f0f0;">
                    <div>
                        <h3 class="text-3xl font-extrabold text-stone-800">{{ $leave->user->name }}</h3>
                        <p class="text-gray-500 mt-1">Diajukan pada: {{ $leave->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase text-gray-500">Status Saat Ini</p>
                        <span class="px-3 py-1 mt-1 text-sm font-bold rounded-full 
                            {{ $leave->status == 'approved' ? 'bg-green-100 text-green-800' : 
                              ($leave->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                              ($leave->status == 'cancelled' ? 'bg-stone-800 text-white' : 
                              ($leave->status == 'pending' ? 'bg-gray-100 text-gray-700' : 
                              ($leave->status == 'approved_leader' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700')))) }}"
                              style="font-size: 14px;">
                            {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-y-4 gap-x-8 text-sm mb-6">
                    <div class="col-span-2">
                        <p class="font-bold text-gray-700">Jenis Cuti:</p>
                        <p class="text-stone-800 capitalize text-lg font-bold" style="color: #FDA769;">
                            {{ $leave->jenis_cuti }}
                        </p>
                    </div>

                    <div>
                        <p class="font-bold text-gray-700">Periode Cuti:</p>
                        <p class="text-stone-800 font-semibold">{{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="font-bold text-gray-700">Total Hari:</p>
                        <p class="text-stone-800 font-semibold">{{ $leave->total_hari }} Hari Kerja</p>
                    </div>
                    
                    <div class="col-span-2 mt-4 p-4 rounded-xl border" style="background-color: #F8F8F8; border-color: #F0F0F0;">
                        <p class="font-bold text-gray-700 mb-1">Alasan Pengajuan:</p>
                        <p class="text-stone-800 italic">"{{ $leave->alasan }}"</p>
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <p class="font-bold text-gray-700">Alamat Selama Cuti:</p>
                        <p class="text-stone-800">{{ $leave->alamat_selama_cuti }}</p>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <p class="font-bold text-gray-700">Nomor Darurat:</p>
                        <p class="text-stone-800">{{ $leave->nomor_darurat }}</p>
                    </div>
                </div>

                @if($leave->jenis_cuti == 'sakit' && $leave->bukti_sakit)
                    <div class="mt-6 border-t pt-4" style="border-color: #f0f0f0;">
                        <p class="font-bold text-gray-700 mb-2">Dokumen Pendukung (Surat Dokter):</p>
                        <a href="{{ Storage::url($leave->bukti_sakit) }}" target="_blank" 
                           class="inline-flex items-center px-4 py-2 text-sm font-bold text-white rounded-xl shadow-md transition" 
                           style="background-color: #ABC270; box-shadow: 0 2px 4px rgba(171, 194, 112, 0.5);">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Lihat Dokumen
                        </a>
                    </div>
                @endif
                
                <div class="mt-8 pt-6 border-t" style="border-color: #f0f0f0;">
                    <h4 class="text-xl font-extrabold text-stone-800 mb-4">Timeline Persetujuan</h4>
                    <ol class="relative border-s border-gray-200 ml-4">                  
                        <li class="mb-6 ms-6">
                            <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white" 
                                  style="background-color: #473C33; color: white;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h-2M7 21h-2M17 9H7m14 12v-7a2 2 0 00-2-2H7a2 2 0 00-2 2v7m14 0H5"></path></svg>
                            </span>
                            <h3 class="flex items-center mb-1 text-lg font-semibold text-stone-800">
                                Keputusan Final HRD
                                @if($leave->status == 'approved')
                                    <span class="bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded ms-3">DISETUJUI</span>
                                @elseif($leave->status == 'rejected')
                                    <span class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded ms-3">DITOLAK</span>
                                @endif
                            </h3>
                            @if($leave->approved_hrd_at)
                                <time class="block mb-2 text-xs font-normal leading-none text-gray-400">{{ $leave->approved_hrd_at->format('d F Y, H:i') }}</time>
                            @endif
                            <p class="text-sm font-normal text-gray-500">
                                Catatan HRD: <span class="font-semibold">{{ $leave->catatan_hrd ?? ($leave->status == 'approved' ? 'Disetujui tanpa catatan.' : ($leave->status == 'rejected' ? $leave->catatan_penolakan : 'Menunggu persetujuan HRD.')) }}</span>
                            </p>
                            @if($leave->status == 'approved')
                                <a href="{{ route('leaves.pdf', $leave->id) }}" class="inline-flex items-center px-4 py-2 mt-2 text-xs font-bold text-white bg-amber-500 rounded-xl hover:bg-amber-600 transition" style="background-color: #FDA769; box-shadow: 0 2px 4px rgba(253, 167, 105, 0.5);">
                                    Download Surat Izin (PDF)
                                </a>
                            @endif
                        </li>
                        
                        <li class="mb-6 ms-6">
                            <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white" 
                                  style="background-color: #ABC270; color: white;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2-4v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4a2 2 0 012-2h14a2 2 0 012 2z"></path></svg>
                            </span>
                            
                            <h3 class="flex items-center mb-1 text-lg font-semibold text-stone-800">
                                Persetujuan Ketua Divisi 
                                
                                @if($leave->status == 'rejected' && $leave->catatan_penolakan && str_contains($leave->catatan_penolakan, 'Ditolak Ketua Divisi'))
                                     <span class="bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded ms-3">DITOLAK LEADER</span>
                                @elseif($leave->approved_leader_at || $leave->status == 'approved' || $leave->status == 'rejected')
                                    <span class="bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded ms-3">SUDAH DISETUJUI</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 text-sm font-medium me-2 px-2.5 py-0.5 rounded ms-3">MENUNGGU VERIFIKASI</span>
                                @endif
                            </h3>
                            
                            @if($leave->user->role == 'ketua_divisi')
                                <time class="block mb-2 text-xs font-normal leading-none text-gray-400">Pengajuan Langsung ke HRD</time>
                                <p class="text-sm font-normal text-gray-500">Catatan Leader: <span class="font-semibold">Melewati verifikasi atasan (Ketua Divisi mengajukan sendiri).</span></p>
                            @elseif($leave->approved_leader_at)
                                <time class="block mb-2 text-xs font-normal leading-none text-gray-400">Oleh: {{ $leave->user->divisi->ketuaDivisi->name ?? 'N/A' }}, pada {{ $leave->approved_leader_at->format('d F Y, H:i') }}</time>
                                <p class="text-sm font-normal text-gray-500">Catatan Leader: <span class="font-semibold">{{ $leave->catatan_leader ?? 'Disetujui tanpa catatan.' }}</span></p>
                            @elseif($leave->status == 'rejected' && str_contains($leave->catatan_penolakan, 'Ditolak Ketua Divisi'))
                                <p class="text-sm font-normal text-red-500">Ditolak oleh Leader pada tahap ini. Lihat catatan HRD di atas.</p>
                            @else
                                <p class="text-sm font-normal text-gray-500">Sudah diproses oleh Ketua Divisi.</p>
                            @endif
                        </li>

                         <li class="ms-6">
                            <span class="absolute flex items-center justify-center w-6 h-6 rounded-full -start-3 ring-8 ring-white" 
                                  style="background-color: #ABC270; color: white;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            <h3 class="flex items-center mb-1 text-lg font-semibold text-stone-800">
                                Pengajuan Awal Dibuat
                                @if($leave->status == 'cancelled')
                                    <span class="bg-stone-800 text-white text-sm font-medium me-2 px-2.5 py-0.5 rounded ms-3">TELAH DIBATALKAN</span>
                                @endif
                            </h3>
                            <time class="block mb-2 text-xs font-normal leading-none text-gray-400">{{ $leave->created_at->format('d F Y, H:i') }}</time>
                            @if($leave->status == 'cancelled')
                                <p class="text-sm font-normal text-red-500 font-semibold">Dibatalkan oleh Karyawan. Alasan: {{ $leave->alasan_pembatalan ?? 'Tidak ada alasan.' }}</p>
                            @endif
                        </li>
                    </ol>
                </div>
                
                @if(auth()->user()->id == $leave->user_id && in_array($leave->status, ['pending', 'approved_leader']))
                    <div class="mt-8 pt-6 border-t flex justify-end" style="border-color: #f0f0f0;">
                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'cancel-modal')" 
                                class="px-6 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition" 
                                style="background-color: #FDA769; box-shadow: 0 4px 8px -2px rgba(253, 167, 105, 0.5); border-radius: 12px;">
                            Batalkan Pengajuan
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-modal name="cancel-modal" focusable>
        <form method="POST" action="{{ route('leaves.cancel', $leave->id) }}" class="p-6">
            @csrf
            @method('DELETE') 
            
            <h2 class="text-xl font-bold text-red-600 flex items-center">
                <span class="bg-red-100 text-red-600 p-2 rounded-full mr-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                Konfirmasi Pembatalan Cuti
            </h2>
            <p class="mt-3 text-sm text-gray-600">
                Anda akan membatalkan pengajuan cuti ini. Kuota cuti tahunan (jika terpotong) akan dikembalikan.
            </p>

            <div class="mt-4">
                <label class="block text-sm font-bold text-gray-700">
                    Alasan Pembatalan <span class="text-red-500">*</span>
                </label>
                <textarea name="alasan_pembatalan" class="w-full border-gray-300 rounded-xl shadow-sm mt-1 focus:ring-red-500 focus:border-red-500" 
                          rows="3" required placeholder="Jelaskan alasan Anda membatalkan cuti." style="border-radius: 12px;"></textarea>
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Tutup</x-secondary-button>
                <x-danger-button class="ms-3">Ya, Batalkan</x-danger-button>
            </div>
        </form>
    </x-modal>
</x-app-layout>