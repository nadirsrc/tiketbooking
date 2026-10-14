<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Data Tiket Event</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-4">
            <a href="{{ route('events.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow transition">
                + Tambah Tiket
            </a>
            <a href="{{ route('dashboard') }}" class="bg-gray-800 hover:bg-gray-700 text-white font-semibold px-4 py-2 rounded shadow transition text-sm">
                Ke Dashboard Order
            </a>
        </div>

        {{-- Notifikasi Sukses --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Notifikasi Error --}}
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 uppercase text-xs border-b">
                        <th class="p-4 text-center">Gambar</th>
                        <th class="p-4">Nama Tiket</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Harga Tiket</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @forelse($events as $event)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 text-center">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" class="w-16 h-16 object-cover rounded mx-auto border">
                            @else
                                <span class="text-gray-400 text-xs italic">Tanpa Gambar</span>
                            @endif
                        </td>
                        <td class="p-3 font-semibold text-gray-800">{{ $event->title }}</td>
                        <td class="p-3">
                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">
                                {{ $event->category }}
                            </span>
                        </td>
                        <td class="p-3 font-bold text-green-600">Rp {{ number_format($event->ticket_price) }}</td>
                        <td class="p-3 text-center space-x-2">
                            <a href="{{ route('events.edit', $event->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">Edit</a>
                            <span class="text-gray-300">|</span>
                            <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500">Belum ada data tiket event.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $events->links() }}
        </div>
    </div>
</x-app-layout>