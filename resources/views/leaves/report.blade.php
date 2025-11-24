<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Laporan Seluruh Cuti Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filter & Download Section -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[24px] p-6 mb-8 border border-gray-100 flex justify-between items-center" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                <h3 class="font-extrabold text-lg text-stone-800">Data Riwayat Pengajuan Cuti (Total: {{ $leaves->count() }})</h3>
               
            </div>


            <!-- MAIN CARD: Tabel Data -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead style="background-color: #F8F8F8;">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Nama Karyawan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Divisi</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Jenis</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Periode Cuti</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Durasi</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Surat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($leaves as $leave)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-stone-800">{{ $leave->user->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $leave->user->divisi->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-stone-800 capitalize">{{ $leave->jenis_cuti }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                    {{ $leave->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($leave->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                      ($leave->status == 'cancelled' ? 'bg-stone-800 text-white' : 
                                      ($leave->status == 'pending' ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-800'))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-stone-800">{{ $leave->total_hari }} Hari</td>

                                <!-- TOMBOL DOWNLOAD PDF (Hijau Lumut #ABC270) -->
                                <td class="px-4 py-3 text-sm font-medium">
                                    @if($leave->status == 'approved')
                                        <a href="{{ route('leaves.pdf', $leave->id) }}" class="text-white px-3 py-1.5 rounded-xl text-xs font-bold hover:opacity-90 transition" style="background-color: #ABC270;">
                                            <svg class="w-4 h-4 inline-block -mt-0.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            PDF
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat pengajuan cuti yang tercatat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>