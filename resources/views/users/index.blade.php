<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Manajemen Users') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <!-- Container Lebar -->
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- MAIN CARD: Tabel Data -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <!-- TOOLBAR: Tombol Tambah -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-extrabold text-xl text-stone-800">Daftar Pengguna Aktif</h3>
                    <a href="{{ route('users.create') }}" 
                        class="px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition flex items-center hover:opacity-90" 
                        style="background-color: #FDA769; box-shadow: 0 4px 8px -2px rgba(253, 167, 105, 0.5); border: none; font-size: 15px;">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Tambah User
                    </a>
                </div>

                <!-- TABEL DATA -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead style="background-color: #F8F8F8;">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Divisi</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kuota Cuti</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-800">
                                    <div class="flex items-center">
                                        <!-- Placeholder inisial (Hijau Lumut) -->
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-white font-bold text-xs mr-3" style="background-color: #ABC270;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm capitalize">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                    {{ $user->role == 'admin' ? 'bg-red-100 text-red-800' : 
                                      ($user->role == 'hrd' ? 'bg-orange-100 text-orange-800' : 
                                      ($user->role == 'ketua_divisi' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->divisi->nama ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-stone-800">{{ $user->kuota_cuti }} Hari</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <!-- Tombol Edit (Biru) -->
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 font-bold">Edit</a>
                                    
                                    <!-- Tombol Hapus (Merah) -->
                                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-user-{{ $user->id }}')" class="text-red-600 hover:text-red-800 font-bold">
                                        Hapus
                                    </button>
                                </td>
                            </tr>

                            <!-- MODAL KONFIRMASI HAPUS -->
                            <x-modal name="delete-user-{{ $user->id }}" focusable>
                                <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="p-6">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <h2 class="text-lg font-medium text-gray-900">
                                        Hapus Akun {{ $user->name }}?
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Peringatan: Menghapus akun ini akan menghapus semua data pengajuan cutinya. Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                    
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                        <x-danger-button class="ms-3">Ya, Hapus Akun</x-danger-button>
                                    </div>
                                </form>
                            </x-modal>

                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data user.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>