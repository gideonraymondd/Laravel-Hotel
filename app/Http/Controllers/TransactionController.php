<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Repositories\Interface\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Transaction;


class TransactionController extends Controller
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {
    }

    public function index(Request $request)
    {
        $transactions = $this->transactionRepository->getTransaction($request);
        $transactionsExpired = $this->transactionRepository->getTransactionExpired($request);

        return view('transaction.index', [
            'transactions' => $transactions,
            'transactionsExpired' => $transactionsExpired,
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




}
