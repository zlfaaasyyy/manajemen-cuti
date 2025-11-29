<nav x-data="{ open: false }" style="background-color: #473C33;" class="border-b border-gray-700 dark:border-gray-700 shadow-xl">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <!-- Mengganti warna logo menjadi Hijau Lumut -->
                        <svg fill="#000000" width="50px" height="50px" viewBox="0 0 24 24" id="calendar-event" data-name="Line Color" xmlns="http://www.w3.org/2000/svg" class="icon line-color">
                            <path id="primary" d="M21,19l-2,2V19ZM20,4H4A1,1,0,0,0,3,5V20a1,1,0,0,0,1,1H19l2-2V5A1,1,0,0,0,20,4Z" style="fill: none; stroke: rgb(0, 0, 0); stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path>
                            <path id="secondary" d="M21,5V9H3V5A1,1,0,0,1,4,4H20A1,1,0,0,1,21,5ZM17,3V5M12,3V5M7,3V5" style="fill: none; stroke: rgb(253, 167, 105); stroke-linecap: round; stroke-linejoin: round; stroke-width: 2;"></path>
                        </svg> 
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" style="color: #FFF; border-bottom-color: {{ request()->routeIs('dashboard') ? '#ABC270' : 'transparent' }};">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- MENU ADMIN -->
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('divisi.index')" :active="request()->routeIs('divisi.*')" style="color: #FFF; border-bottom-color: {{ request()->routeIs('divisi.*') ? '#ABC270' : 'transparent' }};">
                            {{ __('Manajemen Divisi') }}
                        </x-nav-link>
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" style="color: #FFF; border-bottom-color: {{ request()->routeIs('users.*') ? '#ABC270' : 'transparent' }};">
                            {{ __('Manajemen User') }}
                        </x-nav-link>
                    @endif

                    <!-- MENU KETUA DIVISI -->
                    @if(Auth::user()->role === 'ketua_divisi')
                        <x-nav-link :href="route('leader.leaves.index')" :active="request()->routeIs('leader.leaves.index')" style="color: #FFF; border-bottom-color: {{ request()->routeIs('leader.leaves.index') ? '#ABC270' : 'transparent' }};">
                            {{ __('Verifikasi Leader') }}
                        </x-nav-link>
                    @endif

                    <!-- MENU HRD -->
                    @if(Auth::user()->role === 'hrd')
                        <x-nav-link :href="route('hrd.leaves.index')" :active="request()->routeIs('hrd.leaves.index')" style="color: #FFF; border-bottom-color: {{ request()->routeIs('hrd.leaves.index') ? '#ABC270' : 'transparent' }};">
                            {{ __('Verifikasi HRD') }}
                        </x-nav-link>
                        <x-nav-link :href="route('leaves.report')" :active="request()->routeIs('leaves.report')" style="color: #FFF; border-bottom-color: {{ request()->routeIs('leaves.report') ? '#ABC270' : 'transparent' }};">
                            {{ __('Laporan Cuti') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-transparent hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div> <!-- Tampilkan Nama User -->

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger (Mobile Menu Button) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-200 hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-gray-200 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" style="background-color: #38312B;">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-white hover:bg-gray-700">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- MOBILE MENU ADMIN -->
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('divisi.index')" :active="request()->routeIs('divisi.*')" class="text-white hover:bg-gray-700">
                    {{ __('Manajemen Divisi') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="text-white hover:bg-gray-700">
                    {{ __('Manajemen User') }}
                </x-responsive-nav-link>
            @endif

            <!-- MOBILE MENU KETUA DIVISI -->
            @if(Auth::user()->role === 'ketua_divisi')
                <x-responsive-nav-link :href="route('leader.leaves.index')" :active="request()->routeIs('leader.leaves.index')" class="text-white hover:bg-gray-700">
                    {{ __('Verifikasi Leader') }}
                </x-responsive-nav-link>
            @endif
            
            <!-- MOBILE MENU HRD -->
            @if(Auth::user()->role === 'hrd')
                <x-responsive-nav-link :href="route('hrd.leaves.index')" :active="request()->routeIs('hrd.leaves.index')" class="text-white hover:bg-gray-700">
                    {{ __('Verifikasi HRD') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('leaves.report')" :active="request()->routeIs('leaves.report')" class="text-white hover:bg-gray-700">
                    {{ __('Laporan Cuti') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:bg-gray-700">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-white hover:bg-gray-700">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>