<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Tambahkan Banner Selamat Datang yang Jelas --}}
            {{-- <div class="bg-white p-6 rounded-[30px] shadow-xl border border-gray-100 mb-6">
                <h3 class="text-2xl font-extrabold text-stone-800">
                    @if(auth()->user()->role === 'admin')
                        Selamat Datang, Administrator!
                    @elseif(auth()->user()->role === 'hrd')
                        Selamat Datang, Staff HRD!
                    @else
                        Selamat Datang, {{ auth()->user()->name }}!
                    @endif
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    @if(auth()->user()->role === 'admin')
                        Kelola data pengguna, divisi, dan pantau performa sistem cuti.
                    @elseif(auth()->user()->role === 'hrd')
                        Lakukan verifikasi final dan pantau laporan cuti global.
                    @else
                        Siap untuk mengelola cuti Anda?
                    @endif
                </p>
            </div> --}}
            {{-- Akhir Banner Selamat Datang --}}

            {{-- ============================================= --}}
            {{-- 1. DASHBOARD ADMIN --}}
            {{-- ============================================= --}}
            @if(auth()->user()->role === 'admin')
            <div class="rounded-[24px] shadow-xl p-6 text-white mb-6" style="background-color: #473C33;">
                <h3 class="text-xl font-extrabold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                <p class="text-gray-300 text-sm mb-4">Kelola data pengguna, divisi, dan pantau performa sistem.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Stat Card 1: Total Karyawan (DIPERBARUI) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #34D399;">
                    <p class="text-gray-500 text-sm">Total Karyawan</p>
                    <h3 class="text-3xl font-extrabold text-stone-800 mt-1">{{ $stats['total_karyawan'] ?? 0 }}</h3>
                    
                    <!-- Breakdown Status: Aktif vs Cuti -->
                    <div class="flex items-center space-x-2 mt-3">
                        <!-- Aktif -->
                        <div class="flex items-center px-2.5 py-1 rounded-lg bg-green-50 border border-green-100" title="Karyawan Aktif Bekerja Hari Ini">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                            <span class="text-xs font-bold text-green-700">{{ $stats['karyawan_aktif'] ?? 0 }} Aktif</span>
                        </div>
                        
                        <!-- Cuti -->
                        <div class="flex items-center px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-100" title="Karyawan Sedang Cuti Hari Ini">
                            <span class="w-2 h-2 bg-amber-500 rounded-full mr-1.5"></span>
                            <span class="text-xs font-bold text-amber-700">{{ $stats['karyawan_cuti'] ?? 0 }} Cuti</span>
                        </div>
                    </div>
                </div>

                <!-- Stat Card 2: Total Divisi -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #292524;">
                    <p class="text-gray-500 text-sm">Total Divisi</p>
                    <h3 class="text-3xl font-extrabold text-stone-800 mt-1">{{ $stats['total_divisi'] ?? 0 }}</h3>
                </div>

                <!-- Stat Card 3: Pengajuan Bulan Ini -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #FDA769;">
                    <p class="text-gray-500 text-sm">Pengajuan Bulan Ini</p>
                    <h3 class="text-3xl font-extrabold" style="color: #FDA769;">{{ $stats['pengajuan_bulan_ini'] ?? 0 }}</h3>
                </div>

                <!-- Stat Card 4: Pending Approval -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #DC2626;">
                    <p class="text-gray-500 text-sm">Pending Approval</p>
                    <h3 class="text-3xl font-extrabold text-red-600">{{ $stats['pending_approval'] ?? 0 }}</h3>
                    <p class="text-xs text-red-700 mt-2">Butuh Tindakan</p>
                </div>
            </div>

            <!-- Tabel Karyawan < 1 Tahun -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                <h3 class="font-extrabold text-xl mb-4 text-stone-800">Karyawan Baru (< 1 Tahun)</h3>
                <p class="text-sm text-gray-500 mb-4">Mereka belum eligible untuk Cuti Tahunan.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead style="background-color: #F8F8F8;">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Nama</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tanggal Bergabung</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase">Masa Kerja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @if(isset($extraData['karyawan_baru']))
                                @forelse($extraData['karyawan_baru'] as $karyawan)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-stone-800">{{ $karyawan->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $karyawan->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $karyawan->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 font-bold">{{ $karyawan->created_at->diffForHumans(null, true) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">Tidak ada karyawan baru.</td></tr>
                                @endforelse
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @endif


            {{-- ============================================= --}}
            {{-- 2. DASHBOARD USER (KARYAWAN) --}}
            {{-- ============================================= --}}
            @if(auth()->user()->role === 'user')
            <!-- Info Divisi & Ketua (Warna Cokelat Gelap) -->
            <div class="rounded-[24px] shadow-xl p-6 text-white mb-6" style="background-color: #473C33;">
                <h3 class="text-xl font-extrabold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                <p class="text-gray-300 text-sm mb-4">Siap untuk mengelola cuti Anda?</p>
                <div class="flex items-center space-x-8">
                    <div>
                        <p class="text-xs uppercase opacity-75">Divisi Anda</p>
                        <p class="font-bold text-xl" style="color: #FEC868;">{{ $extraData['divisi']->nama ?? 'Belum Ada Divisi' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase opacity-75">Ketua Divisi</p>
                        <p class="font-bold text-xl">{{ $extraData['ketua']->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- SISA KUOTA (HIJAU LUMUT) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #ABC270;">
                    <p class="text-gray-500 text-sm">Sisa Kuota Cuti Tahunan</p>
                    <h3 class="text-4xl font-extrabold mt-1" style="color: #ABC270;">{{ $stats['sisa_kuota'] ?? 0 }} <span class="text-base font-medium text-gray-400">Hari</span></h3>
                </div>
                <!-- Cuti Sakit (MERAH) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #EF4444;">
                    <p class="text-gray-500 text-sm">Total Cuti Sakit</p>
                    <h3 class="text-4xl font-extrabold text-red-600 mt-1">{{ $stats['cuti_sakit'] ?? 0 }} <span class="text-base font-medium text-gray-400">Kali</span></h3>
                </div>
                <!-- Total Pengajuan (ORANGE TERAKOTA) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #FDA769;">
                    <p class="text-gray-500 text-sm">Total Pengajuan Anda</p>
                    <h3 class="text-4xl font-extrabold mt-1" style="color: #FDA769;">{{ $stats['total_pengajuan'] ?? 0 }}</h3>
                </div>
            </div>
            
            <div class="mt-8">
                <a href="{{ route('leaves.create') }}" class="inline-flex items-center px-6 py-3 font-bold text-white rounded-xl shadow-lg transition" style="background-color: #FDA769; box-shadow: 0 6px 12px -2px rgba(253, 167, 105, 0.5); border: none; font-size: 15px;">
                    + Ajukan Cuti Baru
                </a>
            </div>
            @endif


            {{-- ============================================= --}}
            {{-- 3. DASHBOARD KETUA DIVISI --}}
            {{-- ============================================= --}}
            @if(auth()->user()->role === 'ketua_divisi')
            <!-- Info Divisi & Ketua (Warna Cokelat Gelap - Mirip Dashboard User) -->
            <div class="rounded-[24px] shadow-xl p-6 text-white mb-6" style="background-color: #473C33;">
                <h3 class="text-xl font-extrabold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                <p class="text-gray-300 text-sm mb-4">Anda adalah Ketua Divisi <strong>{{ auth()->user()->divisiKetua->nama ?? 'N/A' }}</strong></p>
                <div class="flex items-center space-x-8">
                    <div>
                        <p class="text-xs uppercase opacity-75">Total Anggota</p>
                        <p class="font-bold text-xl" style="color: #FEC868;">{{ isset($extraData['anggota_divisi']) ? count($extraData['anggota_divisi']) : 0 }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase opacity-75">Sisa Kuota Pribadi</p>
                        <p class="font-bold text-xl">{{ auth()->user()->kuota_cuti ?? 0 }} Hari</p>
                    </div>
                </div>
                <!-- TOMBOL AJUKAN CUTI PRIBADI -->
                <div class="mt-4">
                    <a href="{{ route('leaves.create') }}" class="inline-flex items-center px-6 py-2.5 font-bold text-white rounded-xl shadow-lg transition" style="background-color: #FDA769; box-shadow: 0 4px 8px -2px rgba(253, 167, 105, 0.5); border: none; font-size: 14px;">
                        + Ajukan Cuti Pribadi
                    </a>
                </div>
            </div>

            <!-- STATISTIK UTAMA -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- PENDING VERIFIKASI (ORANGE TERAKOTA) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #FDA769;">
                    <p class="text-gray-500 text-sm">Pending Verifikasi Anda</p>
                    <h3 class="text-4xl font-extrabold mt-1" style="color: #FDA769;">{{ $stats['pending_verifikasi'] ?? 0 }} <span class="text-base font-medium text-gray-400">Pengajuan</span></h3>
                    @if(($stats['pending_verifikasi'] ?? 0) > 0)
                        <a href="{{ route('leader.leaves.index') }}" class="text-xs text-orange-600 hover:underline font-bold mt-2 inline-block">Proses Sekarang &rarr;</a>
                    @endif
                </div>
                <!-- TOTAL PENGAJUAN MASUK (HIJAU LUMUT) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #ABC270;">
                    <p class="text-gray-500 text-sm">Total Pengajuan Divisi</p>
                    <h3 class="text-4xl font-extrabold mt-1" style="color: #ABC270;">{{ $stats['total_masuk'] ?? 0 }} <span class="text-base font-medium text-gray-400">Total</span></h3>
                </div>
                 <!-- SEDANG CUTI MINGGU INI (COKELAT GELAP) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #473C33;">
                    <p class="text-gray-500 text-sm">Anggota Cuti Minggu Ini</p>
                    <h3 class="text-4xl font-extrabold text-stone-800 mt-1">{{ isset($extraData['sedang_cuti_minggu_ini']) ? count($extraData['sedang_cuti_minggu_ini']) : 0 }} <span class="text-base font-medium text-gray-400">Orang</span></h3>
                </div>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
                <!-- Tabel Anggota Divisi -->
                <div class="bg-white p-8 rounded-[30px] shadow-2xl border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                    <h3 class="font-extrabold text-xl mb-3 text-stone-800">Daftar Anggota Divisi</h3>
                    <ul class="divide-y divide-gray-200 max-h-72 overflow-y-auto">
                        @if(isset($extraData['anggota_divisi']))
                            @forelse($extraData['anggota_divisi'] as $anggota)
                                <li class="py-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-white font-bold text-xs mr-3" style="background-color: #ABC270;">
                                            {{ substr($anggota->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-stone-800">{{ $anggota->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $anggota->email }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full border">Sisa: {{ $anggota->kuota_cuti }} Hari</span>
                                </li>
                            @empty
                                <li class="py-3 text-sm text-gray-500 italic">Belum ada anggota di divisi.</li>
                            @endforelse
                        @endif
                    </ul>
                </div>

                <!-- Tabel Sedang Cuti Minggu Ini -->
                <div class="bg-white p-8 rounded-[30px] shadow-2xl border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                    <h3 class="font-extrabold text-xl mb-3" style="color: #FDA769;">
                        Sedang Cuti Minggu Ini
                    </h3>
                    <ul class="space-y-3 max-h-72 overflow-y-auto">
                        @if(isset($extraData['sedang_cuti_minggu_ini']))
                            @forelse($extraData['sedang_cuti_minggu_ini'] as $userCuti)
                                <li class="bg-orange-50 p-3 rounded-xl flex justify-between items-center border border-orange-200">
                                    <div class="flex items-center space-x-3">
                                        <!-- Initial Anggota -->
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-white font-bold text-xs" style="background-color: #FDA769;">
                                            {{ substr($userCuti->name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-sm text-stone-800">{{ $userCuti->name }}</span>
                                    </div>
                                    <span class="text-xs text-orange-600 font-mono">
                                        @if($userCuti->leaveRequests->isNotEmpty())
                                            s/d {{ \Carbon\Carbon::parse($userCuti->leaveRequests->first()->tanggal_selesai)->format('d M') }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </li>
                            @empty
                                <li class="text-sm text-gray-500 italic p-3">Tidak ada anggota yang cuti minggu ini.</li>
                            @endforelse
                        @endif
                    </ul>
                </div>
            </div>
            @endif


            {{-- ============================================= --}}
            {{-- 4. DASHBOARD HRD --}}
            {{-- ============================================= --}}
            @if(auth()->user()->role === 'hrd')
            <div class="rounded-[24px] shadow-xl p-6 text-white mb-6" style="background-color: #473C33;">
                <h3 class="text-xl font-extrabold">Selamat Datang, {{ auth()->user()->name }}!</h3>
                <p class="text-gray-300 text-sm mb-4">Lakukan verifikasi final dan pantau laporan cuti global.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pengajuan Bulan Ini -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #292524;">
                    <p class="text-gray-500 text-sm">Total Pengajuan Bulan Ini</p>
                    <h3 class="text-3xl font-extrabold text-stone-800 mt-1">{{ $stats['total_bulan_ini'] ?? 0 }}</h3>
                </div>
                <!-- Pending Final (ORANGE TERAKOTA) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #DC2626;">
                    <p class="text-gray-500 text-sm">Pending Final Approval</p>
                    <h3 class="text-3xl font-extrabold text-red-600 mt-1">{{ $stats['pending_final'] ?? 0 }}</h3>
                    @if(($stats['pending_final'] ?? 0) > 0)
                        <a href="{{ route('hrd.leaves.index') }}" class="text-xs text-red-600 hover:underline font-bold mt-2 inline-block">Lihat & Proses &rarr;</a>
                    @endif
                </div>
                <!-- Total Divisi -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #473C33;">
                    <p class="text-gray-500 text-sm">Total Divisi</p>
                    <h3 class="text-3xl font-extrabold text-stone-800 mt-1">{{ $stats['total_divisi'] ?? 0 }}</h3>
                </div>
                <!-- Cuti Bulan Ini (HIJAU LUMUT) -->
                <div class="bg-white p-6 rounded-[24px] shadow-xl border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-left: 6px solid #ABC270;">
                    <p class="text-gray-500 text-sm">Karyawan Cuti Bulan Ini</p>
                    <h3 class="text-3xl font-extrabold" style="color: #ABC270;">{{ isset($extraData['cuti_bulan_ini']) ? count($extraData['cuti_bulan_ini']) : 0 }} <span class="text-base font-normal text-gray-400">Orang</span></h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Tabel Karyawan Sedang Cuti Bulan Ini -->
                <div class="bg-white p-8 rounded-[30px] shadow-2xl border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                    <h3 class="font-extrabold text-xl mb-3" style="color: #ABC270;">Karyawan Cuti (Bulan Ini)</h3>
                    <div class="overflow-y-auto max-h-60">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-700 uppercase">Nama</th>
                                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-700 uppercase">Divisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($extraData['cuti_bulan_ini']))
                                    @forelse($extraData['cuti_bulan_ini'] as $userCuti)
                                    <tr>
                                        <td class="px-2 py-2 border-b">{{ $userCuti->name }}</td>
                                        <td class="px-2 py-2 border-b text-gray-500">{{ $userCuti->divisi->nama ?? '-' }}</td>
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
                <div class="bg-white p-8 rounded-[30px] shadow-2xl border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                    <h3 class="font-extrabold text-xl mb-3" style="color: #473C33;">Ringkasan Divisi</h3>
                    <div class="overflow-y-auto max-h-60">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-700 uppercase">Nama Divisi</th>
                                    <th class="px-2 py-2 text-center text-xs font-bold text-gray-700 uppercase">Jml Anggota</th>
                                    <th class="px-2 py-2 text-left text-xs font-bold text-gray-700 uppercase">Ketua</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($extraData['daftar_divisi']))
                                    @foreach($extraData['daftar_divisi'] as $div)
                                    <tr>
                                        <td class="px-2 py-2 border-b font-bold">{{ $div->nama }}</td>
                                        <td class="px-2 py-2 border-b text-center text-gray-500">{{ $div->users_count }}</td>
                                        <td class="px-2 py-2 border-b text-gray-500">{{ $div->ketuaDivisi->name ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif


            {{-- ============================================= --}}
            {{-- RIWAYAT CUTI TERAKHIR (HANYA UNTUK KARYAWAN & KETUA DIVISI) --}}
            {{-- ============================================= --}}
            @if(auth()->user()->role === 'user' || auth()->user()->role === 'ketua_divisi')
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 mt-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                <h3 class="text-gray-500 uppercase text-xs font-bold mb-4 tracking-widest">Riwayat Pengajuan Terakhir Saya</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead style="background-color: #F8F8F8;" class="uppercase font-bold text-xs text-gray-500">
                            <tr>
                                <th class="px-4 py-2">Tanggal</th>
                                <th class="px-4 py-2">Jenis</th>
                                <th class="px-4 py-2">Durasi</th>
                                <th class="px-4 py-2">Alasan & Catatan</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($riwayatCuti as $cuti)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-stone-800">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500">s/d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</div>
                                </td>
                                <td class="px-4 py-3 capitalize text-stone-800">{{ $cuti->jenis_cuti }}</td>
                                <td class="px-4 py-3 text-stone-800">{{ $cuti->total_hari }} Hari</td>
                                <td class="px-4 py-3 text-stone-800">
                                    <p class="italic">"{{ Str::limit($cuti->alasan, 20) }}"</p>
                                    @if($cuti->catatan_penolakan) <p class="text-red-500 text-xs mt-1">Note: {{ Str::limit($cuti->catatan_penolakan, 20) }}</p> @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                                    {{ $cuti->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($cuti->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                      ($cuti->status == 'cancelled' ? 'bg-stone-800 text-white' : 
                                      ($cuti->status == 'pending' ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-800'))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $cuti->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('leaves.show', $cuti->id) }}" class="text-blue-600 hover:underline font-bold">Detail</a>
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
            @endif
            
        </div>
    </div>
</x-app-layout>