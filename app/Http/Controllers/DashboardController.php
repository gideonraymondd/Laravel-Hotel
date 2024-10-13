<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Ambil tanggal dari input, gunakan hari ini jika tidak ada input
    //     $date = $request->input('date', Carbon::now()->format('Y-m-d'));

    //     // Mengubah input tanggal menjadi objek Carbon sesuai timezone Jakarta
    //     $selectedDate = Carbon::createFromFormat('Y-m-d', $date, 'Asia/Jakarta');

    //     // Ambil semua transaksi yang statusnya "reserved" pada tanggal yang dipilih
    //     $occupiedRooms = Transaction::where('status', 'Reservation')
    //         ->where(function ($query) use ($selectedDate) {
    //             $query->whereDate('check_in', '<=', $selectedDate)
    //                 ->whereDate('check_out', '>=', $selectedDate);
    //         })
    //         ->pluck('room_id'); // Hanya ambil room_id yang sedang ditempati

    //     // Ambil semua kamar dengan pagination
    //     $allRooms = Room::simplePaginate(8); // 8 ruangan per halaman

    //      // Ambil filter dari query string, default ke 'check_in'
    //     $filter = $request->input('filter', 'check_in');
    //     $today = Carbon::today()->format('Y-m-d');
    //     $filterData = null;

    //     // Filter data berdasarkan pilihan user
    //     if ($filter === 'check_in') {
    //         $filterData = Transaction::whereDate('checked_in_time', $today)->get();
    //     } elseif ($filter === 'check_out') {
    //         $filterData = Transaction::whereDate('checked_out_time', $today)->get();
    //     } elseif ($filter === 'cleaned') {
    //         $filterData = Transaction::whereDate('cleaned_time', $today)->get();
    //     }


    //     // Kirim variabel transaksi dan kamar kosong ke view
    //     return view('dashboard.index', [
    //         'transactions' => Transaction::with('user', 'room', 'customer')
    //             ->where([['check_in', '<=', Carbon::now()], ['check_out', '>=', Carbon::now()]])
    //             ->orderBy('check_out', 'ASC')
    //             ->orderBy('id', 'DESC')
    //             ->get(),
    //         'occupiedRooms' => $occupiedRooms, // Kamar yang sedang terisi
    //         'allRooms' => $allRooms, // Semua kamar
    //         'date' => $date, // Kirim tanggal ke view
    //         'filter' => $filter, // Data check-in hari ini
    //         'filterData' => $filterData, // Data check-out hari ini
    //         'today' => $today, // Data cleaned hari in        ]);
    //     ]);
    // }

    public function index(Request $request)
{
    //Check in check out dan cleaned by Date

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

    // Room Status By Date

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


    // Reservation Pie chart

    // Ambil periode waktu dari request, default ke '1d'
    $timePeriod = $request->input('timePeriod', '1d');
    $dataForPieChart = ['checkin' => 0, 'checkout' => 0, 'reserved' => 0];

    // Hitung data berdasarkan periode yang dipilih
    if ($timePeriod === '1d') {
        // Ambil data untuk hari ini
        $dataForPieChart['checkin'] = Transaction::whereDate('checked_in_time', now('Asia/Jakarta')->format('Y-m-d'))->count();
        $dataForPieChart['checkout'] = Transaction::whereDate('checked_out_time', now('Asia/Jakarta')->format('Y-m-d'))->count();
        $dataForPieChart['reserved'] = Transaction::whereDate('created_at', now('Asia/Jakarta')->format('Y-m-d'))->count();
    } elseif ($timePeriod === '1w') {
        // Ambil data untuk 1 minggu terakhir
        $dataForPieChart['checkin'] = Transaction::whereBetween('checked_in_time', [now('Asia/Jakarta')->subDays(7), now('Asia/Jakarta')])->count();
        $dataForPieChart['checkout'] = Transaction::whereBetween('checked_out_time', [now('Asia/Jakarta')->subDays(7), now('Asia/Jakarta')])->count();
        $dataForPieChart['reserved'] = Transaction::whereBetween('created_at', [now('Asia/Jakarta')->subDays(7), now('Asia/Jakarta')])->count();
    } elseif ($timePeriod === '1m') {
        // Ambil data untuk bulan ini
        $dataForPieChart['checkin'] = Transaction::whereMonth('checked_in_time', now('Asia/Jakarta')->month)->count();
        $dataForPieChart['checkout'] = Transaction::whereMonth('checked_out_time', now('Asia/Jakarta')->month)->count();
        $dataForPieChart['reserved'] = Transaction::whereMonth('created_at', now('Asia/Jakarta')->month)->count();
    } elseif ($timePeriod === 'custom') {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        // Ambil data berdasarkan rentang waktu khusus
        $dataForPieChart['checkin'] = Transaction::whereBetween('checked_in_time', [$startDate, $endDate])->count();
        $dataForPieChart['checkout'] = Transaction::whereBetween('checked_out_time', [$startDate, $endDate])->count();
        $dataForPieChart['reserved'] = Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
    }

    // Perbandingan Guest
    // Mendapatkan tanggal untuk bulan ini dan bulan lalu
    $thisMonth = Carbon::now()->month;
    $lastMonth = Carbon::now()->subMonth()->month;

    $thisYear = Carbon::now()->year;
    $lastMonthYear = Carbon::now()->subMonth()->year;

    // Jumlah order bulan ini
    $ordersThisMonth = Transaction::whereMonth('created_at', $thisMonth)
                            ->whereYear('created_at', $thisYear)
                            ->count();

    // Jumlah order bulan lalu
    $ordersLastMonth = Transaction::whereMonth('created_at', $lastMonth)
                            ->whereYear('created_at', $lastMonthYear)
                            ->count();

    // Menghitung selisih jumlah order
    $difference = $ordersThisMonth - $ordersLastMonth;

    // Card

    // Ambil data bulan ini
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();

    // Ambil data bulan lalu
    $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
    $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

    // Total Revenue
    $totalRevenueThisMonth = Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('price');
    $totalRevenueLastMonth = Payment::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('price');
    $revenueDifference = $totalRevenueThisMonth - $totalRevenueLastMonth; // Selisih Revenue

    // Total Customer
    $totalCustomersThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)->count();
    $totalCustomersLastMonth = Customer::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
    $customersDifference = $totalCustomersThisMonth - $totalCustomersLastMonth; // Selisih Customer

    // Total Booking
    $totalBookingsThisMonth = Transaction::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
    $totalBookingsLastMonth = Transaction::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
    $bookingsDifference = $totalBookingsThisMonth - $totalBookingsLastMonth; // Selisih Booking


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
        // 'filter' => $filter, // Data check-in hari ini
        'filterData' => $filterData, // Data check-out,clean,checkin by date
        // 'today' => $today, // Data cleaned hari ini
        'dataForPieChart' => $dataForPieChart, // Kirim data untuk chart ke view
        'timePeriod' => $timePeriod, // Kirim periode waktu ke view
        // Perbanding Guest
        'difference' => $difference,
        // Card Total
        'totalRevenueThisMonth' => $totalRevenueThisMonth,
        'totalRevenueLastMonth' => $totalRevenueLastMonth,
        'totalCustomersThisMonth' => $totalCustomersThisMonth,
        'totalCustomersLastMonth' => $totalCustomersLastMonth,
        'totalBookingsThisMonth' => $totalBookingsThisMonth,
        'totalBookingsLastMonth' => $totalBookingsLastMonth,
        'revenueDifference' => $revenueDifference,
        'customersDifference' => $customersDifference,
        'bookingsDifference' => $bookingsDifference,
    ]);
}


}
