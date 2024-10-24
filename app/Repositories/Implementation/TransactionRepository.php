<?php

namespace App\Repositories\Implementation;

use App\Models\Customer;
use App\Models\Room;
use App\Models\Transaction;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Carbon\Carbon;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function store($request, Customer $customer, Room $room)
    {
        return Transaction::create([
            'user_id' => auth()->user()->id,
            'customer_id' => $customer->id,
            'room_id' => $room->id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'status' => 'Reservation',
            'origin' => $request->origin,
            'created_by'=> auth()->user()->id,
            'created_at' => Carbon::now('Asia/Jakarta'),
            'checked_in_time' => null,
            'checked_out_time' => null,
            'cleaned_time' => null,
        ]);
    }

    public function getTransaction($request)
    {
        return Transaction::with('user', 'room', 'customer')
            ->where('check_out', '>=', Carbon::now('Asia/Jakarta'))
            ->when(! empty($request->search), function ($query) use ($request) {
                $query->where('id', '=', $request->search);
            })
            ->orderBy('check_out', 'ASC')->orderBy('id', 'DESC')->paginate(20)
            ->appends($request->all());
    }

    public function getTransactionPagination($request)
    {
    return Transaction::with('user', 'room', 'customer')
        ->where('check_out', '>=', Carbon::now('Asia/Jakarta'))
        ->when(!empty($request->search), function ($query) use ($request) {
            $query->where('id', '=', $request->search);
        })
        ->orderBy('check_out', 'ASC')
        ->orderBy('id', 'DESC');
    }


    public function getTransactionExpired($request)
    {
        return Transaction::with('user', 'room', 'customer')->where('check_out', '<', Carbon::now())
            ->when(! empty($request->search), function ($query) use ($request) {
                $query->where('id', '=', $request->search);
            })
            ->orderBy('check_out', 'ASC')->paginate(20)
            ->appends($request->all());
    }
}
