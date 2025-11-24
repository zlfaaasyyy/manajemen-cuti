<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Divisi: ') . $divisi->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Notifikasi Sukses/Error -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Info Divisi Card -->
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

            <!-- Daftar Anggota Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Anggota ({{ $divisi->users->count() }} Orang)</h4>
                    
                    <!-- TOMBOL TAMBAH ANGGOTA (Memicu Modal) -->
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-member-modal')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm flex items-center shadow-md transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Tambah Anggota
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bergabung</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <!-- Logika Tombol Keluarkan -->
                                    @if($divisi->ketua_divisi_id !== $member->id)
                                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'remove-member-{{ $member->id }}')" class="text-red-600 hover:text-red-900 font-bold text-xs bg-red-100 hover:bg-red-200 px-3 py-1 rounded transition duration-150 ease-in-out">
                                            Keluarkan
                                        </button>
                                    @else
                                        <span class="text-xs text-blue-500 font-bold bg-blue-100 px-2 py-1 rounded border border-blue-200">Ketua</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- MODAL KONFIRMASI KELUARKAN MEMBER -->
                            <x-modal name="remove-member-{{ $member->id }}" focusable>
                                <form method="POST" action="{{ route('divisi.removeMember', $member->id) }}" class="p-6">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Keluarkan {{ $member->name }}?
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        User ini akan dikeluarkan dari divisi ini, namun akunnya <strong>TIDAK</strong> akan dihapus dari sistem. Status divisinya akan menjadi kosong.
                                    </p>
                                    
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                        <x-danger-button class="ms-3">Ya, Keluarkan</x-danger-button>
                                    </div>
                                </form>
                            </x-modal>

                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">Belum ada anggota di divisi ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <a href="{{ route('divisi.index') }}" class="text-gray-600 dark:text-gray-400 hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Daftar Divisi
                    </a>
                </div>
            </div>

            <!-- MODAL TAMBAH ANGGOTA -->
            <x-modal name="add-member-modal" focusable>
                <form method="POST" action="{{ route('divisi.addMember', $divisi->id) }}" class="p-6">
                    @csrf
                    
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Tambah Anggota ke Divisi {{ $divisi->nama }}
                    </h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Karyawan</label>
                        <select name="user_id" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Pilih Karyawan (Tanpa Divisi) --</option>
                            @forelse($potentialMembers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @empty
                                <option value="" disabled>Tidak ada karyawan yang tersedia (Semua sudah punya divisi)</option>
                            @endforelse
                        </select>
                        <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">
                            Hanya menampilkan user dengan role 'Karyawan' yang belum memiliki divisi.
                        </p>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                        <x-primary-button class="ms-3 bg-blue-600 hover:bg-blue-700">Tambah Anggota</x-primary-button>
                    </div>
                </form>
            </x-modal>

        </div>
    </div>
</x-app-layout>