<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Pengajuan Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- FILTER SECTION -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('leaves.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <!-- Filter Jenis -->
                    <select name="jenis_cuti" class="border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Jenis</option>
                        <option value="tahunan" {{ request('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                        <option value="sakit" {{ request('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    </select>

                    <!-- Filter Status -->
                    <select name="status" class="border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved_leader" {{ request('status') == 'approved_leader' ? 'selected' : '' }}>ACC Leader</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    <!-- Filter Bulan -->
                    <select name="bulan" class="border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Bulan</option>
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                        @endfor
                    </select>

                    <!-- Filter Tahun -->
                    <select name="tahun" class="border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Tahun</option>
                        @for($y=date('Y'); $y>=date('Y')-2; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700 font-bold">
                        Filter Data
                    </button>
                </form>
            </div>

            <!-- TABEL DATA -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tgl Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Periode Cuti</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Jenis</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($leaves as $leave)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $leave->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d M') }} - 
                                    {{ \Carbon\Carbon::parse($leave->tanggal_selesai)->format('d M Y') }}
                                    <span class="text-xs text-gray-500 block">({{ $leave->total_hari }} Hari)</span>
                                </td>
                                <td class="px-6 py-4 text-sm capitalize text-gray-700 dark:text-gray-300">
                                    {{ $leave->jenis_cuti }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full 
                                    {{ $leave->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($leave->status == 'pending' ? 'bg-gray-100 text-gray-800' : 
                                      ($leave->status == 'rejected' ? 'bg-red-100 text-red-800' : 
                                      ($leave->status == 'cancelled' ? 'bg-black text-white' : 'bg-yellow-100 text-yellow-800'))) }}">
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
                <div class="mt-4">
                    {{ $leaves->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>