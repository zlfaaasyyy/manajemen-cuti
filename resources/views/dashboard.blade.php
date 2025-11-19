<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Anda berhasil login!") }}
                    
                    <div class="mt-6 space-y-3">
                        
                        @if(auth()->user()->role === 'user' || auth()->user()->role === 'ketua_divisi')
                            <a href="{{ route('leaves.create') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                Ajukan Cuti Sekarang
                            </a>
                        @endif
                        
                        @if(auth()->user()->role === 'ketua_divisi')
                            <a href="{{ route('leader.leaves.index') }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                                Verifikasi Cuti Bawahan
                            </a>
                        @endif

                        @if(auth()->user()->role === 'hrd')
                            <a href="#" class="inline-block bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-4 rounded">
                                Verifikasi Final HRD
                            </a>
                        @endif

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('divisi.index') }}" class="inline-block bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded">
                                Manajemen Divisi & Users
                            </a>
                        @endif
                        
                    </div>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>