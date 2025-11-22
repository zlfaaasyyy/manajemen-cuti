<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Divisi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Tampilkan Error Validasi -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
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
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nama Divisi</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <!-- Ketua Divisi -->
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Ketua Divisi</label>
                        <select name="ketua_divisi_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700">
                            <option value="">-- Pilih Ketua --</option>
                            
                            <!-- PERBAIKAN: Menggunakan $availableLeaders sesuai controller -->
                            @foreach($availableLeaders as $leader)
                                <option value="{{ $leader->id }}" {{ old('ketua_divisi_id') == $leader->id ? 'selected' : '' }}>
                                    {{ $leader->name }} ({{ $leader->email }})
                                </option>
                            @endforeach

                        </select>
                        <p class="text-sm text-gray-500 mt-1">*Hanya user dengan role 'Ketua Divisi' yang belum memimpin divisi lain yang muncul.</p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('deskripsi') }}</textarea>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('divisi.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 mr-4">Batal</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Simpan Divisi
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>