<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- PESAN SUKSES -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- KARTU STATISTIK -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <!-- Kartu 1: Sisa Kuota (Disembunyikan untuk HRD) -->
                @if(auth()->user()->role !== 'hrd')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold">Sisa Kuota Cuti</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ auth()->user()->kuota_cuti }} <span class="text-lg font-normal text-gray-500">Hari</span>
                    </div>
                </div>
                @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold">Posisi</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">HRD / Admin</div>
                    <p class="text-xs text-gray-500 mt-1">Pengelola Sistem</p>
                </div>
                @endif

                <!-- Kartu 2: Status Pending -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm uppercase font-bold">Menunggu Proses</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ $statusPending }} <span class="text-lg font-normal text-gray-500">Pengajuan</span>
                    </div>
                </div>

                <!-- Kartu 3: Shortcut Menu -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500 flex flex-col justify-center space-y-2">
                    @if(auth()->user()->role === 'user' || auth()->user()->role === 'ketua_divisi')
                        <a href="{{ route('leaves.create') }}" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Ajukan Cuti Baru
                        </a>
                    @endif
                    
                    @if(auth()->user()->role === 'ketua_divisi')
                        <a href="{{ route('leader.leaves.index') }}" class="block w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                            Verifikasi Bawahan
                        </a>
                    @endif

                    @if(auth()->user()->role === 'hrd')
                        <a href="{{ route('hrd.leaves.index') }}" class="block w-full text-center bg-pink-600 hover:bg-pink-700 text-white font-bold py-2 px-4 rounded">
                            Verifikasi Final HRD
                        </a>
                    @endif
                </div>
            </div>

            <!-- TABEL RIWAYAT CUTI -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Riwayat Pengajuan Cuti Anda</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Durasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Alasan & Catatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($riwayatCuti as $cuti)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} <br>
                                        <span class="text-xs">s/d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white capitalize">
                                        {{ $cuti->jenis_cuti }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $cuti->total_hari }} Hari
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-xs">
                                        <p class="italic">"{{ $cuti->alasan }}"</p>
                                        @if($cuti->catatan_penolakan)
                                            <p class="text-red-500 text-xs mt-1">Note: {{ $cuti->catatan_penolakan }}</p>
                                        @endif
                                        @if($cuti->bukti_sakit)
                                            <a href="{{ Storage::url($cuti->bukti_sakit) }}" target="_blank" class="text-blue-500 text-xs hover:underline">Lihat Surat</a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cuti->status == 'pending')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Pending</span>
                                        @elseif($cuti->status == 'approved_leader')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">ACC Ketua Divisi</span>
                                        @elseif($cuti->status == 'approved')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disetujui HRD</span>
                                        @elseif($cuti->status == 'rejected')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                        @elseif($cuti->status == 'cancelled')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-800 text-white">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <!-- Tombol Batal hanya jika status PENDING -->
                                        @if($cuti->status == 'pending')
                                            <form action="{{ route('leaves.cancel', $cuti->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini? Kuota akan dikembalikan.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-500 font-bold">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada riwayat pengajuan cuti.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>