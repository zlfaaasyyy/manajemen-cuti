<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Riwayat Pengajuan Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <!-- Container Lebar -->
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8">
            
            <!-- FILTER SECTION (Card Berbentuk Modern) -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[24px] p-6 mb-8 border border-gray-100" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                <h3 class="font-extrabold text-lg text-stone-800 mb-4">Filter Riwayat</h3>
                
                <form method="GET" action="{{ route('leaves.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                    
                    <!-- Filter Jenis -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Jenis Cuti</label>
                        <select name="jenis_cuti" class="w-full border-gray-300 rounded-xl shadow-sm text-stone-700 py-2.5 text-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                            <option value="">Semua Jenis</option>
                            <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                            <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                        </select>
                    </div>

                    <!-- Filter Status -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-xl shadow-sm text-stone-700 py-2.5 text-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved_leader" {{ request('status') == 'approved_leader' ? 'selected' : '' }}>ACC Leader</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <!-- Filter Tanggal Pengajuan -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Tgl Pengajuan</label>
                        <input type="date" name="tgl_pengajuan" value="{{ request('tgl_pengajuan') }}" class="w-full border-gray-300 rounded-xl shadow-sm text-stone-700 py-2.5 text-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                    </div>

                    <!-- Filter Bulan -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Periode Bulan</label>
                        <select name="bulan" class="w-full border-gray-300 rounded-xl shadow-sm text-stone-700 py-2.5 text-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                            <option value="">Semua Bulan</option>
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div class="lg:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Periode Tahun</label>
                        <select name="tahun" class="w-full border-gray-300 rounded-xl shadow-sm text-stone-700 py-2.5 text-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                            <option value="">Semua Tahun</option>
                            @for($y=date('Y'); $y>=date('Y')-2; $y--)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Tombol Filter (ORANGE TERAKOTA #FDA769) -->
                    <div class="lg:col-span-1">
                        <button type="submit" 
                                class="text-white w-full px-4 py-2.5 rounded-xl text-sm font-bold shadow-lg hover:opacity-90 transition"
                                style="background-color: #FDA769; box-shadow: 0 4px 8px -2px rgba(253, 167, 105, 0.5); border-radius: 12px;">
                            <svg class="w-4 h-4 mr-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v7l-4 3v-7a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- TABEL DATA (Card Berbentuk Modern) -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                <h3 class="font-extrabold text-xl mb-4 text-stone-800">Riwayat Pengajuan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead style="background-color: #F8F8F8;">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Tgl Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Periode Cuti</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($leaves as $leave)
                            <tr>
                                <td class="px-6 py-4 text-sm text-stone-800">
                                    {{ $leave->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-stone-800">
                                    <span class="font-bold">{{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d M Y') }}</span>
                                    <span class="text-xs text-gray-500 block">({{ $leave->total_hari }} Hari Kerja)</span>
                                </td>
                                <td class="px-6 py-4 text-sm capitalize text-stone-800">
                                    {{ $leave->jenis_cuti }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                    {{ $leave->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($leave->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                      ($leave->status == 'cancelled' ? 'bg-stone-800 text-white' : 
                                      ($leave->status == 'pending' ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-800'))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $leave->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('leaves.show', $leave->id) }}" class="text-blue-600 hover:underline font-bold">Lihat Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-8">
                    {{ $leaves->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>