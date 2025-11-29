<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Persetujuan Final Cuti (HRD)') }}
        </h2>
    </x-slot>

    <!-- Alpine Data untuk Bulk Action -->
    <div class="py-12" style="background-color: #F8F8F8;" x-data="{
        selected: [],
        selectAll: false,
        toggleAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                // Mengambil ID dari semua request yang pending
                this.selected = [{{ $pendingRequests->pluck('id')->implode(',') }}];
            } else {
                this.selected = [];
            }
        }
    }">
        <div class="max-w-screen-xl mx-auto sm:px-6 lg:px-8 pb-20"> <!-- Tambah padding bottom agar tidak tertutup toolbar -->
            
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-xl shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-xl shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <!-- HEADER LIST & CHECKBOX SELECT ALL -->
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h3 class="text-xl font-extrabold text-stone-800">
                        Menunggu Persetujuan Final: 
                        <span class="px-3 py-1 rounded-full text-lg font-bold" style="background-color: #FFFBE8; color: #FEC868; border: 1px solid #FEC868;">
                            {{ $pendingRequests->count() }} Pengajuan
                        </span>
                    </h3>
                    
                    <!-- Checkbox Select All -->
                    <div class="mt-4 flex items-center">
                        <input type="checkbox" id="select-all" x-model="selectAll" @click="toggleAll()" 
                               class="w-5 h-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                        <label for="select-all" class="ml-2 text-sm font-bold text-gray-700 cursor-pointer">
                            Pilih Semua ({{ $pendingRequests->count() }})
                        </label>
                    </div>
                </div>
            </div>

            <!-- GRID KARTU PENGAJUAN -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($pendingRequests as $request)
                <div class="bg-white overflow-hidden shadow-2xl rounded-[30px] border border-gray-100 flex flex-col transition hover:shadow-3xl duration-300 relative"
                     style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); border-radius: 24px;">
                    
                    <!-- CHECKBOX INDIVIDU (Pojok Kiri Atas) -->
                    <div class="absolute top-4 left-4 z-10">
                        <input type="checkbox" value="{{ $request->id }}" x-model="selected"
                               class="w-6 h-6 text-amber-600 border-gray-300 rounded focus:ring-amber-500 shadow-md">
                    </div>

                    <!-- Label Asal Pengajuan (Pojok Kanan Atas) -->
                    <div class="absolute top-3 right-3">
                        @if($request->status == 'approved_leader')
                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full border border-amber-200 shadow-sm" style="background-color: #FFFBE8;">
                                ✓ Via Leader
                            </span>
                        @else
                            <span class="bg-stone-100 text-stone-700 text-xs font-bold px-3 py-1 rounded-full border border-stone-200 shadow-sm" style="background-color: #F8F8F8;">
                                ⚡ Direct
                            </span>
                        @endif
                    </div>

                    <!-- INFO USER -->
                    <div class="p-5 flex items-center space-x-4 border-b mt-8" style="background-color: #F8F8F8; border-color: #F0F0F0;">
                        <div class="flex-shrink-0">
                            @if($request->user->foto_profil)
                                <img class="h-14 w-14 rounded-full object-cover border-2 border-white shadow-sm" src="{{ Storage::url($request->user->foto_profil) }}" alt="{{ $request->user->name }}">
                            @else
                                <div class="h-14 w-14 rounded-full flex items-center justify-center text-white font-bold text-xl border-2 border-white shadow-sm" style="background-color: #ABC270;">
                                    {{ substr($request->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="text-md font-extrabold text-stone-800 truncate" title="{{ $request->user->name }}">{{ $request->user->name }}</h4>
                            <p class="text-xs text-gray-500 truncate">{{ $request->user->divisi->nama ?? 'Tanpa Divisi' }}</p>
                        </div>
                    </div>

                    <!-- Detail Cuti -->
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full border 
                                {{ $request->jenis_cuti == 'sakit' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200' }}"
                                style="{{ $request->jenis_cuti == 'tahunan' ? 'background-color: #ECF2E1; color: #ABC270; border-color: #ABC270;' : '' }}">
                                {{ $request->jenis_cuti }}
                            </span>
                            <span class="text-sm font-extrabold text-stone-800">{{ $request->total_hari }} Hari Kerja</span>
                        </div>
                        
                        <div class="mb-4 text-sm text-gray-600 p-3 rounded-xl border border-amber-200" style="background-color: #FFFBE8;">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs text-gray-500">Periode:</span>
                                <span class="font-semibold text-amber-700">{{ \Carbon\Carbon::parse($request->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($request->tanggal_selesai)->format('d M Y') }}</span>
                            </div>
                        </div>

                        <p class="text-xs font-bold text-gray-500 mb-1">Alasan:</p>
                        <div class="text-sm text-stone-800 italic mb-4 flex-1">
                            "{{ Str::limit($request->alasan, 50) }}"
                        </div>
                    </div>

                    <!-- Tombol Aksi Individual -->
                    <div class="p-5 border-t" style="border-color: #F0F0F0; background-color: #F8F8F8;">
                        <div class="grid grid-cols-2 gap-3">
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'hrd-reject-modal-{{ $request->id }}')" 
                                class="w-full py-2 px-3 border border-red-500 text-red-600 rounded-xl hover:bg-red-50 text-xs font-bold transition">
                                Tolak Final
                            </button>
                            <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'hrd-approve-modal-{{ $request->id }}')" 
                                class="w-full py-2 px-3 text-white rounded-xl text-xs font-bold transition shadow-md" style="background-color: #FDA769;">
                                Approve Final
                            </button>
                        </div>
                    </div>
                </div>

                <!-- MODAL INDIVIDUAL -->
                <x-modal name="hrd-approve-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('hrd.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <h2 class="text-xl font-bold text-stone-800">Approve Pengajuan Ini?</h2>
                        <p class="mt-2 text-sm text-gray-600">User: {{ $request->user->name }}</p>
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            <button type="submit" class="ms-3 px-4 py-2 bg-green-600 text-white rounded-md font-bold text-xs uppercase">Approve</button>
                        </div>
                    </form>
                </x-modal>

                <x-modal name="hrd-reject-modal-{{ $request->id }}" focusable>
                    <form method="POST" action="{{ route('hrd.leaves.action', $request->id) }}" class="p-6">
                        @csrf
                        <input type="hidden" name="action" value="reject">
                        <h2 class="text-xl font-bold text-red-600">Tolak Pengajuan Ini?</h2>
                        <div class="mt-4">
                            <label class="block text-sm font-bold text-gray-700">Alasan Penolakan (Min. 10 Karakter)</label>
                            <textarea name="catatan" class="w-full border-gray-300 rounded-md mt-1" required minlength="10"></textarea>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                            <x-danger-button class="ms-3">Tolak</x-danger-button>
                        </div>
                    </form>
                </x-modal>
                @empty
                <div class="col-span-full py-10 text-center text-gray-500">Tidak ada pengajuan pending.</div>
                @endforelse
            </div>

            <!-- TOOLBAR BULK ACTION (Fixed Bottom) -->
            <div x-show="selected.length > 0" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-10"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="fixed bottom-6 left-0 right-0 mx-auto max-w-xl z-50">
                <div class="bg-stone-800 text-white rounded-full shadow-2xl px-6 py-4 flex justify-between items-center border border-stone-700">
                    <span class="font-bold text-sm"><span x-text="selected.length"></span> Item Dipilih</span>
                    <div class="flex space-x-3">
                        <button x-on:click="$dispatch('open-modal', 'bulk-reject-modal')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-full text-xs font-bold transition">
                            Tolak Terpilih
                        </button>
                        <button x-on:click="$dispatch('open-modal', 'bulk-approve-modal')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full text-xs font-bold transition">
                            Approve Terpilih
                        </button>
                    </div>
                </div>
            </div>

            <!-- MODAL BULK APPROVE -->
            <x-modal name="bulk-approve-modal" focusable>
                <form method="POST" action="{{ route('hrd.leaves.bulk_action') }}" class="p-6">
                    @csrf
                    <input type="hidden" name="ids" :value="selected.join(',')">
                    <input type="hidden" name="bulk_action" value="approve">
                    
                    <h2 class="text-xl font-bold text-stone-800">Approve Semua Pengajuan?</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Anda akan menyetujui <span x-text="selected.length" class="font-bold"></span> pengajuan sekaligus.
                    </p>
                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                        <button type="submit" class="ms-3 px-4 py-2 bg-green-600 text-white rounded-md font-bold text-xs uppercase">Ya, Approve Semua</button>
                    </div>
                </form>
            </x-modal>

            <!-- MODAL BULK REJECT -->
            <x-modal name="bulk-reject-modal" focusable>
                <form method="POST" action="{{ route('hrd.leaves.bulk_action') }}" class="p-6">
                    @csrf
                    <input type="hidden" name="ids" :value="selected.join(',')">
                    <input type="hidden" name="bulk_action" value="reject">
                    
                    <h2 class="text-xl font-bold text-red-600">Tolak Semua Pengajuan?</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Anda akan menolak <span x-text="selected.length" class="font-bold"></span> pengajuan sekaligus.
                    </p>
                    <div class="mt-4">
                        <label class="block text-sm font-bold text-gray-700">Catatan Penolakan Massal (Min. 10 Karakter)</label>
                        <textarea name="bulk_catatan" class="w-full border-gray-300 rounded-md mt-1" rows="3" required minlength="10" placeholder="Alasan penolakan untuk semua item terpilih..."></textarea>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                        <x-danger-button class="ms-3">Ya, Tolak Semua</x-danger-button>
                    </div>
                </form>
            </x-modal>

        </div>
    </div>
</x-app-layout>