@extends('template.master')
@section('title', 'Transaction')
@section('content')

    <div class="card shadow-sm border">
        <div class="card-header">
            <!-- Title for table -->
            <h4 class="card-title">Transaction</h4>

            <!-- Filter for expired transactions -->
            <form action="{{ route('transaction.history') }}" method="GET" class="mb-3" id="filterForm">
                <div class="row">
                    <!-- Filter for Expired Transactions -->
                    <div class="form-group col-12 col-md-6">
                        <label for="filterExpired" class="mb-2">Filter Expired Transactions</label>
                        <select name="filterExpired" id="filterExpired" class="form-control" onchange="this.form.submit()">
                            <option value="all" {{ request('filterExpired') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="current" {{ request('filterExpired') == 'current' ? 'selected' : '' }}>Current</option>
                            <option value="expired" {{ request('filterExpired') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>

                    <!-- Filter for Origin -->
                    <div class="form-group col-12 col-md-6">
                        <label for="filterOrigin" class="mb-2">Filter by Origin</label>
                        <select name="filterOrigin" id="filterOrigin" class="form-control" onchange="this.form.submit()">
                            <option value="all" {{ request('filterOrigin') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="traveloka" {{ request('filterOrigin') == 'traveloka' ? 'selected' : '' }}>Traveloka</option>
                            <option value="tiket.com" {{ request('filterOrigin') == 'tiket.com' ? 'selected' : '' }}>Tiket.com</option>
                            <option value="booking.com" {{ request('filterOrigin') == 'booking.com' ? 'selected' : '' }}>Booking.com</option>
                            <option value="offline" {{ request('filterOrigin') == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Customer</th>
                        <th scope="col">Room</th>
                        <th scope="col">Order Date</th>
                        <th scope="col">Total Price</th>
                        <th scope="col">Origin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->customer->name }}</td>
                            <td>{{ $transaction->room->number }}</td>
                            <td>
                                {{ Helper::dateFormat($transaction->check_in) }} - {{ Helper::dateFormat($transaction->check_out) }}
                            </td>
                            <td>{{ Helper::convertToRupiah($transaction->getTotalPrice()) }}</td>
                            <td>{{ $transaction->origin }}</td>
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="6">No transactions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $transactions->links('vendor.pagination.simple-bootstrap-5') }}   <!-- Pagination links -->
            </div>
        </div>
    </div>

@endsection
