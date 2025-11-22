<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Divisi: ') . $divisi->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Tampilkan Error Jika Ada -->
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form action="{{ route('divisi.update', $divisi->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nama Divisi -->
                    <div class="mb-4">
                        <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Divisi</label>
                        <input type="text" name="nama" id="nama" value="{{ old('nama', $divisi->nama) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    
                    <!-- Ketua Divisi Dropdown -->
                    <div class="mb-4">
                        <label for="ketua_divisi_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ketua Divisi</label>
                        <select name="ketua_divisi_id" id="ketua_divisi_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="">-- Pilih Ketua --</option>
                            <!-- $availableLeaders dikirim dari DivisiController -->
                            @foreach($availableLeaders as $leader)
                                <option value="{{ $leader->id }}" {{ old('ketua_divisi_id', $divisi->ketua_divisi_id) == $leader->id ? 'selected' : '' }}>
                                    {{ $leader->name }} ({{ $leader->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Hanya user dengan role 'Ketua Divisi' yang bisa dipilih, dan tidak sedang memimpin divisi lain.</p>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('deskripsi', $divisi->deskripsi) }}</textarea>
                    </div>
                    
                    <!-- Tombol Aksi -->
                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('divisi.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-3">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>