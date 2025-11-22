<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Verifikasi Final HRD') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Pesan Sukses -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <h3 class="text-xl font-bold mb-4 dark:text-white">Menunggu Persetujuan Final ({{ $pendingRequests->count() }})</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($pendingRequests as $request)
                <div class="bg-white dark:bg-gray-800 p-6 shadow-lg sm:rounded-lg border-l-4 border-pink-500 relative">
                    
                    <!-- Label Status Asal -->
                    <div class="absolute top-4 right-4">
                        @if($request->status == 'approved_leader')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Dari Ketua Divisi</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">Langsung (Ketua Divisi)</span>
                        @endif
                    </div>

                    <p class="text-lg font-bold text-gray-800 dark:text-white">{{ $request->user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $request->user->role === 'ketua_divisi' ? 'Ketua Divisi' : 'Staff' }} - {{ $request->user->divisi->nama ?? 'Tanpa Divisi' }}</p>
                    
                    <div class="mt-4">
                        <p class="text-sm font-semibold dark:text-gray-300">Jenis Cuti: <span class="font-normal">{{ ucfirst($request->jenis_cuti) }}</span></p>
                        <p class="text-sm font-semibold dark:text-gray-300">Durasi: <span class="font-normal">{{ $request->total_hari }} Hari</span></p>
                        <p class="text-sm font-semibold dark:text-gray-300">Tanggal: <span class="font-normal">{{ $request->tanggal_mulai }} s/d {{ $request->tanggal_selesai }}</span></p>
                        <p class="text-sm font-semibold dark:text-gray-300 mt-2">Alasan:</p>
                        <p class="text-sm italic text-gray-600 dark:text-gray-400">"{{ $request->alasan }}"</p>
                        
                        @if($request->catatan_leader)
                            <div class="mt-2 bg-yellow-50 p-2 rounded border border-yellow-100">
                                <p class="text-xs font-bold text-yellow-700">Catatan Ketua Divisi:</p>
                                <p class="text-xs text-yellow-600">{{ $request->catatan_leader }}</p>
                            </div>
                        @endif

                        @if($request->jenis_cuti == 'sakit' && $request->bukti_sakit)
                            <div class="mt-2">
                                <a href="{{ Storage::url($request->bukti_sakit) }}" target="_blank" class="text-blue-500 hover:underline text-sm">📄 Lihat Surat Dokter</a>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Form Aksi HRD -->
                    <form action="{{ route('hrd.leaves.action', $request->id) }}" method="POST" class="mt-6">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm dark:text-white mb-1">Catatan HRD (Wajib jika tolak)</label>
                            <textarea name="catatan" rows="2" class="w-full border rounded text-sm text-gray-700 p-2" placeholder="Catatan keputusan..."></textarea>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button type="submit" name="action" value="approve" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm w-1/2"
                                onclick="return confirm('Setujui final pengajuan ini?')">
                                Final Approve
                            </button>
                            <button type="submit" name="action" value="reject" 
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm w-1/2"
                                onclick="return confirm('Tolak pengajuan ini? Kuota akan dikembalikan.')">
                                Reject
                            </button>
                        </div>
                    </form>
                </div>
                @empty
                <div class="col-span-2 text-center py-10">
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada pengajuan yang menunggu verifikasi HRD.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>