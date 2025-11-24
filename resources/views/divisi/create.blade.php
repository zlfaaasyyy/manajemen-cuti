<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Tambah Divisi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <!-- Container Lebar untuk form -->
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Card -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                
                <h3 class="text-2xl font-extrabold text-stone-800 mb-6">Detail Divisi Baru</h3>
                
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
                
                <form method="POST" action="{{ route('divisi.store') }}">
                    @csrf

                    <!-- Nama Divisi -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold mb-2 text-stone-800">Nama Divisi <span class="text-red-500">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #f0f0f0;">
                    </div>

                    <!-- Ketua Divisi Dropdown -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold mb-2 text-stone-800">Ketua Divisi <span class="text-red-500">*</span></label>
                        <select name="ketua_divisi_id" class="w-full border-gray-300 rounded-xl px-4 py-2.5 text-stone-700 focus:border-amber-500 focus:ring-amber-500 shadow-sm" required style="border-radius: 12px; border-color: #f0f0f0;">
                            <option value="">-- Pilih Ketua --</option>
                            
                            @foreach($availableLeaders as $leader)
                                <option value="{{ $leader->id }}" {{ old('ketua_divisi_id') == $leader->id ? 'selected' : '' }}>
                                    {{ $leader->name }} ({{ $leader->email }})
                                </option>
                            @endforeach

                        </select>
                        <p class="text-xs text-gray-500 mt-1">*Hanya user dengan role 'Ketua Divisi' yang belum memimpin divisi lain yang muncul.</p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold mb-2 text-stone-800">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="3" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring-amber-500 text-stone-700" style="border-radius: 12px; border-color: #f0f0f0;">{{ old('deskripsi') }}</textarea>
                    </div>

                    
                    <!-- TOMBOL AKSI (Submit - HIJAU LUMUT #ABC270) -->
                    <div class="mt-8 flex justify-end space-x-3 border-t pt-6" style="border-color: #f0f0f0;">
                        <a href="{{ route('divisi.index') }}" class="px-5 py-2.5 text-sm font-medium text-stone-800 bg-gray-50 border border-gray-300 rounded-xl hover:bg-gray-100 transition" style="border-radius: 12px;">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition" 
                                style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7); border-radius: 12px;">
                            Simpan Divisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>