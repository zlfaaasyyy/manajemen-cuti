<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Form Pengajuan Cuti') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #F8F8F8;">
        <!-- LEBAR KONTEN MENYESUAIKAN DESKTOP -->
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Error Handling Global -->
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-md rounded-lg">
                    <p class="font-bold">Gagal Mengirim Pengajuan:</p>
                    <ul class="list-disc ml-5 mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- MAIN CARD (Aesthetic Baru + Sudut Melengkung + Shadow Kuat) -->
            <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] p-8 border border-gray-100" style="box-shadow: 0 15px 30px rgba(0,0,0,0.15);">
                <form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- HEADER INFO (Hijau Lumut - ABC270) -->
                    <div class="flex items-center justify-between mb-8 p-4 rounded-xl" style="background-color: #ABC270; color: #473C33;">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest">Tanggal Pengajuan</p>
                            <p class="text-xl font-extrabold mt-1">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                        </div>
                        <div class="text-right">
                             <p class="text-xs font-bold uppercase tracking-widest">Sisa Kuota</p>
                             <p class="text-3xl font-extrabold">{{ auth()->user()->kuota_cuti }} <span class="text-sm font-medium">Hari</span></p>
                        </div>
                    </div>

                    <!-- 1. JENIS CUTI -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold mb-2 text-stone-800">Jenis Cuti <span class="text-red-500">*</span></label>
                        <select name="jenis_cuti" id="jenis_cuti" 
                                class="w-full border-gray-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring-amber-500 text-stone-700 py-3" 
                                onchange="cekJenisCuti()" style="border-radius: 12px; border-color: #f0f0f0;">
                            <option value="tahunan" {{ old('jenis_cuti') == 'tahunan' ? 'selected' : '' }}>Cuti Tahunan</option>
                            <option value="sakit" {{ old('jenis_cuti') == 'sakit' ? 'selected' : '' }}>Cuti Sakit (Wajib Surat Dokter)</option>
                        </select>
                    </div>

                    <!-- 2. DATE PICKER (Range) -->
                    <div class="grid grid-cols-2 gap-4 mb-2">
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" 
                                   class="w-full border-gray-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring-amber-500 text-stone-700 py-2.5" 
                                   required onchange="hitungHari()" style="border-radius: 12px; border-color: #f0f0f0;">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" 
                                   class="w-full border-gray-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring-amber-500 text-stone-700 py-2.5" 
                                   required onchange="hitungHari()" style="border-radius: 12px; border-color: #f0f0f0;">
                        </div>
                    </div>

                    <!-- CALCULATION RESULT (FEC868 - Kuning Muda) -->
                    <div class="mb-6 flex items-center p-3 rounded-xl border" style="background-color: #FFFBE8; border-color: #FEC868;">
                        <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm font-bold text-amber-800">
                            Total Durasi Cuti: <span id="total_hari" class="text-2xl font-extrabold mx-1">0</span> Hari Kerja 
                        </p>
                        <p id="error_msg" class="text-xs text-red-600 ml-auto hidden font-bold"></p>
                    </div>

                    <!-- 3. ALASAN -->
                    <div class="mb-5">
                        <label class="block text-sm font-bold mb-2 text-stone-800">Alasan Cuti <span class="text-red-500">*</span></label>
                        <textarea name="alasan" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring-amber-500 text-stone-700" 
                                  rows="3" required placeholder="Jelaskan alasan pengajuan cuti Anda..." style="border-radius: 12px; border-color: #f0f0f0;">{{ old('alasan') }}</textarea>
                    </div>

                    <!-- 4. ALAMAT & KONTAK DARURAT -->
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Alamat Selama Cuti <span class="text-red-500">*</span></label>
                            <textarea name="alamat_selama_cuti" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring-amber-500 text-stone-700" 
                                      rows="3" required placeholder="Alamat lengkap..." style="border-radius: 12px; border-color: #f0f0f0;">{{ old('alamat_selama_cuti') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2 text-stone-800">Nomor Telepon Darurat <span class="text-red-500">*</span></label>
                            <input type="text" name="nomor_darurat" value="{{ old('nomor_darurat') }}" 
                                   class="w-full border-gray-300 rounded-xl shadow-sm focus:border-amber-500 focus:ring-amber-500 text-stone-700 py-2.5" 
                                   required placeholder="08xxxxxxxxx" style="border-radius: 12px; border-color: #f0f0f0;">
                            <p class="text-xs text-gray-500 mt-1">Nomor kerabat yang bisa dihubungi.</p>
                        </div>
                    </div>

                    <!-- 5. UPLOAD SURAT DOKTER (Conditional) -->
                    <div class="mb-8 hidden" id="upload_surat">
                        <label class="block text-sm font-bold mb-2 text-stone-800">
                            Upload Surat Keterangan Dokter <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label for="bukti_sakit" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100" style="border-radius: 12px;">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik upload</span> atau drag and drop</p>
                                    <p class="text-xs text-gray-500">PDF, PNG, JPG (Max. 2MB)</p>
                                </div>
                                <input id="bukti_sakit" name="bukti_sakit" type="file" class="hidden" />
                            </label>
                        </div> 
                    </div>

                    <!-- TOMBOL AKSI (Submit, Reset, Cancel) -->
                    <div class="flex items-center justify-end space-x-3 border-t pt-6" style="border-color: #f0f0f0;">
                        <!-- Tombol Cancel -->
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-medium text-stone-800 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition" style="border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            Cancel
                        </a>

                        <!-- Tombol Reset -->
                        <button type="reset" class="px-5 py-2.5 text-sm font-medium text-stone-800 bg-amber-200 rounded-xl hover:bg-amber-300 focus:ring-4 focus:outline-none focus:ring-amber-100 transition" style="border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            Reset
                        </button>

                        <!-- Tombol Submit (FDA769 - Orange Terakota) -->
                        <button type="submit" id="submit_btn" 
                                class="px-6 py-3 text-sm font-bold text-white rounded-xl shadow-lg transition" 
                                style="background-color: #FDA769; box-shadow: 0 6px 12px -2px rgba(253, 167, 105, 0.5); border: none; font-size: 15px;">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC (Sama seperti sebelumnya) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            cekJenisCuti();
            hitungHari(); 
        });

        function cekJenisCuti() {
            let jenis = document.getElementById('jenis_cuti').value;
            let uploadDiv = document.getElementById('upload_surat');
            
            if (jenis === 'sakit') {
                uploadDiv.classList.remove('hidden');
                document.getElementById('bukti_sakit').required = true;
            } else {
                uploadDiv.classList.add('hidden');
                document.getElementById('bukti_sakit').value = ''; // Reset file
                document.getElementById('bukti_sakit').required = false;
            }
        }

        function hitungHari() {
            let start = document.getElementById('tanggal_mulai').value;
            let end = document.getElementById('tanggal_selesai').value;
            let output = document.getElementById('total_hari');
            let errorMsg = document.getElementById('error_msg');
            let submitBtn = document.getElementById('submit_btn');

            if (start && end) {
                let startDate = new Date(start);
                let endDate = new Date(end);
                
                if (startDate > endDate) {
                    output.innerText = "0";
                    errorMsg.innerText = "Error: Tanggal Selesai < Tanggal Mulai";
                    errorMsg.classList.remove('hidden');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    return;
                }

                let count = 0;
                let curDate = new Date(startDate);
                while (curDate <= endDate) {
                    let dayOfWeek = curDate.getDay();
                    if (dayOfWeek !== 0 && dayOfWeek !== 6) { 
                        count++;
                    }
                    curDate.setDate(curDate.getDate() + 1);
                }

                output.innerText = count;
                errorMsg.classList.add('hidden');
                
                if (count === 0) {
                    errorMsg.innerText = "Hanya berisi hari libur.";
                    errorMsg.classList.remove('hidden');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                output.innerText = "0";
            }
        }
    </script>
</x-app-layout>