<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Verifikasi Pengajuan Cuti (Ketua Divisi)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <h3 class="text-xl font-bold mb-4 dark:text-white">Pengajuan dari Bawahan ({{ $pendingRequests->count() }} Pending)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                @forelse($pendingRequests as $request)
                <div class="bg-white dark:bg-gray-800 p-6 shadow-lg sm:rounded-lg border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500">{{ $request->user->name }} ({{ $request->user->divisi->nama ?? 'Tanpa Divisi' }})</p>
                    <h4 class="text-lg font-semibold dark:text-white mt-1">{{ ucfirst($request->jenis_cuti) }} ({{ $request->total_hari }} Hari)</h4>
                    <p class="text-sm dark:text-gray-300">Periode: {{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y') }}</p>
                    
                    <div class="mt-4 border-t pt-3">
                        <p class="font-medium dark:text-white">Alasan:</p>
                        <p class="text-sm italic dark:text-gray-400">{{ $request->alasan }}</p>

                        @if($request->jenis_cuti == 'sakit')
                            <p class="text-xs text-red-500 mt-2">Wajib cek Surat Dokter</p>
                            @endif
                    </div>
                    
                    <form action="{{ route('leader.leaves.action', $request->id) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-sm dark:text-white mb-1">Catatan Verifikasi (Opsional)</label>
                            <textarea name="catatan" rows="2" class="w-full border rounded text-sm text-gray-700"></textarea>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button type="submit" name="action" value="approve" 
                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-sm w-1/2"
                                onclick="return confirm('Setujui pengajuan cuti ini?')">
                                Approve
                            </button>
                            <button type="submit" name="action" value="reject" 
                                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm w-1/2"
                                onclick="return confirm('Tolak pengajuan cuti ini? Wajib isi Catatan Penolakan.')">
                                Reject
                            </button>
                        </div>
                    </form>
                </div>
                @empty
                <p class="dark:text-gray-400">Tidak ada pengajuan cuti pending dari bawahan.</p>
                @endforelse
            </div>
            
        </div>
    </div>
</x-app-layout>