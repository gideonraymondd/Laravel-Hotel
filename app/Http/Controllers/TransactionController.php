<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Room;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;



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

        // Tentukan tanggal yang dipilih atau gunakan hari ini sebagai default
        $selectedDate = Carbon::Today('Asia/Jakarta');

        // Query untuk mengambil transaksi yang belum expired berdasarkan check_in dan check_out
        $unexpiredTransactions = Transaction::where('status', 'Reservation')
            ->where(function ($query) use ($selectedDate) {
                $query->whereDate('check_in', '<=', $selectedDate)
                    ->whereDate('check_out', '>=', $selectedDate);
            })
            ->get();



        return view('transaction.index', [
            'transactions' => $transactions,
            'occupiedRooms' => $occupiedRooms, // Kamar yang sedang terisi
            'allRooms' => $allRooms, // Semua kamar
            'date' => $date, // Kirim tanggal ke view
            'filterData' => $filterData, // Data check-out,clean,checkin by date
            'filter' => $filter, // Data check-in hari ini
            'unexpiredTransactions'=>$unexpiredTransactions,
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

        return response()->json(['success' => 'Room status updated successfully.']);

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
        $transactions = Transaction::query()->simplePaginate(2);

        return view('transaction.history', compact('transactions'));
    }

    public function getRoomDetails($transactionId)
    {
        // Ambil detail transaksi yang dipilih
        $transaction = Transaction::with('room')->findOrFail($transactionId);

        // Ambil detail kamar
        $roomDetails = [
            'roomNumber' => $transaction->room->number,
            'floorNumber' => $transaction->room->floor,
            'status' => $transaction->room->status,
        ];

        return response()->json($roomDetails);
    }

    // public function storeGroupBooking(Request $request)
    // {
    //     dd($request->all());

    //     $request->validate([
    //         'user_id' => 'required|integer',
    //         'customer_id' => 'required|integer',
    //         'room_ids' => 'required|array', // array kamar yang dipesan
    //         'check_in' => 'required|date',
    //         'check_out' => 'required|date',
    //         'group_note' => 'nullable|string',
    //     ]);

    //     // Format `group_note` dengan `group_booking + customer_id + created_at`
    //     $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
    //     $groupNote = 'group_booking_' . $request->customer_id . '_' . $today;

    //     foreach ($request->room_ids as $room_id) {
    //         Transaction::create([
    //             'user_id' => $request->user_id,
    //             'customer_id' => $request->customer_id,
    //             'room_id' => $room_id,
    //             'check_in' => $request->check_in,
    //             'check_out' => $request->check_out,
    //             'status' => 'booked',
    //             'origin' => 'group_booking', // atau penanda lainnya untuk Group Booking
    //             'group_note' => $groupNote, // menyimpan catatan khusus
    //             'created_at' => Carbon::now('Asia/Jakarta'), // waktu dengan timezone Asia/Jakarta
    //         ]);
    //     }


    //     return redirect()->route('transaction.index')->with('success', 'Group booking berhasil disimpan!');
    // }


    public function storeGroupBooking(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'room_ids' => 'required|array', // Memastikan room_ids terisi
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'group_note' => 'nullable|string',
        ]);

        // Format room_ids jika terpisah koma
        if (is_string($request->room_ids[0])) {
            $request->room_ids = explode(',', $request->room_ids[0]);
        }

        // Format group_note
        $today = Carbon::today('Asia/Jakarta')->format('Y-m-d');
        $groupNote = 'group_booking_' . $request->customer_id . '_' . $today;

        // Array untuk menyimpan ID transaksi yang baru saja dibuat
        $transactionIds = [];

        try {
            DB::beginTransaction(); // Memulai transaksi database

            foreach ($request->room_ids as $room_id) {
                $transaction = Transaction::create([
                    'user_id' => $request->user_id,
                    'customer_id' => $request->customer_id,
                    'room_id' => (int)$room_id, // Pastikan ini adalah ID ruangan tunggal
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'status' => 'booked',
                    'origin' => 'group_booking',
                    'group_note' => $groupNote,
                    'created_at' => Carbon::now('Asia/Jakarta'),
                ]);

                $transactionIds[] = $transaction->id; // Simpan ID transaksi
            }

            DB::commit(); // Menyimpan semua perubahan jika tidak ada error

            // Gabungkan ID transaksi dengan tanda '-'
            $transactionIdString = implode('-', $transactionIds);

            // Redirect ke halaman pembayaran dengan ID transaksi
            return redirect()->route('group.booking.payment', ['transactionId' => $transactionIdString])
                ->with('success', 'Group booking berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika ada error
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }


    public function showGroupBooking(Request $request)
    {
        $checkInDate = $request->input('check_in',Carbon::now()->format('Y-m-d')); // Ambil tanggal check-in dari request
        $checkOutDate = $request->input('check_out',Carbon::now()->format('Y-m-d')); // Ambil tanggal check-out dari request

        // Mengubah input tanggal menjadi objek Carbon sesuai timezone Jakarta
        $selectedDate = Carbon::createFromFormat('Y-m-d', $checkInDate, 'Asia/Jakarta');
        $selectedDate2 = Carbon::createFromFormat('Y-m-d', $checkOutDate, 'Asia/Jakarta');


        // Ambil semua transaksi dalam rentang tanggal yang ditentukan
        $occupiedRooms = Transaction::where(function ($query) use ($selectedDate, $selectedDate2) {
            $query->whereDate('check_in', '<=', $selectedDate)
                ->whereDate('check_out', '>=', $selectedDate2);
        })->pluck('room_id'); // Get the room IDs of occupied rooms


        // Ambil semua kamar yang tidak terpakai
        $availableRooms = Room::whereNotIn('id', $occupiedRooms)->get(); // Ambil kamar yang kosong

        $customers = Customer::all(); // Ambil semua data pelanggan

        return view('transaction.group_booking', compact('availableRooms', 'customers', 'occupiedRooms', 'checkInDate', 'checkOutDate')); // Pastikan nama file sesuai
    }

    public function checkRoomAvailability(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'check_in' => 'required|date',
                'check_out' => 'required|date',
            ]);

            $checkInDate = Carbon::createFromFormat('Y-m-d', $request->input('check_in'), 'Asia/Jakarta');
            $checkOutDate = Carbon::createFromFormat('Y-m-d', $request->input('check_out'), 'Asia/Jakarta');

            // Pastikan validasi tanggal diterima dengan benar
            if (!$checkInDate || !$checkOutDate) {
                throw new \Exception("Invalid date format.");
            }

            // Cari kamar yang sudah terisi
            $occupiedRooms = Transaction::where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereDate('check_in', '<=', $checkInDate)
                    ->whereDate('check_out', '>=', $checkOutDate);
            })->pluck('room_id');

            // Ambil kamar yang belum terisi
            $availableRooms = Room::whereNotIn('id', $occupiedRooms)->get();

            return response()->json([
                'success' => true,
                'available_rooms' => $availableRooms
            ]);
        } catch (\Exception $e) {
            // Jika terjadi error, tampilkan pesan error
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function showPaymentPage($transactionId)
    {
        $transactionIds = explode('-', $transactionId); // Split the transaction ID string into an array

        // You can now fetch the transactions or do any processing
        $transactions = Transaction::whereIn('id', $transactionIds)->get();

        // Return the payment page view
        return view('transaction.group_booking_payment', compact('transactions' ,  'transactionId'));
    }







}
