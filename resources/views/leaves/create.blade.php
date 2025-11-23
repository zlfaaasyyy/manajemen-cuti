<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Form Pengajuan Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <p class="font-bold">Gagal Mengirim Pengajuan:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Jenis Cuti -->
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Jenis Cuti</label>
                        <select name="jenis_cuti" id="jenis_cuti" class="w-full border rounded px-3 py-2 text-gray-700 dark:text-gray-300 dark:bg-gray-700" onchange="cekJenisCuti()">
                            <option value="tahunan" {{ old('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>
                                Cuti Tahunan (Sisa Kuota: {{ auth()->user()->kuota_cuti }} Hari)
                            </option>
                            <option value="sakit" {{ old('jenis_cuti') == 'sakit' ? 'selected' : '' }}>
                                Cuti Sakit (Wajib Surat Dokter)
                            </option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            @if(auth()->user()->role === 'ketua_divisi')
                                Pengajuan Anda akan langsung ke HRD.
                            @else
                                Pengajuan Anda akan diverifikasi oleh Ketua Divisi dahulu.
                            @endif
                        </p>
                    </div>

                    <!-- Tanggal Mulai & Selesai -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="w-full border rounded px-3 py-2 text-gray-700 dark:text-gray-300 dark:bg-gray-700" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full border rounded px-3 py-2 text-gray-700 dark:text-gray-300 dark:bg-gray-700" required>
                        </div>
                    </div>

                    <!-- Alasan Cuti -->
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Alasan Cuti</label>
                        <textarea name="alasan" class="w-full border rounded px-3 py-2 text-gray-700 dark:text-gray-300 dark:bg-gray-700" rows="3" required placeholder="Contoh: Menghadiri acara keluarga / Pemulihan pasca operasi">{{ old('alasan') }}</textarea>
                    </div>

                    <!-- BARU: Alamat Selama Cuti (Sesuai Soal) -->
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Alamat Selama Cuti</label>
                        <textarea name="alamat_selama_cuti" class="w-full border rounded px-3 py-2 text-gray-700 dark:text-gray-300 dark:bg-gray-700" rows="2" required placeholder="Alamat lengkap tempat Anda berada saat cuti">{{ old('alamat_selama_cuti') }}</textarea>
                    </div>

                    <!-- BARU: Nomor Darurat (Sesuai Soal) -->
                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Nomor Telepon Darurat</label>
                        <input type="text" name="nomor_darurat" value="{{ old('nomor_darurat') }}" class="w-full border rounded px-3 py-2 text-gray-700 dark:text-gray-300 dark:bg-gray-700" required placeholder="Nomor kontak yang dapat dihubungi saat darurat">
                    </div>

                    <!-- Upload Surat Dokter (Tersembunyi Awalnya) -->
                    <div class="mb-4 {{ old('jenis_cuti') == 'sakit' ? '' : 'hidden' }}" id="upload_surat">
                        <label class="block text-sm font-bold mb-2 dark:text-red-400">Upload Surat Dokter (Wajib - PDF/JPG)</label>
                        <input type="file" name="bukti_sakit" class="w-full border rounded px-3 py-2 bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                        <p class="text-xs text-red-500 mt-1">Surat dokter wajib diunggah. Maksimal 2MB.</p>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 font-bold">
                            Reset Form
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-bold">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Panggil saat halaman dimuat (untuk old('jenis_cuti'))
            cekJenisCuti();
        });

        function cekJenisCuti() {
            let jenis = document.getElementById('jenis_cuti').value;
            let uploadDiv = document.getElementById('upload_surat');
            
            if (jenis === 'sakit') {
                uploadDiv.classList.remove('hidden');
                // Tidak perlu required di JS, sudah di-handle di Controller
            } else {
                uploadDiv.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>