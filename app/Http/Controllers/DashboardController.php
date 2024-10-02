<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil tanggal dari input, gunakan hari ini jika tidak ada input
        $date = $request->input('date', Carbon::now()->format('Y-m-d'));

        // Mengubah input tanggal menjadi objek Carbon sesuai timezone Jakarta
        $selectedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');

        // Ambil semua transaksi yang statusnya "reserved" pada tanggal yang dipilih
        $occupiedRooms = Transaction::where('status', 'Reservation')
            ->where(function ($query) use ($selectedDate) {
                $query->whereDate('check_in', '<=', $selectedDate)
                    ->whereDate('check_out', '>=', $selectedDate);
            })
            ->pluck('room_id'); // Hanya ambil room_id yang sedang ditempati

        // Ambil semua kamar dengan pagination
        $allRooms = Room::simplePaginate(8); // 8 ruangan per halaman

         // Ambil filter dari query string, default ke 'check_in'
        $filter = $request->input('filter', 'check_in');
        $today = Carbon::today()->format('Y-m-d');
        $filterData = null;

        // Filter data berdasarkan pilihan user
        if ($filter === 'check_in') {
            $filterData = Transaction::whereDate('checked_in_time', $today)->get();
        } elseif ($filter === 'check_out') {
            $filterData = Transaction::whereDate('checked_out_time', $today)->get();
        } elseif ($filter === 'cleaned') {
            $filterData = Transaction::whereDate('cleaned_time', $today)->get();
        }


        // Kirim variabel transaksi dan kamar kosong ke view
        return view('dashboard.index', [
            'transactions' => Transaction::with('user', 'room', 'customer')
                ->where([['check_in', '<=', Carbon::now()], ['check_out', '>=', Carbon::now()]])
                ->orderBy('check_out', 'ASC')
                ->orderBy('id', 'DESC')
                ->get(),
            'occupiedRooms' => $occupiedRooms, // Kamar yang sedang terisi
            'allRooms' => $allRooms, // Semua kamar
            'date' => $date, // Kirim tanggal ke view
            'filter' => $filter, // Data check-in hari ini
            'filterData' => $filterData, // Data check-out hari ini
            'today' => $today, // Data cleaned hari in        ]);
        ]);
    }


}
