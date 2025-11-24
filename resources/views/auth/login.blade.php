<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Card Container (Menggunakan background F8F8F8 dari theme) -->
    <div class="p-8 bg-white shadow-2xl rounded-[30px] border border-gray-100 max-w-sm w-full" style="box-shadow: 0 15px 30px rgba(0,0,0,0.1); background-color: #FFFFFF;">
        
        <div class="flex flex-col items-center justify-center mb-6">
            <a href="/" class="flex items-center gap-2">
                <!-- Logo: Menggunakan warna Hijau Lumut (#ABC270) -->
                <x-application-logo class="w-12 h-12 fill-current" style="color: #ABC270;" /> 
                <span class="text-2xl font-bold text-stone-800">Sistem Cuti</span>
            </a>
            <p class="text-sm text-gray-500 mt-2">Masuk untuk mengelola pengajuan cuti</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block font-medium text-sm text-gray-700">
                    Email / Username
                </label>
                <input id="email" 
                       class="block mt-1 w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm py-2.5 px-4 text-stone-700" 
                       type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                       placeholder="Masukan email kantor..." 
                       style="border-radius: 12px; border-color: #f0f0f0;" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block font-medium text-sm text-gray-700">
                    Password
                </label>
                <input id="password" 
                       class="block mt-1 w-full border-gray-300 focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm py-2.5 px-4 text-stone-700" 
                       type="password" name="password" required autocomplete="current-password" 
                       placeholder="********" 
                       style="border-radius: 12px; border-color: #f0f0f0;" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mt-4 mb-6">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-amber-600 shadow-sm focus:ring-amber-500" name="remember">
                    <span class="ms-2 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-stone-800 transition rounded-md" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <!-- TOMBOL MASUK (ORANGE TERAKOTA #FDA769) -->
            <div class="mt-6 pb-2">
                <button type="submit" 
                        class="w-full py-3 text-sm font-bold text-white rounded-xl shadow-lg hover:opacity-90 transition"
                        style="background-color: #FDA769; box-shadow: 0 6px 12px -2px rgba(253, 167, 105, 0.5); border: none; font-size: 15px;">
                    {{ __('Masuk Sekarang') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>