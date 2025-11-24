<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- BAGIAN 1: INFORMASI KUOTA (Khusus User & Ketua Divisi) -->
            @if($quotaInfo)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Total -->
                <div class="bg-blue-500 rounded-lg shadow-lg p-6 text-white flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-bold uppercase">Total Kuota Tahunan</p>
                        <h3 class="text-3xl font-extrabold mt-1">{{ $quotaInfo['total'] }} <span class="text-sm font-normal">Hari</span></h3>
                    </div>
                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <!-- Terpakai -->
                <div class="bg-yellow-500 rounded-lg shadow-lg p-6 text-white flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-bold uppercase">Kuota Terpakai</p>
                        <h3 class="text-3xl font-extrabold mt-1">{{ $quotaInfo['terpakai'] }} <span class="text-sm font-normal">Hari</span></h3>
                    </div>
                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <!-- Sisa -->
                <div class="bg-green-600 rounded-lg shadow-lg p-6 text-white flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-bold uppercase">Sisa Kuota Saat Ini</p>
                        <h3 class="text-3xl font-extrabold mt-1">{{ $quotaInfo['sisa'] }} <span class="text-sm font-normal">Hari</span></h3>
                    </div>
                    <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            @endif

            <!-- BAGIAN 2: EDIT PROFIL -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Informasi Profil') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Perbarui informasi profil, alamat, dan nomor telepon akun Anda.
                        </p>
                    </header>

                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <!-- Foto Profil -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Foto Profil</label>
                            <div class="flex items-center space-x-4">
                                @if($user->foto_profil)
                                    <img src="{{ Storage::url($user->foto_profil) }}" alt="Foto Profil" class="w-20 h-20 rounded-full object-cover border-2 border-gray-200">
                                @else
                                    <div class="w-20 h-20 rounded-full bg-indigo-500 flex items-center justify-center text-white text-2xl font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                @endif
                                <input type="file" name="foto_profil" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('foto_profil')" />
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" :value="old('name', $user->name)" required autofocus autocomplete="name" 
                                {{ $user->role !== 'admin' ? 'readonly' : '' }} /> <!-- Readonly kecuali Admin -->
                            @if($user->role !== 'admin')
                                <p class="text-xs text-gray-500 mt-1">Hubungi Admin untuk mengubah Nama.</p>
                            @endif
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-100 cursor-not-allowed" :value="old('email', $user->email)" required autocomplete="username" 
                                {{ $user->role !== 'admin' ? 'readonly' : '' }} />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Role & Divisi (Readonly All) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label :value="__('Role')" />
                                <x-text-input type="text" class="mt-1 block w-full bg-gray-200 cursor-not-allowed" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" readonly />
                            </div>
                            <div>
                                <x-input-label :value="__('Divisi')" />
                                <x-text-input type="text" class="mt-1 block w-full bg-gray-200 cursor-not-allowed" value="{{ $user->divisi->nama ?? '-' }}" readonly />
                            </div>
                        </div>

                        <!-- Nomor Telepon (Editable) -->
                        <div>
                            <x-input-label for="nomor_telepon" :value="__('Nomor Telepon')" />
                            <x-text-input id="nomor_telepon" name="nomor_telepon" type="text" class="mt-1 block w-full" :value="old('nomor_telepon', $user->nomor_telepon)" placeholder="Contoh: 08123456789" />
                            <x-input-error class="mt-2" :messages="$errors->get('nomor_telepon')" />
                        </div>

                        <!-- Alamat (Editable) -->
                        <div>
                            <x-input-label for="alamat" :value="__('Alamat')" />
                            <textarea id="alamat" name="alamat" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="3">{{ old('alamat', $user->alamat) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('alamat')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                            @if (session('status') === 'profile-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-green-600 font-bold">{{ __('Berhasil Disimpan.') }}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- BAGIAN 3: UPDATE PASSWORD -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- BAGIAN 4: DELETE USER -->
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>