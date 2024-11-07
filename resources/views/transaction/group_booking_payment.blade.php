@extends('template.master')
@section('title', 'Payment for Group Booking')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <div class="card">
                <div class="card-header">
                    <h3>Payment for Group Booking</h3>
                </div>
                <div class="card-body">
                    {{-- Display success or error messages --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Display the details of each transaction --}}
                    <h5 class="mb-3">Booking Details:</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->room->number }}</td>
                                    <td>{{ Carbon\Carbon::parse($transaction->check_in)->format('d-m-Y') }}</td>
                                    <td>{{ Carbon\Carbon::parse($transaction->check_out)->format('d-m-Y') }}</td>
                                    <td>Rp {{ number_format($transaction->room->price, 2) }}</td>
                                    <td>{{ ucfirst($transaction->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- Payment Form --}}
                    <h5 class="mt-4">Make Payment</h5>
                    <form action="{{ route('group.booking.payment.process', ['transactionId' => $transactionId]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="">Select Payment Method</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="text" name="amount" class="form-control" value="Rp {{ number_format($transactions->sum('room.price'), 2) }}" disabled>
                        </div>

                        <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
