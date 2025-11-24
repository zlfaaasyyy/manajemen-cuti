<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- ============================================= -->
            <!-- 1. DASHBOARD ADMIN -->
            <!-- ============================================= -->
            @if(auth()->user()->role === 'admin')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Stat Cards -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Karyawan</p>
                    <h3 class="text-2xl font-bold dark:text-white">{{ $stats['total_karyawan'] ?? 0 }}</h3>
                    <p class="text-xs text-green-500">Semua User Aktif</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-purple-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Divisi</p>
                    <h3 class="text-2xl font-bold dark:text-white">{{ $stats['total_divisi'] ?? 0 }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-yellow-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Pengajuan Bulan Ini</p>
                    <h3 class="text-2xl font-bold dark:text-white">{{ $stats['pengajuan_bulan_ini'] ?? 0 }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Pending Approval</p>
                    <h3 class="text-2xl font-bold dark:text-white">{{ $stats['pending_approval'] ?? 0 }}</h3>
                    <p class="text-xs text-gray-500">Butuh Tindakan</p>
                </div>
            </div>

            <!-- Tabel Karyawan < 1 Tahun -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-white">Karyawan Baru (< 1 Tahun) - Belum Eligible Cuti Tahunan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Bergabung</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Masa Kerja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @if(isset($extraData['karyawan_baru']))
                                @forelse($extraData['karyawan_baru'] as $karyawan)
                                <tr>
                                    <td class="px-4 py-2 text-sm dark:text-white">{{ $karyawan->name }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $karyawan->email }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $karyawan->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-500">{{ $karyawan->created_at->diffForHumans(null, true) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-2 text-center text-sm text-gray-500">Tidak ada karyawan baru.</td></tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endif


            <!-- ============================================= -->
            <!-- 2. DASHBOARD USER (KARYAWAN) -->
            <!-- ============================================= -->
            @if(auth()->user()->role === 'user')
            <!-- Info Divisi & Ketua -->
            <div class="bg-indigo-600 rounded-lg shadow p-6 text-white mb-6">
                <h3 class="text-lg font-bold">Halo, {{ auth()->user()->name }}!</h3>
                <p class="text-indigo-200 text-sm mb-4">Berikut informasi divisi Anda:</p>
                <div class="flex items-center space-x-6">
                    <div>
                        <p class="text-xs uppercase opacity-75">Divisi Anda</p>
                        <p class="font-bold text-xl">{{ $extraData['divisi']->nama ?? 'Belum Ada Divisi' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase opacity-75">Ketua Divisi</p>
                        <p class="font-bold text-xl">{{ $extraData['ketua']->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Sisa Kuota Cuti Tahunan</p>
                    <h3 class="text-4xl font-bold text-green-600">{{ $stats['sisa_kuota'] ?? 0 }} <span class="text-sm text-gray-400">Hari</span></h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Cuti Sakit</p>
                    <h3 class="text-4xl font-bold text-red-600">{{ $stats['cuti_sakit'] ?? 0 }} <span class="text-sm text-gray-400">Kali</span></h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Pengajuan</p>
                    <h3 class="text-4xl font-bold text-blue-600">{{ $stats['total_pengajuan'] ?? 0 }}</h3>
                </div>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('leaves.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 shadow-md transition ease-in-out duration-150">
                    + Ajukan Cuti Baru
                </a>
            </div>
            @endif


            <!-- ============================================= -->
            <!-- 3. DASHBOARD KETUA DIVISI -->
            <!-- ============================================= -->
            @if(auth()->user()->role === 'ketua_divisi')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-blue-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Pengajuan Masuk</p>
                    <h3 class="text-3xl font-bold dark:text-white">{{ $stats['total_masuk'] ?? 0 }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-yellow-500 flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Pending Verifikasi Anda</p>
                        <h3 class="text-3xl font-bold text-yellow-600">{{ $stats['pending_verifikasi'] ?? 0 }}</h3>
                    </div>
                    @if(($stats['pending_verifikasi'] ?? 0) > 0)
                        <a href="{{ route('leader.leaves.index') }}" class="text-sm bg-yellow-100 text-yellow-700 px-3 py-1 rounded hover:bg-yellow-200 font-bold transition">Proses Sekarang &rarr;</a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Tabel Anggota Divisi -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-3 dark:text-white">Daftar Anggota Divisi</h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700 max-h-64 overflow-y-auto">
                        @if(isset($extraData['anggota_divisi']))
                            @forelse($extraData['anggota_divisi'] as $anggota)
                                <li class="py-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs font-bold mr-3">
                                            {{ substr($anggota->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium dark:text-white">{{ $anggota->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $anggota->email }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded border">Sisa: {{ $anggota->kuota_cuti }} Hari</span>
                                </li>
                            @empty
                                <li class="py-3 text-sm text-gray-500">Belum ada anggota.</li>
                            @endforelse
                        @endif
                    </ul>
                </div>

                <!-- Tabel Sedang Cuti Minggu Ini -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border border-blue-100">
                    <h3 class="font-bold text-lg mb-3 text-blue-700 dark:text-blue-400 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Sedang Cuti Minggu Ini
                    </h3>
                    <ul class="space-y-3 max-h-64 overflow-y-auto">
                        @if(isset($extraData['sedang_cuti_minggu_ini']))
                            @forelse($extraData['sedang_cuti_minggu_ini'] as $userCuti)
                                <li class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded flex justify-between items-center">
                                    <span class="font-bold text-sm text-gray-700 dark:text-gray-200">{{ $userCuti->name }}</span>
                                    <span class="text-xs text-blue-600 dark:text-blue-300 font-mono">
                                        {{-- Ambil request terakhir yg approved --}}
                                        @if($userCuti->leaveRequests->isNotEmpty())
                                        s/d {{ \Carbon\Carbon::parse($userCuti->leaveRequests->first()->tanggal_selesai)->format('d M') }}
                                        @endif
                                    </span>
                                </li>
                            @empty
                                <li class="text-sm text-gray-500 italic">Tidak ada anggota yang cuti minggu ini.</li>
                            @endforelse
                        @endif
                    </ul>
                </div>
            </div>
            @endif


            <!-- ============================================= -->
            <!-- 4. DASHBOARD HRD -->
            <!-- ============================================= -->
            @if(auth()->user()->role === 'hrd')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-yellow-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Pengajuan Bulan Ini</p>
                    <h3 class="text-2xl font-bold dark:text-white">{{ $stats['total_bulan_ini'] ?? 0 }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-red-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Pending Final Approval</p>
                    <h3 class="text-2xl font-bold dark:text-white">{{ $stats['pending_final'] ?? 0 }}</h3>
                    @if(($stats['pending_final'] ?? 0) > 0)
                        <a href="{{ route('hrd.leaves.index') }}" class="text-xs text-red-600 hover:underline font-bold">Lihat & Proses &rarr;</a>
                    @endif
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-purple-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Total Divisi</p>
                    <h3 class="text-2xl font-bold dark:text-white">{{ $stats['total_divisi'] ?? 0 }}</h3>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow border-l-4 border-green-500">
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Cuti Bulan Ini</p>
                    <h3 class="text-2xl font-bold dark:text-white">{{ isset($extraData['cuti_bulan_ini']) ? count($extraData['cuti_bulan_ini']) : 0 }} <span class="text-sm font-normal text-gray-400">Orang</span></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Tabel Karyawan Sedang Cuti Bulan Ini -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-3 text-green-700 dark:text-green-400">Karyawan Cuti (Bulan Ini)</h3>
                    <div class="overflow-y-auto max-h-60">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Divisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($extraData['cuti_bulan_ini']))
                                    @forelse($extraData['cuti_bulan_ini'] as $userCuti)
                                    <tr>
                                        <td class="px-2 py-2 border-b dark:border-gray-700">{{ $userCuti->name }}</td>
                                        <td class="px-2 py-2 border-b dark:border-gray-700 text-gray-500">{{ $userCuti->divisi->nama ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="2" class="px-2 py-2 text-center text-gray-500">Nihil.</td></tr>
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel Daftar Divisi Ringkas -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <h3 class="font-bold text-lg mb-3 text-purple-700 dark:text-purple-400">Ringkasan Divisi</h3>
                    <div class="overflow-y-auto max-h-60">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Divisi</th>
                                    <th class="px-2 py-2 text-center text-xs font-medium text-gray-500 uppercase">Jml Anggota</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ketua</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($extraData['daftar_divisi']))
                                    @foreach($extraData['daftar_divisi'] as $div)
                                    <tr>
                                        <td class="px-2 py-2 border-b dark:border-gray-700 font-bold">{{ $div->nama }}</td>
                                        <td class="px-2 py-2 border-b dark:border-gray-700 text-center">{{ $div->users_count }}</td>
                                        <td class="px-2 py-2 border-b dark:border-gray-700 text-gray-500">{{ $div->ketuaDivisi->name ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif


            <!-- ============================================= -->
            <!-- GLOBAL: Riwayat Cuti Terakhir (Muncul di Semua Role) -->
            <!-- ============================================= -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mt-8">
                <h3 class="text-gray-500 dark:text-gray-400 uppercase text-xs font-bold mb-4 tracking-wider">Riwayat Pengajuan Terakhir Saya</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 dark:bg-gray-700 uppercase font-bold text-xs text-gray-500">
                            <tr>
                                <th class="px-4 py-2">Tanggal</th>
                                <th class="px-4 py-2">Jenis</th>
                                <th class="px-4 py-2">Durasi</th>
                                <th class="px-4 py-2">Alasan & Catatan</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($riwayatCuti as $cuti)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-bold dark:text-white">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">s/d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</div>
                                </td>
                                <td class="px-4 py-3 capitalize dark:text-gray-300">{{ $cuti->jenis_cuti }}</td>
                                <td class="px-4 py-3 dark:text-gray-300">{{ $cuti->total_hari }} Hari</td>
                                <td class="px-4 py-3 dark:text-gray-300">
                                    <p class="italic">"{{ Str::limit($cuti->alasan, 20) }}"</p>
                                    @if($cuti->catatan_penolakan) <p class="text-red-500 text-xs mt-1">Note: {{ Str::limit($cuti->catatan_penolakan, 20) }}</p> @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded text-xs font-bold 
                                    {{ $cuti->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($cuti->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                      ($cuti->status == 'cancelled' ? 'bg-gray-800 text-white' : 'bg-yellow-100 text-yellow-800')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $cuti->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('leaves.show', $cuti->id) }}" class="text-blue-600 hover:underline">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat pengajuan cuti.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>