<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="p-8 bg-white rounded-[30px] max-w-sm w-full" 
         style="box-shadow: 0 10px 20px rgba(0,0,0,0.05), 0 0 0 1px #f0f0f0; border: none; background-color: #FFFFFF; transition: all 0.3s ease;">
        
        <div class="flex flex-col items-center justify-center mb-6">
            <a href="/" class="flex items-center gap-2">
                <svg fill="#000000" width="80px" height="80px" viewBox="0 0 24 24" id="calendar-event" data-name="Line Color" xmlns="http://www.w3.org/2000/svg" class="icon line-color">
                    <path id="primary" d="M21,19l-2,2V19ZM20,4H4A1,1,0,0,0,3,5V20a1,1,0,0,0,1,1H19l2-2V5A1,1,0,0,0,20,4Z" style="fill: none; stroke: rgb(0, 0, 0); stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path>
                    <path id="secondary" d="M21,5V9H3V5A1,1,0,0,1,4,4H20A1,1,0,0,1,21,5ZM17,3V5M12,3V5M7,3V5" style="fill: none; stroke: rgb(253, 167, 105); stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path>
                </svg>   

                <span class="text-2xl font-bold text-stone-800">Sistem Cuti</span>
            </a>
            <p class="text-sm text-gray-500 mt-2">Masuk untuk mengelola pengajuan cuti</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block font-medium text-sm text-gray-700">
                    Email / Username
                </label>
                <input id="email" 
                       class="block mt-1 w-full focus:ring-amber-500 rounded-xl py-2.5 px-4 text-stone-700 focus:border-none focus:ring-2" 
                       type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                       placeholder="Masukan email kantor..." 
                       style="border-radius: 12px; border: 1px solid #e5e7eb; background-color: #F8F8F8;" /> 
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mb-4">
                <label for="password" class="block font-medium text-sm text-gray-700">
                    Password
                </label>
                <input id="password" 
                       class="block mt-1 w-full focus:ring-amber-500 rounded-xl py-2.5 px-4 text-stone-700 focus:border-none focus:ring-2" 
                       type="password" name="password" required autocomplete="current-password" 
                       placeholder="********" 
                       style="border-radius: 12px; border: 1px solid #e5e7eb; background-color: #F8F8F8;" /> 
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

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

            <div class="mt-6 pb-2">
                <button type="submit" 
                        class="w-full py-3 text-sm font-bold text-white rounded-xl shadow-lg transition"
                        style="background-color: #FDA769; box-shadow: 0 6px 12px -2px rgba(253, 167, 105, 0.5); border: none; font-size: 15px;">
                    {{ __('Masuk Sekarang') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>