<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Room;


class TransactionController extends Controller
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function index(Request $request)
    {
        $transactions = $this->transactionRepository->getTransactionPagination($request)->simplePaginate(3);

        // Pindahan Dashboard :

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

         //Check in check out dan cleaned by Date

        // // Ambil filter dari query string, default ke 'check_in'
        // $filter = $request->input('filter', 'check_in');
        // $today = Carbon::today()->format('Y-m-d');
        // $filterData = null;;

        // $perPage = 10;

        // // Filter data berdasarkan pilihan user
        // if ($filter === 'check_in') {
        //     $filterData = Transaction::whereDate('checked_in_time', $today)->get();
        // } elseif ($filter === 'check_out') {
        //     $filterData = Transaction::whereDate('checked_out_time', $today)->get();
        // } elseif ($filter === 'cleaned') {
        //     $filterData = Transaction::whereDate('cleaned_time', $today)->get();
        // }

        $filter = $request->input('filter', 'check_in');
        $today = Carbon::today()->format('Y-m-d');
        $perPage = 10; // Atur jumlah item per halaman

        // Filter data berdasarkan pilihan user
        if ($filter === 'check_in') {
            $filterData = Transaction::whereDate('checked_in_time', $today)
                ->with('room') // Pastikan relasi room dimuat
                ->paginate($perPage)
                ->appends($request->all()); // Menjaga query string saat pagination
        } elseif ($filter === 'check_out') {
            $filterData = Transaction::whereDate('checked_out_time', $today)
                ->with('room') // Pastikan relasi room dimuat
                ->paginate($perPage)
                ->appends($request->all());
        } elseif ($filter === 'cleaned') {
            $filterData = Transaction::whereDate('cleaned_time', $today)
                ->with('room') // Pastikan relasi room dimuat
                ->paginate($perPage)
                ->appends($request->all());
        } else {
            $filterData = Transaction::with('room')->paginate($perPage); // Jika filter tidak dikenali
        }



        return view('transaction.index', [
            'transactions' => $transactions,
            'occupiedRooms' => $occupiedRooms, // Kamar yang sedang terisi
            'allRooms' => $allRooms, // Semua kamar
            'date' => $date, // Kirim tanggal ke view
            'filterData' => $filterData, // Data check-out,clean,checkin by date
            'filter' => $filter, // Data check-in hari ini
        ]);
    }

    public function show(Transaction $transaction)
    {
        return view('transaction.show', [
            // Anda bisa mengirimkan data transaksi jika perlu
            'transaction' => $transaction,
        ]);
    }

    public function changeRoomStatus(Request $request, Transaction $transaction)
    {
        // Validate the incoming request
        $request->validate([
            'room_status' => 'required|string',
        ]);

        // Get the current room status
        $currentStatus = $transaction->room_status;

        // Update the room_status field in the transaction
        $transaction->room_status = $request->input('room_status');

        // If the room status is 'check-in', update the checked_in_time
        if ($transaction->room_status === 'check-in') {
            $transaction->checked_in_time = Carbon::now('Asia/Jakarta'); // Set to current time in WIB
            $transaction->checked_in_by = auth()->user()->id;
        }

        // If the room status is 'check-in', update the checked_in_time
        if ($transaction->room_status === 'transfer') {
            $transaction->status = 'Transfer'; // Set to current time in WIB
        }

        // If the room status is 'checkout', update the checked_out_time
        if ($transaction->room_status === 'check-out') {
            $transaction->checked_out_time = Carbon::now('Asia/Jakarta'); // Set to current time in WIB
            $transaction->checked_out_by = auth()->user()->id;

        }

        // If the room status is 'cleaned', update the cleaned_time and set status to 'available'
        if ($transaction->room_status === 'cleaned') {
            $transaction->cleaned_time = Carbon::now('Asia/Jakarta'); // Set to current time in WIB
            $transaction->cleaned_by = auth()->user()->id;
            $transaction->room_status = 'available'; // Set status to available
            $transaction->status = 'Done';
        }

        // Save the transaction
        $transaction->save();

        // Redirect to a suitable route (like a transaction details view)
        return redirect()->route('transaction.index')
                        ->with('success', 'Room status updated successfully.');
    }



    public function showChangeRoomStatusForm(Transaction $transaction)
    {
        return view('transaction.changeRoomStatus', [
            'transaction' => $transaction,
        ]);
    }


    public function history()
    {
        $transactions = Transaction::with(['user', 'customer', 'room', 'payment'])->get();
        $transactions = Transaction::query()->simplePaginate(10);

        return view('transaction.history', compact('transactions'));
    }

    public function filter(Request $request)
    {
        // Dapatkan semua transaksi atau hanya yang expired
        $transactions = Transaction::query();

        if ($request->filterExpired == 'expired') {
            $transactions = $transactions->where('check_out', '<', now());
        }
        elseif ($request->filterExpired == 'current') {
            // Menampilkan transaksi yang saat ini sedang berlangsung
            $transactions = $transactions->where('check_in', '<=', now())
                                        ->where('check_out', '>=', now());
        }

        // Gunakan pagination langsung tanpa get()
        // $transactions = $transactions->simplePaginate(6);
        $transactions = Transaction::query()->simplePaginate(10);

        return view('transaction.history', compact('transactions'));
    }

}
