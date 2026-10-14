<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Menampilkan katalog tiket event untuk pelanggan publik (Halaman Utama Ticket Box).
     */
    public function index()
    {
        // Mengambil semua data master tiket event dari database untuk ditampilkan di etalase pembeli
        $events = Event::all();

        // Mengirimkan variabel $events ke view customer di resources/views/customer/index.blade.php
        return view('customer.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Memproses checkout pemesanan tiket oleh pelanggan dan menyimpannya secara atomik ke 2 tabel (orders & order_details).
     */
    public function store(Request $request)
    {
        // 1. Validasi Keamanan dan Integritas Input Pemesan
        // - customer_name: nama pemesan wajib string dan maksimal 255 karakter
        // - customer_email: alamat email pemesan wajib format email valid
        // - items: array tiket yang dipilih pemesan (key = event_id, value = jumlah tiket)
        // - items.*: nilai jumlah tiket per event boleh null/kosong atau angka positif minimal 0
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'items' => 'required|array',
            'items.*' => 'nullable|integer|min:0',
        ]);

        // 2. Filter Kuantitas Pesanan
        // Menyaring hanya tiket event yang jumlah pemesanannya lebih besar dari 0
        // Tiket yang diinput 0 atau dibiarkan kosong otomatis dibuang dari array proses
        $orderedItems = array_filter($request->items, fn ($qty) => $qty > 0);

        // Jika tidak ada satu pun tiket yang kuantitasnya > 0, tolak proses dan kembalikan dengan pesan error
        if (empty($orderedItems)) {
            return back()->with('error', 'Pilih minimal satu tiket event!');
        }

        // 3. Memulai Sesi Database Transaction
        // Bertujuan menjamin prinsip ACID (Atomicity, Consistency, Isolation, Durability).
        // Jika pembuatan data pesanan sukses namun server mengalami kegagalan saat menyimpan rincian tiket,
        // seluruh rangkaian query akan dibatalkan otomatis via rollBack() sehingga database tetap bersih dan konsisten.
        DB::beginTransaction();
        try {
            // A. Simpan Header Pesanan ke tabel 'orders'
            // Nilai total_price diinisialisasi 0 terlebih dahulu sebelum diakumulasikan lewat perhitungan loop di bawah
            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'total_price' => 0,
                'status' => 'Pending', // Status awal sesuai enum tabel: Pending, Confirmed, Cancelled
            ]);

            $totalPrice = 0;

            // B. Looping setiap event tiket yang dipesan untuk disimpan ke tabel 'order_details'
            foreach ($orderedItems as $eventId => $quantity) {
                // Ambil data harga tiket terkini dari database master events
                $event = Event::findOrFail($eventId);

                // Hitung subtotal: harga satuan tiket dikalikan kuantitas pemesanan
                $subtotal = $event->ticket_price * $quantity;

                // Akumulasikan ke total keseluruhan harga order
                $totalPrice += $subtotal;

                // Simpan baris rincian tiket ke tabel 'order_details'
                OrderDetail::create([
                    'order_id' => $order->id,          // Foreign key menghubungkan ke header order
                    'event_id' => $event->id,          // Foreign key menghubungkan ke master event
                    'quantity' => $quantity,          // Jumlah kuantitas tiket yang dibeli
                    'subtotal' => $subtotal,          // Nominal subtotal untuk item ini
                ]);
            }

            // C. Perbarui total_price pada header tabel 'orders' setelah semua kalkulasi item selesai
            $order->update(['total_price' => $totalPrice]);

            // D. Jika seluruh rangkaian penyimpanan berhasil tanpa exception, lakukan commit permanen ke MySQL
            DB::commit();

            return redirect()->route('customer.index')->with('success', 'Pesanan tiket berhasil dibuat, silahkan cek email anda.');
        } catch (\Exception $e) {
            // E. Jika terjadi error di salah satu langkah, batalkan seluruh perubahan (UNDO)
            DB::rollBack();

            return back()->with('error', 'Gagal memproses pesanan tiket: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan rekapitulasi seluruh transaksi pesanan tiket di Dashboard Admin.
     */
    public function adminDashboard()
    {
        // Menggunakan Eager Loading (with) untuk memuat relasi orderDetails dan master event sekaligus.
        // Langkah ini mengeliminasi 'N+1 Query Problem' sehingga query SQL dieksekusi secara efisien dan cepat.
        $orders = Order::with('orderDetails.event')->latest()->get();

        // Mengirimkan data pesanan ke halaman dashboard admin (resources/views/dashboard.blade.php)
        return view('dashboard', compact('orders'));
    }

    /**
     * Memperbarui status transaksi pemesanan tiket oleh Administrator.
     */
    public function updateStatus(Request $request, $id)
    {
        // 1. Validasi input status
        $request->validate([
            'status' => 'required|string',
        ]);

        // 2. Normalisasi teks status (kapitalisasi otomatis) agar selalu klop dengan opsi enum basis data
        $status = ucfirst(strtolower($request->status));
        if (! in_array($status, ['Pending', 'Confirmed', 'Cancelled'])) {
            return back()->with('error', 'Status pesanan tidak valid!');
        }

        // 3. Ambil data pesanan berdasarkan parameter ID dan lakukan pembaruan kolom status
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);

        return back()->with('success', 'Status pesanan #'.$order->id.' berhasil diperbarui!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
