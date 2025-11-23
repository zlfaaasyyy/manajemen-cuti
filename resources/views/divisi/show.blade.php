<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Divisi: ') . $divisi->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Info Divisi -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $divisi->nama }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $divisi->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Ketua Divisi</p>
                        <p class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $divisi->ketuaDivisi->name ?? 'Belum Ada' }}</p>
                        <p class="text-xs text-gray-400">Dibentuk: {{ $divisi->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Daftar Anggota -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Daftar Anggota Divisi ({{ $divisi->users->count() }} Orang)</h4>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bergabung</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($divisi->users as $member)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-xs mr-3">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $member->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm capitalize text-gray-500 dark:text-gray-400">{{ str_replace('_', ' ', $member->role) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $member->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada anggota di divisi ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <a href="{{ route('divisi.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline">← Kembali ke Daftar Divisi</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>