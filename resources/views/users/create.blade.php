<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Tambah User Baru') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <!-- Container Lebar untuk form -->
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Card -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <h3 class="text-2xl font-extrabold text-stone-800 mb-6">Informasi Akun Karyawan</h3>
                
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
                
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- SECTION 1: DATA DASAR AKUN -->
                    <p class="text-sm font-bold text-gray-500 mb-3 border-b pb-2" style="border-color: #f0f0f0;">Data Login & Personal</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        
                        <!-- NAMA -->
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #f0f0f0;">
                        </div>
                        
                        <!-- EMAIL -->
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Email (Username)</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #f0f0f0;">
                        </div>

                        <!-- PASSWORD -->
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Password</label>
                            <input type="password" name="password" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #f0f0f0;">
                        </div>

                        <!-- KONFIRMASI PASSWORD -->
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #f0f0f0;">
                        </div>
                    </div>
                    
                    <!-- SECTION 2: ROLE & DIVISI -->
                    <p class="text-sm font-bold text-gray-500 mb-3 border-b pb-2" style="border-color: #f0f0f0;">Penugasan & Kuota Cuti</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <!-- ROLE -->
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Role</label>
                            <select name="role" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 capitalize focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #f0f0f0;">
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', $role) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-red-500 mt-1">Pastikan HRD hanya ada 1.</p>
                        </div>

                        <!-- DIVISI -->
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Divisi</label>
                            <select name="divisi_id" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                                <option value="">-- Tidak Ada Divisi --</option>
                                @foreach($divisis as $divisi)
                                    <option value="{{ $divisi->id }}" {{ old('divisi_id') == $divisi->id ? 'selected' : '' }}>
                                        {{ $divisi->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Jika Role Ketua, Divisi harus dipilih & belum ada Ketua.</p>
                        </div>
                        
                        <!-- KUOTA CUTI -->
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Kuota Cuti (Hari)</label>
                            <input type="number" name="kuota_cuti" value="{{ old('kuota_cuti', 12) }}" min="0" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                            <p class="text-xs text-gray-500 mt-1">Default 12 hari untuk User/Ketua.</p>
                        </div>
                    </div>

                    
                    <!-- TOMBOL AKSI (Submit - HIJAU LUMUT #ABC270) -->
                    <div class="mt-8 flex justify-end space-x-3 border-t pt-6" style="border-color: #f0f0f0;">
                        <a href="{{ route('users.index') }}" class="px-5 py-2.5 text-sm font-medium text-stone-800 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 transition" style="border-radius: 12px;">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition" 
                                style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7); border-radius: 12px;">
                            Simpan User Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>