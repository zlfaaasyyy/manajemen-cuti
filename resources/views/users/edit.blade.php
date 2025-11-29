<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Edit User') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <!-- Container Lebar untuk form -->
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Card -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <h3 class="text-2xl font-extrabold text-stone-800 mb-6">Edit Informasi Akun</h3>

                <!-- Error Handling -->
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- Inisialisasi x-data dengan role user saat ini -->
                <form action="{{ route('users.update', $user->id) }}" method="POST" x-data="{ role: '{{ old('role', $user->role) }}' }">
                    @csrf
                    @method('PUT')

                    <!-- SECTION 1: DATA DASAR AKUN -->
                    <div class="border-b pb-4 mb-6" style="border-color: #f0f0f0;">
                        <p class="text-sm font-bold text-gray-500">Data Login & Personal</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- NAMA -->
                        <div>
                            <label for="name" class="block text-sm font-bold mb-2 text-stone-800">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #e0e0e0;">
                        </div>
                        
                        <!-- EMAIL -->
                        <div>
                            <label for="email" class="block text-sm font-bold mb-2 text-stone-800">Email (Username)</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #e0e0e0;">
                        </div>
                    </div>

                    <!-- SECTION 2: ROLE & DIVISI -->
                    <div class="border-b pb-4 mb-6" style="border-color: #f0f0f0;">
                        <p class="text-sm font-bold text-gray-500">Penugasan & Kuota Cuti</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- ROLE -->
                        <div>
                            <label for="role" class="block text-sm font-bold mb-2 text-stone-800">Role</label>
                            <select name="role" id="role" x-model="role" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 capitalize focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #e0e0e0;">
                                @foreach($roles as $roleOption)
                                    <option value="{{ $roleOption }}" {{ old('role', $user->role) == $roleOption ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $roleOption) }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- Pesan Bantuan -->
                            <p class="text-xs text-gray-400 mt-1 italic" x-show="role === 'admin' || role === 'hrd'" style="display: none;">
                                *Admin/HRD tidak butuh kuota.
                            </p>
                        </div>

                        <!-- DIVISI -->
                        <div>
                            <label for="divisi_id" class="block text-sm font-bold mb-2 text-stone-800">Divisi</label>
                            <select name="divisi_id" id="divisi_id" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #e0e0e0;">
                                <option value="">-- Tidak Ada / Belum Ditentukan --</option>
                                @foreach($divisis as $d)
                                    <option value="{{ $d->id }}" {{ old('divisi_id', $user->divisi_id) == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Divisi wajib bagi Ketua & User.</p>
                        </div>

                        <!-- KUOTA CUTI -->
                        <div x-show="role === 'user' || role === 'ketua_divisi'" x-transition>
                            <label for="kuota_cuti" class="block text-sm font-bold mb-2 text-stone-800">Kuota Cuti (Hari)</label>
                            <input type="number" id="kuota_cuti" name="kuota_cuti" value="{{ old('kuota_cuti', $user->kuota_cuti) }}" min="0" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #e0e0e0;">
                            <p class="text-xs text-gray-500 mt-1">Otomatis 0 untuk HRD/Admin.</p>
                        </div>
                    </div>
                    
                    <!-- SECTION 3: GANTI PASSWORD (OPSIONAL) -->
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 mb-6">
                        <p class="text-sm font-bold text-stone-800 mb-4">Ganti Password (Opsional)</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-xs font-bold text-gray-500 mb-1">Password Baru</label>
                                <input type="password" id="password" name="password" class="w-full border-gray-300 rounded-xl px-4 py-2 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #e0e0e0;" placeholder="Kosongkan jika tidak ubah">
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-gray-500 mb-1">Konfirmasi Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full border-gray-300 rounded-xl px-4 py-2 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #e0e0e0;">
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-medium text-stone-800 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition" style="border-radius: 12px;">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition transform hover:-translate-y-0.5" 
                                style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7); border-radius: 12px;">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>