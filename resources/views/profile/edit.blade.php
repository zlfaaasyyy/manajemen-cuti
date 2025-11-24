<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <!-- LEBAR KONTEN MAKSIMAL AGAR TAMPILAN DESKTOP NYAMAN -->
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- BAGIAN 1: INFORMASI KUOTA (Khusus User & Ketua Divisi) -->
            @if($quotaInfo)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total (COKELAT GELAP #473C33) -->
                <div class="rounded-[24px] shadow-2xl p-6 text-white flex items-center justify-between transition hover:scale-[1.01] duration-200" style="background-color: #473C33; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">
                    <div>
                        <p class="text-gray-300 text-sm font-bold uppercase tracking-widest">Total Kuota Tahunan</p>
                        <h3 class="text-4xl font-extrabold mt-1" style="color: #FEC868;">{{ $quotaInfo['total'] }} <span class="text-base font-normal">Hari</span></h3>
                    </div>
                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #FEC868;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                
                <!-- Terpakai (ORANGE TERAKOTA #FDA769) -->
                <div class="rounded-[24px] shadow-2xl p-6 text-white flex items-center justify-between transition hover:scale-[1.01] duration-200" style="background-color: #FDA769; box-shadow: 0 10px 20px rgba(253, 167, 105, 0.4);">
                    <div>
                        <p class="text-orange-100 text-sm font-bold uppercase tracking-widest">Kuota Terpakai</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ $quotaInfo['terpakai'] }} <span class="text-base font-normal">Hari</span></h3>
                    </div>
                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <!-- Sisa (HIJAU LUMUT #ABC270) -->
                <div class="rounded-[24px] shadow-2xl p-6 text-white flex items-center justify-between transition hover:scale-[1.01] duration-200" style="background-color: #ABC270; box-shadow: 0 10px 20px rgba(171, 194, 112, 0.4);">
                    <div>
                        <p class="text-green-100 text-sm font-bold uppercase tracking-widest">Sisa Kuota Saat Ini</p>
                        <h3 class="text-4xl font-extrabold mt-1">{{ $quotaInfo['sisa'] }} <span class="text-base font-normal">Hari</span></h3>
                    </div>
                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            @endif

            <!-- BAGIAN 2: EDIT PROFIL -->
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-2xl rounded-[30px] border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-xl font-bold text-stone-800">
                            {{ __('Informasi Profil') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Perbarui informasi profil, alamat, dan nomor telepon akun Anda.
                        </p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <!-- Foto Profil -->
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-2">Foto Profil</label>
                            <div class="flex items-center space-x-4">
                                @if($user->foto_profil)
                                    <img src="{{ Storage::url($user->foto_profil) }}" alt="Foto Profil" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 shadow-md">
                                @else
                                    <!-- Placeholder sesuai tema (HIJAU LUMUT) -->
                                    <div class="w-20 h-20 rounded-full flex items-center justify-center text-white text-2xl font-extrabold shadow-md" style="background-color: #ABC270;">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <input type="file" name="foto_profil" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition" style="border-radius: 12px;">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('foto_profil')" />
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" :class="{'bg-gray-100 cursor-not-allowed': $user->role !== 'admin', 'border-gray-300': true}" :value="old('name', $user->name)" required autofocus autocomplete="name" 
                                {{ $user->role !== 'admin' ? 'readonly' : '' }} style="border-radius: 12px; border-color: #f0f0f0;" />
                            @if($user->role !== 'admin')
                                <p class="text-xs text-gray-500 mt-1">Hubungi Admin untuk mengubah Nama.</p>
                            @endif
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl" :class="{'bg-gray-100 cursor-not-allowed': $user->role !== 'admin', 'border-gray-300': true}" :value="old('email', $user->email)" required autocomplete="username" 
                                {{ $user->role !== 'admin' ? 'readonly' : '' }} style="border-radius: 12px; border-color: #f0f0f0;" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Role & Divisi (Readonly All) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Role')" />
                                <x-text-input type="text" class="mt-1 block w-full bg-gray-200 cursor-not-allowed rounded-xl" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" readonly style="border-radius: 12px; border-color: #f0f0f0;" />
                            </div>
                            <div>
                                <x-input-label :value="__('Divisi')" />
                                <x-text-input type="text" class="mt-1 block w-full bg-gray-200 cursor-not-allowed rounded-xl" value="{{ $user->divisi->nama ?? '-' }}" readonly style="border-radius: 12px; border-color: #f0f0f0;" />
                            </div>
                        </div>

                        <!-- Nomor Telepon (Editable) -->
                        <div>
                            <x-input-label for="nomor_telepon" :value="__('Nomor Telepon')" />
                            <x-text-input id="nomor_telepon" name="nomor_telepon" type="text" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500" :value="old('nomor_telepon', $user->nomor_telepon)" placeholder="Contoh: 08123456789" style="border-radius: 12px; border-color: #f0f0f0;" />
                            <x-input-error class="mt-2" :messages="$errors->get('nomor_telepon')" />
                        </div>

                        <!-- Alamat (Editable) -->
                        <div>
                            <x-input-label for="alamat" :value="__('Alamat')" />
                            <textarea id="alamat" name="alamat" class="mt-1 block w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm" rows="3" style="border-radius: 12px; border-color: #f0f0f0;">{{ old('alamat', $user->alamat) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
                        </div>

                        <div class="flex items-center gap-4 pt-4 border-t" style="border-color: #f0f0f0;">
                            <!-- Tombol Simpan (HIJAU LUMUT #ABC270) -->
                            <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white rounded-xl shadow-lg transition" 
                                    style="background-color: #ABC270; box-shadow: 0 4px 8px -2px rgba(171, 194, 112, 0.7);">
                                {{ __('Simpan Perubahan') }}
                            </button>

                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-bold">{{ __('Berhasil Disimpan.') }}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- BAGIAN 3: UPDATE PASSWORD -->
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-2xl rounded-[30px] border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- BAGIAN 4: DELETE USER -->
            <div class="p-4 sm:p-8 bg-white overflow-hidden shadow-2xl rounded-[30px] border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.05);">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>