<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Seluruh Cuti Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Divisi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                        @foreach($leaves as $leave)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">{{ $leave->user->name }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $leave->user->divisi->nama ?? '-' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 capitalize">{{ $leave->jenis_cuti }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($leave->tanggal_mulai)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-2">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $leave->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                      ($leave->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>