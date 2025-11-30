<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Detail Divisi: ') . $divisi->nama }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8">
<<<<<<< HEAD
            
=======
>>>>>>> 34911a05c55ea847ea8129b7d6dfce84fbd27732
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

            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 mb-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                <div class="flex flex-wrap justify-between items-start border-b pb-4 mb-4" style="border-color: #f0f0f0;">
                    <div>
                        <h3 class="text-3xl font-extrabold text-stone-800">{{ $divisi->nama }}</h3>
                        <p class="text-gray-600 mt-1">{{ $divisi->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                    </div>
                    <div class="text-right mt-4 md:mt-0">
                        <p class="text-sm font-bold text-gray-500 uppercase">Ketua Divisi</p>
                        <p class="text-2xl font-extrabold mt-1" style="color: #FDA769;">{{ $divisi->ketuaDivisi->name ?? 'Belum Ada' }}</p>
                        <p class="text-xs text-gray-500">Dibentuk: {{ $divisi->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                
                <div class="flex justify-start space-x-3">
                    <a href="{{ route('divisi.edit', $divisi->id) }}" class="px-4 py-2 text-sm font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition" style="border-radius: 12px;">
                        Edit Divisi
                    </a>
                     <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'delete-divisi-{{ $divisi->id }}')" class="px-4 py-2 text-sm font-bold text-red-600 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition" style="border-radius: 12px;">
                        Hapus Divisi
                    </button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <div class="flex justify-between items-center mb-6 border-b pb-3" style="border-color: #f0f0f0;">
                    <h4 class="text-xl font-extrabold text-stone-800">Daftar Anggota ({{ $divisi->users->count() }} Orang)</h4>
                    
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-member-modal')" 
                            class="px-4 py-2 text-sm font-bold text-white rounded-xl shadow-lg transition flex items-center hover:opacity-90" 
                            style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7); border-radius: 12px;">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Tambah Anggota
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead style="background-color: #F8F8F8;">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Username</th> <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sisa Cuti</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($divisi->users as $member)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-white font-bold text-xs mr-3" style="background-color: #FDA769;">
                                            {{ substr($member->name, 0, 1) }}
                                        </div>
                                        <div class="text-sm font-medium text-stone-800">{{ $member->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->username ?? '-' }}</td> <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm capitalize">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                    {{ $member->role == 'ketua_divisi' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ str_replace('_', ' ', $member->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-stone-800">
                                    {{ $member->kuota_cuti }} Hari
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($divisi->ketua_divisi_id !== $member->id)
                                        <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'remove-member-{{ $member->id }}')" class="text-red-600 hover:text-red-900 font-bold text-xs bg-red-50 hover:bg-red-100 px-3 py-1 rounded-xl transition duration-150 ease-in-out" style="border-radius: 12px;">
                                            Keluarkan
                                        </button>
                                    @else
                                        <span class="text-xs font-bold px-3 py-1 rounded-xl" style="background-color: #ECF2E1; color: #ABC270;">Ketua Divisi</span>
                                    @endif
                                </td>
                            </tr>

                            <x-modal name="remove-member-{{ $member->id }}" focusable>
                                <form method="POST" action="{{ route('divisi.removeMember', $member->id) }}" class="p-6">
                                    @csrf
                                    @method('DELETE')
                                    
                                    <h2 class="text-lg font-medium text-gray-900">
                                        Keluarkan {{ $member->name }}?
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        User ini akan dikeluarkan dari divisi ini (Divisi akan kosong), namun akunnya <strong>TIDAK</strong> akan dihapus dari sistem.
                                    </p>
                                    
                                    <div class="mt-6 flex justify-end">
                                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                                        <x-danger-button class="ms-3">Ya, Keluarkan</x-danger-button>
                                    </div>
                                </form>
                            </x-modal>

                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 italic">Belum ada anggota di divisi ini.</td> </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <a href="{{ route('divisi.index') }}" class="text-stone-800 hover:text-amber-600 flex items-center font-bold text-sm transition">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Daftar Divisi
                    </a>
                </div>
            </div>

            <x-modal name="add-member-modal" focusable>
                <form method="POST" action="{{ route('divisi.addMember', $divisi->id) }}" class="p-6">
                    @csrf
                    
                    <h2 class="text-xl font-bold text-stone-800 mb-4">
                        Tambah Anggota ke Divisi {{ $divisi->nama }}
                    </h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Karyawan</label>
                        <select name="user_id" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring-amber-500 py-2.5 text-stone-700" required style="border-radius: 12px; border-color: #f0f0f0;">
                            <option value="">-- Pilih Karyawan (Tanpa Divisi) --</option>
                            @forelse($potentialMembers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @empty
                                <option value="" disabled>Tidak ada karyawan yang tersedia (Semua sudah punya divisi)</option>
                            @endforelse
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Hanya menampilkan user dengan role 'Karyawan' yang belum memiliki divisi.
                        </p>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                        <button type="submit" class="ms-3 px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition" 
                                style="background-color: #FDA769; box-shadow: 0 4px 8px -2px rgba(253, 167, 105, 0.5); border-radius: 12px;">
                            Tambah Anggota
                        </button>
                    </div>
                </form>
            </x-modal>

            <x-modal name="delete-divisi-{{ $divisi->id }}" focusable>
                <form method="POST" action="{{ route('divisi.destroy', $divisi->id) }}" class="p-6">
                    @csrf
                    @method('DELETE')
                    
                    <h2 class="text-lg font-medium text-red-600">
                        Hapus Divisi {{ $divisi->nama }}?
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Peringatan: Menghapus divisi ini akan **MENGELUARKAN** semua {{ $divisi->users->count() }} anggotanya, dan menghapus relasi ketuanya. Tindakan ini tidak dapat dibatalkan.
                    </p>
                    
                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                        <x-danger-button class="ms-3">Ya, Hapus Divisi</x-danger-button>
                    </div>
                </form>
            </x-modal>

        </div>
    </div>
</x-app-layout>