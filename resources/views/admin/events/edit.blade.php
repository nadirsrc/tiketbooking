<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Tiket Event</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        {{-- Notifikasi Error --}}
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block font-medium">Nama Tiket</label>
                <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Kategori</label>
                <select name="category" class="w-full border p-2 rounded" required>
                    <option value="Musik" {{ old('category', $event->category) == 'Musik' ? 'selected' : '' }}>Musik</option>
                    <option value="Festival" {{ old('category', $event->category) == 'Festival' ? 'selected' : '' }}>Festival</option>
                    <option value="Seminar" {{ old('category', $event->category) == 'Seminar' ? 'selected' : '' }}>Seminar</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Harga Tiket</label>
                <input type="number" name="ticket_price" value="{{ old('ticket_price', $event->ticket_price) }}" class="w-full border p-2 rounded" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Deskripsi</label>
                <textarea name="description" class="w-full border p-2 rounded" required>{{ old('description', $event->description) }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Gambar Baru (Opsional)</label>
                @if($event->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $event->image) }}" class="w-20 h-20 object-cover rounded border">
                    </div>
                @endif
                <input type="file" name="image" class="w-full border p-2 rounded">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded transition">Update</button>
            <a href="{{ route('events.index') }}" class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded transition inline-block">Kembali</a>
        </form>
    </div>
</x-app-layout>