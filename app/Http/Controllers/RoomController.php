<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\Transaction;
use App\Models\Type;
use App\Repositories\Interface\ImageRepositoryInterface;
use App\Repositories\Interface\RoomRepositoryInterface;
use App\Repositories\Interface\RoomStatusRepositoryInterface;
use App\Repositories\Interface\TypeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class RoomController extends Controller
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
        private TypeRepositoryInterface $typeRepository,
        private RoomStatusRepositoryInterface $roomStatusRepositoryInterface
    ) {
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->roomRepository->getRoomsDatatable($request);
        }

        $types = $this->typeRepository->getTypeList($request);
        $roomStatuses = $this->roomStatusRepositoryInterface->getRoomStatusList($request);

        return view('room.index', [
            'types' => $types,
            'roomStatuses' => $roomStatuses,
        ]);
    }

    public function create()
    {
        $types = Type::all();
        $roomstatuses = RoomStatus::all();
        $view = view('room.create', [
            'types' => $types,
            'roomstatuses' => $roomstatuses,
        ])->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'number' => 'required|unique:rooms,number',
            'type_id' => 'required|exists:types,id',
            'room_status_id' => 'required|exists:room_statuses,id',
            'capacity' => ['required', 'integer', 'min:1', 'regex:/^[1-9]\d*$/'],
            'price' => ['required', 'numeric', 'min:1', 'regex:/^[1-9]\d*(\.\d{1,2})?$/'],
            // 'view' => 'nullable|string|max:255',
        ], [
            'number.unique' => 'Nomor ruangan sudah ada!',
            'capacity.min' => 'Capacity harus lebih dari 0!',
            'capacity.regex' => 'Capacity harus bilangan bulat positif!',
            'price.min' => 'Price harus lebih dari 0!',
        ]);

        // Simpan data ke database
        $room = Room::create($request->all());

        return response()->json([
            'message' => 'Room ' . $room->number . ' created',
        ]);
    }


    public function show(Room $room)
    {
        $customer = [];
        $transaction = Transaction::where([['check_in', '<=', Carbon::now()], ['check_out', '>=', Carbon::now()], ['room_id', $room->id]])->first();
        if (! empty($transaction)) {
            $customer = $transaction->customer;
        }

        return view('room.show', [
            'customer' => $customer,
            'room' => $room,
        ]);
    }

    public function edit(Room $room)
    {
        $types = Type::all();
        $roomstatuses = RoomStatus::all();
        $view = view('room.edit', [
            'room' => $room,
            'types' => $types,
            'roomstatuses' => $roomstatuses,
        ])->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    public function update(Request $request, Room $room)
    {
        // Validasi input
        $request->validate([
            'number' => 'required|unique:rooms,number,' . $room->id, // Unique untuk pengecekan nomor ruangan
            'type_id' => 'required|exists:types,id', // Memastikan type_id valid
            'room_status_id' => 'required|exists:room_statuses,id', // Memastikan room_status_id valid
            'capacity' => ['required', 'integer', 'min:1', 'regex:/^[1-9]\d*$/'], // Validasi capacity
            'price' => ['required', 'numeric', 'min:1', 'regex:/^[1-9]\d*(\.\d{1,2})?$/'], // Validasi price
            'view' => 'nullable|string|max:255', // View opsional
        ], [
            'number.unique' => 'Nomor ruangan sudah ada!',
            'capacity.min' => 'Capacity harus lebih dari 0!',
            'capacity.regex' => 'Capacity harus bilangan bulat positif!',
            'price.min' => 'Price harus lebih dari 0!',
            'price.regex' => 'Price harus angka positif dan bisa pakai desimal!',
        ]);

        // Update data room
        $room->update($request->all()); // Update record

        // Mengembalikan response setelah update berhasil
        return response()->json([
            'message' => 'Room ' . $room->number . ' updated',
        ]);
    }


    public function destroy(Room $room)
    {
        Log::info('Start deleting room: '.$room->id);

        try {
            // Periksa apakah ada transaksi terkait sebelum menghapus room
            if ($room->transactions()->exists()) {
                Log::warning('Room has related transactions and cannot be deleted: ' . $room->id);
                return response()->json([
                    'message' => 'Data kamar dengan nomor '.$room->number.' tidak dapat dihapus karena sudah ada transaksi yang terkait.',
                ], 400);
            }

            // Hapus room dari database
            Log::info('Deleting room from database: '.$room->id);
            $room->delete();

            Log::info('Room deleted successfully');
            return response()->json([
                'message' => 'Kamar dengan nomor '.$room->number.' berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting room: '.$e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus kamar '.$room->number.': '.$e->getMessage(),
            ], 500);
        }
    }


    public function updatePrices(Request $request)
    {
        // Validasi input price
        $request->validate([
            'price_increment' => 'required|numeric',
        ]);

        // Ambil nilai harga baru
        $newPrice = $request->input('price_increment');

        // Update harga di tabel 'rooms' dengan harga baru
        Room::query()->update([
            'price' => $newPrice
        ]);

        return redirect()->back()->with('success', 'Room prices updated successfully!');
    }


    public function resetPrices(Request $request)
    {
        // Set semua harga kamar menjadi 250,000
        Room::query()->update([
            'price' => 250000
        ]);

        // Redirect ke halaman yang sama dengan pesan sukses
        return redirect()->back()->with('success', 'Room prices reset to 250,000 successfully!');
    }
}
