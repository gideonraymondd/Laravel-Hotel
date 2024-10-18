@extends('template.master')
@section('title', 'transcation')
@section('content')

    <div class="card shadow-sm border">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Customer</th>
                        <th scope="col">Room</th>
                        <th scope="col">Order Date</th>
                        <th scope="col">Total Price</th>
                        <th scope="col">Origin</th>
                        {{-- <th scope="col">Origin</th> --}}
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
                            {{-- <td><a href="{{ route('transaction.show', $transaction->id) }}">Details</a></td> --}}
                        </tr>
                    @empty
                        <tr class="text-center">
                            <td colspan="6">Tidak ada transaksi yang ditemukan di database</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

@endsection
