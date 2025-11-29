<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Tambah User Baru') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <h3 class="text-2xl font-extrabold text-stone-800 mb-6">Informasi Akun Karyawan</h3>
                
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-sm">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('users.store') }}" method="POST" x-data="{ role: '{{ old('role', 'user') }}' }">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div>
                            <label for="name" class="block text-sm font-bold mb-2 text-stone-800">Nama Lengkap</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus 
                                   class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                        </div>

                        <div>
                            <label for="username" class="block text-sm font-bold mb-2 text-stone-800">Username</label>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required 
                                   class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                            @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-bold mb-2 text-stone-800">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                   class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-bold mb-2 text-stone-800">Role / Jabatan</label>
                            <select name="role" id="role" required x-model="role"
                                    class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                                @foreach($roles as $r)
                                    <option value="{{ $r }}" {{ old('role') == $r ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $r)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="role === 'ketua_divisi' || role === 'user'" class="transition-all duration-300">
                            <label for="divisi_id" class="block text-sm font-bold mb-2 text-stone-800">Divisi</label>
                            <select name="divisi_id" id="divisi_id" 
                                    class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                                <option value="" {{ old('divisi_id') === null ? 'selected' : '' }}>
                                    -- Pilih Divisi (Opsional) --
                                </option>
                                
                                @foreach($divisis as $divisi)
                                    @php
                                        $isOccupied = $divisi->ketua_divisi_id ? 'true' : 'false';
                                        $isCurrentValue = old('divisi_id') == $divisi->id ? 'true' : 'false';
                                    @endphp

                                    <option value="{{ $divisi->id }}" 
                                            {{ $isCurrentValue === 'true' ? 'selected' : '' }}
                                            
                                            x-bind:hidden="role === 'ketua_divisi' && {{ $isOccupied }} && !{{ $isCurrentValue }}"
                                            
                                            x-bind:disabled="role !== 'ketua_divisi' && {{ $isOccupied }}"
                                            >
                                        {{ $divisi->nama }} 
                                        @if($divisi->ketua_divisi_id)
                                            (Sudah ada Ketua)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <p x-show="role === 'ketua_divisi'" class="text-xs text-red-500 mt-1">Hanya Divisi yang belum memiliki Ketua yang ditampilkan.</p>
                            <p x-show="role === 'user'" class="text-xs text-gray-500 mt-1">Pilih Divisi, atau biarkan kosong jika belum ditentukan.</p>
                        </div>
                        
                        <div x-show="role === 'user' || role === 'ketua_divisi'" class="transition-all duration-300">
                            <label for="kuota_cuti" class="block text-sm font-bold mb-2 text-stone-800">Kuota Cuti (Hari)</label>
                            <input type="number" name="kuota_cuti" id="kuota_cuti" value="{{ old('kuota_cuti', 12) }}" min="0" 
                                   class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                            <p class="text-xs text-gray-500 mt-1">Default 12 hari untuk User/Ketua.</p>
                        </div>
                        
                        <div class="md:col-span-2 border-t pt-6 mt-4" style="border-color: #f0f0f0;">
                            <p class="text-sm font-bold text-stone-800 mb-4">Set Password Awal</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-bold mb-2 text-stone-800">Password Baru</label>
                                    <input type="password" name="password" id="password" required 
                                        class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-bold mb-2 text-stone-800">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" required 
                                        class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" style="border-radius: 12px; border-color: #f0f0f0;">
                                </div>
                            </div>
                        </div>
                    </div>

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