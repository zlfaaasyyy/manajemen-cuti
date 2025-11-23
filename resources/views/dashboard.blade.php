<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                @if(auth()->user()->role === 'admin')
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Total Karyawan</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['total_karyawan'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Total Divisi</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['total_divisi'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-purple-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Pengajuan Bulan Ini</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['pengajuan_bulan_ini'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-yellow-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Karyawan Baru (&lt; 1 Thn)</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['karyawan_baru'] ?? 0 }}</div>
                    </div>
                @endif

                @if(auth()->user()->role === 'user')
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Sisa Kuota Cuti</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['sisa_kuota'] ?? 0 }} Hari</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Total Cuti Sakit</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['cuti_sakit_diajukan'] ?? 0 }} Kali</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-gray-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Divisi & Ketua</div>
                        <div class="text-sm font-bold mt-1 dark:text-white">{{ $stats['nama_divisi'] }}</div>
                        <div class="text-xs dark:text-gray-300">{{ $stats['nama_ketua'] }}</div>
                    </div>
                    <a href="{{ route('leaves.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow flex items-center justify-center font-bold transition duration-150 ease-in-out">
                        + Ajukan Cuti Baru
                    </a>
                @endif

                @if(auth()->user()->role === 'ketua_divisi')
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Total Pengajuan Masuk</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['total_masuk'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-yellow-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Pending Verifikasi</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['pending_verifikasi'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-indigo-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Anggota Cuti Minggu Ini</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['sedang_cuti'] ?? 0 }}</div>
                    </div>
                    <a href="{{ route('leader.leaves.index') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white p-6 rounded-lg shadow flex items-center justify-center font-bold transition duration-150 ease-in-out">
                        Verifikasi Pengajuan
                    </a>
                @endif

                @if(auth()->user()->role === 'hrd')
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Pengajuan Bulan Ini</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['total_bulan_ini'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Pending Final Approval</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['pending_final'] ?? 0 }}</div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500">
                        <div class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Sedang Cuti Bulan Ini</div>
                        <div class="text-2xl font-bold dark:text-white">{{ $stats['sedang_cuti_bulan_ini'] ?? 0 }}</div>
                    </div>
                    <a href="{{ route('hrd.leaves.index') }}" class="bg-pink-600 hover:bg-pink-700 text-white p-6 rounded-lg shadow flex items-center justify-center font-bold transition duration-150 ease-in-out">
                        Verifikasi HRD
                    </a>
                @endif

            </div>

            @if(auth()->user()->role == 'user' || auth()->user()->role == 'ketua_divisi')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Riwayat Pengajuan Cuti Pribadi</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Alasan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($riwayatCuti as $cuti)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm capitalize dark:text-white">{{ $cuti->jenis_cuti }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm dark:text-gray-400">{{ $cuti->total_hari }} Hari</td>
                                    <td class="px-6 py-4 text-sm max-w-xs truncate dark:text-gray-400">{{ $cuti->alasan }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 text-xs font-semibold rounded-full 
                                            {{ $cuti->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                              ($cuti->status == 'pending' ? 'bg-gray-100 text-gray-800' : 
                                              ($cuti->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $cuti->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($cuti->status == 'pending')
                                            <form action="{{ route('leaves.cancel', $cuti->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini? Kuota akan dikembalikan.');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @elseif($cuti->status == 'approved')
                                            <a href="{{ route('leaves.pdf', $cuti->id) }}" target="_blank" class="text-blue-600 hover:text-blue-900 font-bold flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                Unduh Surat
                                            </a>
                                        @else - @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada riwayat pengajuan cuti.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>