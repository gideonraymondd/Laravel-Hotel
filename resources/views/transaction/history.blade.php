@extends('template.master')
@section('title', 'Transaction')
@section('content')

    <div class="card shadow-sm border">
        <div class="card-header">
            <!-- Judul untuk tabel -->
            <h4 class="card-title">Transaction</h4>

            <!-- Filter untuk transaksi yang sudah expired -->
            <form action="{{ route('transaction.filter') }}" method="GET" class="mb-3">
                <div class="form-group">
                    <label for="filterExpired">Filter Expired Transactions</label>
                    <select name="filterExpired" id="filterExpired" class="form-control">
                        <option value="all" {{ request('filterExpired') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="current" {{ request('filterExpired') == 'current' ? 'selected' : '' }}>Current</option>
                        <option value="expired" {{ request('filterExpired') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Apply Filter</button>
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
                        @if (request('filterExpired') == 'expired' && $transaction->check_out < now())
                            <tr>
                                <td>{{ $transaction->customer->name }}</td>
                                <td>{{ $transaction->room->number }}</td>
                                <td>
                                    {{ Helper::dateFormat($transaction->check_in) }} - {{ Helper::dateFormat($transaction->check_out) }}
                                </td>
                                <td>{{ Helper::convertToRupiah($transaction->getTotalPrice()) }}</td>
                                <td>{{ $transaction->origin }}</td>
                            </tr>
                        @elseif (request('filterExpired') == 'all')
                            <tr>
                                <td>{{ $transaction->customer->name }}</td>
                                <td>{{ $transaction->room->number }}</td>
                                <td>
                                    {{ Helper::dateFormat($transaction->check_in) }} - {{ Helper::dateFormat($transaction->check_out) }}
                                </td>
                                <td>{{ Helper::convertToRupiah($transaction->getTotalPrice()) }}</td>
                                <td>{{ $transaction->origin }}</td>
                            </tr>
                        @elseif (request('filterExpired') == 'current' && $transaction->check_in <= now() && $transaction->check_out > now())
                            <tr>
                                <td>{{ $transaction->customer->name }}</td>
                                <td>{{ $transaction->room->number }}</td>
                                <td>
                                    {{ Helper::dateFormat($transaction->check_in) }} - {{ Helper::dateFormat($transaction->check_out) }}
                                </td>
                                <td>{{ Helper::convertToRupiah($transaction->getTotalPrice()) }}</td>
                                <td>{{ $transaction->origin }}</td>
                            </tr>
                        @endif
                    @empty
                        <tr class="text-center">
                            <td colspan="6">Tidak ada transaksi yang ditemukan di database</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $transactions->links() }}   <!-- Menampilkan navigasi pagination -->
            </div>
        </div>
    </div>

@endsection
