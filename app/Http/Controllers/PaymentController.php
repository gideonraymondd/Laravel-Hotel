<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Repositories\Interface\PaymentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class PaymentController extends Controller
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository
    ) {
    }

    public function index()
    {
        $payments = Payment::orderBy('id', 'DESC')->paginate(5);

        return view('payment.index', ['payments' => $payments]);
    }

    public function create(Transaction $transaction)
    {
        return view('transaction.payment.create', [
            'transaction' => $transaction,
        ]);
    }

    public function store(Transaction $transaction, Request $request)
    {
        $insufficient = $transaction->getTotalPrice() - $transaction->getTotalPayment();
        $request->validate([
            'payment' => 'required|numeric|lte:'.$insufficient,
        ]);

        $this->paymentRepository->store($request, $transaction, 'Payment');

        return redirect()->route('transaction.index')->with('success', 'Transaction room '.$transaction->room->number.' success, '.Helpers::convertToRupiah($request->payment).' paid');
    }

    public function invoice(Payment $payment)
    {
        return view('payment.invoice', [
            'payment' => $payment,
        ]);
    }

    public function storeGroupBooking(Request $request, $transactionId)
{
    try {
        // Split the transaction ID string into an array
        $transactionIds = explode('-', $transactionId);

        // Get all the transactions for the given IDs
        $transactions = Transaction::whereIn('id', $transactionIds)->get();

        // Initialize total price to 0 (if you want a grand total later)
        $totalPrice = 0;

        // Loop through each transaction (each room in the group booking)
        foreach ($transactions as $transaction) {
            // Calculate the total price of the rooms in the group booking
            $totalPrice += $transaction->room->price;

            // Create a new payment record for each transaction
            $payment = Payment::create([
                'user_id' => auth()->user()->id, // Logged-in user
                'transaction_id' => $transaction->id, // Store individual transaction ID
                'price' => $transaction->room->price, // Price for this specific room
                'status' => 'Success', // Set status to 'pending' or based on your logic
                'created_at' => Carbon::now('Asia/Jakarta'),
            ]);

            // Log the payment data to ensure it was saved for each room
            Log::info('Payment Data for Transaction ID ' . $transaction->id, $payment->toArray());
        }

        // Log the total price (optional)
        Log::info('Total Price for Group Booking: ' . $totalPrice);

        return redirect()->route('transaction.index') // Ensure 'transaction.index' is the correct route name
            ->with('success', 'Payment successfully recorded for all rooms!');
    } catch (\Exception $e) {
        // Log the error for debugging purposes
        Log::error('Error storing group booking payment: ' . $e->getMessage());

        // Redirect with error message
        return redirect()->route('transaction.index')->with('error', 'An error occurred while processing the payment.');
    }
}




}
