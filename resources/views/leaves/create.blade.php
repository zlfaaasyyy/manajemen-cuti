<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Form Pengajuan Cuti
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
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

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Jenis Cuti</label>
                        <select name="jenis_cuti" id="jenis_cuti" class="w-full border rounded px-3 py-2" onchange="cekJenisCuti()">
                            <option value="tahunan">Cuti Tahunan (Kuota: {{ auth()->user()->kuota_cuti }})</option>
                            <option value="sakit">Cuti Sakit</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2 dark:text-white">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="w-full border rounded px-3 py-2" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Alasan Cuti</label>
                        <textarea name="alasan" class="w-full border rounded px-3 py-2" rows="3" required></textarea>
                    </div>

                    <div class="mb-4 hidden" id="upload_surat">
                        <label class="block text-sm font-bold mb-2 dark:text-white">Upload Surat Dokter (PDF/JPG)</label>
                        <input type="file" name="bukti_sakit" class="w-full border rounded px-3 py-2 bg-gray-100">
                    </div>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 w-full">
                        Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function cekJenisCuti() {
            let jenis = document.getElementById('jenis_cuti').value;
            let uploadDiv = document.getElementById('upload_surat');
            if (jenis === 'sakit') {
                uploadDiv.classList.remove('hidden');
            } else {
                uploadDiv.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>