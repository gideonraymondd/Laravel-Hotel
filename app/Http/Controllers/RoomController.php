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

    public function store(StoreRoomRequest $request)
    {
        $room = Room::create($request->all());

        return response()->json([
            'message' => 'Room '.$room->number.' created',
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

    public function update(Room $room, StoreRoomRequest $request)
    {
        $room->update($request->all());

        return response()->json([
            'message' => 'Room '.$room->number.' udpated!',
        ]);
    }

    public function destroy(Room $room)
{
    Log::info('Start deleting room: '.$room->id);

    try {
        // Hapus room dari database
        Log::info('Deleting room from database: '.$room->id);
        $room->delete();

        Log::info('Room deleted successfully');
        return response()->json([
            'message' => 'Room number '.$room->number.' deleted!',
        ]);
    } catch (\Exception $e) {
        Log::error('Error deleting room: '.$e->getMessage());
        return response()->json([
            'message' => 'Room '.$room->number.' cannot be deleted! Error: '.$e->getMessage(),
        ], 500);
    }
}

public function updatePrices(Request $request)
    {
        // Validasi input increment price
        $request->validate([
            'price_increment' => 'required|numeric',
        ]);

        // Ambil nilai kenaikan harga
        $increment = $request->input('price_increment');

        // Update harga di tabel 'rooms'
        Room::query()->update([
            'price' => \DB::raw("price + {$increment}")
        ]);

        // Redirect ke halaman yang sama dengan pesan sukses
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
