<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-stone-800 leading-tight">
            {{ __('Manajemen Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl shadow-sm flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl shadow-sm flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($usersOnLeave->count() > 0)
            <div class="bg-white overflow-hidden shadow-xl rounded-[30px] border border-gray-100">
                <div class="p-8 bg-white border-b border-gray-100">
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="text-left w-full md:w-auto">
                            <h3 class="text-xl font-extrabold text-stone-800 flex items-center">
                                <span class="bg-amber-100 text-amber-600 p-1.5 rounded-lg mr-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                Karyawan Sedang Cuti Hari Ini
                            </h3>
                            <p class="text-sm text-gray-500 mt-1 ml-11">Daftar karyawan yang sedang tidak tersedia hari ini.</p>
                        </div>
                        <span class="text-xs font-bold bg-amber-50 text-amber-700 py-1 px-3 rounded-full border border-amber-200">
                            Total: {{ $usersOnLeave->count() }} Orang
                        </span>
                    </div>

                    <div class="overflow-hidden rounded-[24px] border border-gray-100 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Divisi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Periode Cuti</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @foreach($usersOnLeave as $user)
                                @php $activeLeave = $user->leaveRequests->first(); @endphp
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm" style="background-color: #FDA769;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-800">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-50 border border-gray-200 text-gray-600">
                                            {{ $user->divisi->nama ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-700 font-medium">
                                            {{ \Carbon\Carbon::parse($activeLeave->tanggal_mulai)->format('d M') }} - 
                                            {{ \Carbon\Carbon::parse($activeLeave->tanggal_selesai)->format('d M Y') }}
                                        </div>
                                        <div class="text-xs text-amber-600 mt-0.5">
                                            Kembali: <strong>{{ \Carbon\Carbon::parse($activeLeave->tanggal_selesai)->addDay()->format('d M') }}</strong>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                            {{ $activeLeave->jenis_cuti }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl rounded-[30px] border border-gray-100">
                <div class="p-8 bg-white border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <div class="text-left w-full sm:w-auto">
                            <h3 class="text-xl font-extrabold text-stone-800">Daftar Pengguna Aktif</h3>
                            <p class="text-sm text-gray-500 mt-1">Kelola data akun karyawan yang aktif bekerja.</p>
                        </div>
                        <div class="w-full sm:w-auto text-right">
                            <a href="{{ route('users.create') }}" class="inline-flex justify-center items-center px-6 py-3 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:shadow-lg transition ease-in-out duration-150 transform hover:-translate-y-1" style="background-color: #FDA769; box-shadow: 0 4px 6px rgba(253, 167, 105, 0.4);">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah User Baru
                            </a>
                        </div>
                    </div>
                    
                    <div class="mb-8 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                            
                            <div>
                                <label for="role" class="block text-xs font-medium text-gray-700 mb-1">Filter Role</label>
                                <select name="role" id="role" class="w-full border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="all">Semua Role</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r }}" {{ request('role') == $r ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $r)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="divisi_id" class="block text-xs font-medium text-gray-700 mb-1">Filter Divisi</label>
                                <select name="divisi_id" id="divisi_id" class="w-full border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="all">Semua Divisi</option>
                                    @foreach($divisis as $d)
                                        <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>
                                            {{ $d->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="sort_by" class="block text-xs font-medium text-gray-700 mb-1">Sortir Berdasarkan</label>
                                <div class="flex space-x-2">
                                    <select name="sort_by" id="sort_by" class="flex-1 border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-amber-500">
                                        <option value="name" {{ request('sort_by', 'name') == 'name' ? 'selected' : '' }}>Nama</option>
                                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tgl Bergabung</option>
                                        <option value="divisi_id" {{ request('sort_by') == 'divisi_id' ? 'selected' : '' }}>Divisi</option>
                                        <option value="kuota_cuti" {{ request('sort_by') == 'kuota_cuti' ? 'selected' : '' }}>Kuota Cuti</option>
                                    </select>
                                    <select name="sort_order" id="sort_order" class="w-20 border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-amber-500">
                                        <option value="asc" {{ request('sort_order', 'asc') == 'asc' ? 'selected' : '' }}>ASC</option>
                                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>DESC</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <button type="submit" class="px-4 py-2 bg-amber-500 text-white font-semibold text-sm rounded-lg hover:bg-amber-600 transition w-full">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                    Filter
                                </button>
                                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-white text-gray-700 font-semibold text-sm rounded-lg border border-gray-300 hover:bg-gray-100 transition w-full text-center">
                                    Reset
                                </a>
                            </div>

                        </form>
                    </div>
                    <div class="overflow-hidden rounded-[24px] border border-gray-100 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama User</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role & Jabatan</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Divisi</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Sisa Kuota</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($users as $user)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-sm" style="background-color: #ABC270;">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-800">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800 border border-red-200' : 
                                              ($user->role === 'hrd' ? 'bg-purple-100 text-purple-800 border border-purple-200' : 
                                              ($user->role === 'ketua_divisi' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-gray-100 text-gray-800 border border-gray-200')) }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        {{ $user->divisi->nama ?? '-' }}
                                    </td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-800 font-bold">
                                        @if($user->role == 'user' || $user->role == 'ketua_divisi')
                                            <span class="{{ $user->kuota_cuti == 0 ? 'text-red-500' : 'text-green-600' }}">
                                                {{ $user->kuota_cuti }} Hari
                                            </span>
                                        @else
                                            <span class="text-gray-300 font-normal text-xs italic">-</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('users.edit', $user->id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </a>
                                            
                                            @if(auth()->user()->role === 'admin' && auth()->id() !== $user->id && ($user->role === 'user' || $user->role === 'ketua_divisi'))
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        Tidak ada data pengguna aktif.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>