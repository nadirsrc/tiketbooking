<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Menampilkan daftar master data tiket event untuk administrator.
     * Menggunakan pagination 10 item per halaman untuk efisiensi beban memori.
     */
    public function index()
    {
        // Mengambil data event terbaru (LIFO) dengan batasan 10 baris per halaman
        $events = Event::latest()->paginate(10);

        // Menampilkan view tabel master tiket di folder resources/views/admin/events/index.blade.php
        return view('admin.events.index', compact('events'));
    }

    /**
     * Menampilkan formulir pembuatan tiket event baru.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Menyimpan data tiket event baru yang diinput administrator ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Keamanan dan Tipe Data Input
        // - title: wajib diisi, teks maksimal 255 karakter
        // - category: wajib dan dibatasi hanya pada opsi enum (Musik, Festival, Seminar)
        // - ticket_price: wajib angka numerik dan bernilai minimal 0
        // - description: deskripsi acara wajib diisi
        // - image: opsional, harus berupa file gambar valid (jpeg, png, jpg) dengan batas maksimal 2048 KB (2MB)
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Musik,Festival,Seminar',
            'ticket_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // 2. Pemrosesan Unggah Gambar Poster
            // Jika administrator mengunggah berkas gambar, simpan berkas ke storage/app/public/events
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
            }

            // 3. Simpan Entitas Event Baru ke Tabel 'events'
            Event::create([
                'title' => $request->title,
                'category' => $request->category,
                'ticket_price' => $request->ticket_price,
                'description' => $request->description,
                'image' => $imagePath,
            ]);

            // 4. Redirect kembali ke halaman indeks dengan flash message sukses
            return redirect()->route('events.index')->with('success', 'Data tiket berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika terjadi kesalahan sistem atau kegagalan penyimpanan, kembalikan input form beserta pesan error
            return back()->withInput()->with('error', 'Gagal menambahkan tiket: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan detail spesifik dari satu entitas event.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Menampilkan formulir edit data tiket event yang sudah ada.
     * Menggunakan Route-Model Binding untuk otomatis mengambil data berdasarkan parameter ID.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Memperbarui data tiket event yang telah tersimpan di database.
     */
    public function update(Request $request, Event $event)
    {
        // 1. Validasi Keamanan dan Integritas Input Pembaruan
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:Musik,Festival,Seminar',
            'ticket_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // 2. Manajemen Penggantian Berkas Gambar
            // Default path tetap menggunakan gambar lama yang tersimpan pada objek model
            $imagePath = $event->image;

            // Jika ada berkas gambar baru yang diunggah oleh admin:
            if ($request->hasFile('image')) {
                // Hapus berkas fisik gambar lama dari disk public agar media penyimpanan server tidak menumpuk berkas yatim (orphaned files)
                if ($event->image && Storage::disk('public')->exists($event->image)) {
                    Storage::disk('public')->delete($event->image);
                }

                // Simpan berkas gambar baru ke direktori 'events' di disk public
                $imagePath = $request->file('image')->store('events', 'public');
            }

            // 3. Lakukan Eksekusi Pembaruan Data pada Database
            $event->update([
                'title' => $request->title,
                'category' => $request->category,
                'ticket_price' => $request->ticket_price,
                'description' => $request->description,
                'image' => $imagePath,
            ]);

            return redirect()->route('events.index')->with('success', 'Data tiket berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui tiket: '.$e->getMessage());
        }
    }

    /**
     * Menghapus tiket event dari sistem.
     * Seluruh relasi detail pesanan yang terkait akan otomatis terhapus (cascade) pada level database.
     */
    public function destroy(Event $event)
    {
        try {
            // 1. Hapus berkas gambar fisik dari media penyimpanan disk public jika ada
            if ($event->image && Storage::disk('public')->exists($event->image)) {
                Storage::disk('public')->delete($event->image);
            }

            // 2. Hapus baris data event dari tabel MySQL
            $event->delete();

            return redirect()->route('events.index')->with('success', 'Data tiket berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('events.index')->with('error', 'Gagal menghapus tiket: '.$e->getMessage());
        }
    }
}
