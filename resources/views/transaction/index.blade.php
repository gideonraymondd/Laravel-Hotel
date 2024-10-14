@extends('template.master')
@section('title', 'Reservation')
@section('content')
    {{-- Head  --}}
    <div class="row mt-2 mb-2">
        <div class="col-lg-6 mb-2">
            <div class="d-grid gap-2 d-md-block">
                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Add Room Reservation">
                    <button type="button" class="btn btn-sm shadow-sm myBtn border rounded" data-bs-toggle="modal"
                        data-bs-target="#staticBackdrop">
                        <i class="fas fa-plus"></i>
                    </button>
                </span>
                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Payment History">
                    <a href="{{route('payment.index')}}" class="btn btn-sm shadow-sm myBtn border rounded">
                        <i class="fas fa-history"></i>
                    </a>
                </span>
            </div>
        </div>
        <div class="col-lg-6 mb-2">
            <form class="d-flex" method="GET" action="{{ route('transaction.index') }}">
                <input class="form-control me-2" type="search" placeholder="Search by ID" aria-label="Search"
                    id="search-user" name="search" value="{{ request()->input('search') }}">
                <button class="btn btn-outline-dark" type="submit">Search</button>
            </form>
        </div>
    </div>

    <div class="row mb-3">
        {{-- Room --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border">
                <div class="card-header">
                    <h4>Rooms</h4>
                    <form action="{{ route('dashboard.index') }}" method="GET" class="mb-3">
                        <div class="form-group">
                            <label for="date">Pilih Tanggal:</label>
                            <input type="date" id="date" name="date" class="form-control" value="{{ $date }}"
                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">

                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Cek Status Kamar</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($allRooms as $room)
                            <div class="col-6 col-md-3 mb-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h5 class="card-title">No: {{ $room->number }}</h5>
                                        @if ($occupiedRooms->contains($room->id))
                                            <span class="badge badge-danger text-dark">Terisi</span>
                                        @else
                                            <span class="badge badge-success text-dark">Kosong</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $allRooms->links() }} <!-- Menampilkan navigasi pagination -->
                    </div>
                </div>
            </div>
        </div>
        {{-- Room Status --}}
        <div class="col-lg-7">
            <!-- Tabel Status Kamar -->
            <div class="card shadow-sm border">
                <div class="card-header">
                    <h4>Room Status Today</h4>
                    <!-- Tombol Filter -->
                    <div class="btn-group mb-3">
                        <a href="{{ route('transaction.index', ['filter' => 'check_in']) }}" class="btn btn-primary">Check-In</a>
                        <a href="{{ route('transaction.index', ['filter' => 'check_out']) }}" class="btn btn-success">Check-Out</a>
                        <a href="{{ route('transaction.index', ['filter' => 'cleaned']) }}" class="btn btn-warning">Cleaned</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nomor Ruangan</th>
                                <th>Lantai</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filterData as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaction->room->number }}</td>
                                    <td>{{ $transaction->room->floor }}</td>
                                    <td>
                                        @if ($filter === 'check_in')
                                            <span class="badge badge-primary">Check-In</span>
                                        @elseif ($filter === 'check_out')
                                            <span class="badge badge-success">Check-Out</span>
                                        @elseif ($filter === 'cleaned')
                                            <span class="badge badge-warning">Cleaned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($filter === 'check_in')
                                            {{ $transaction->check_in }}
                                        @elseif ($filter === 'check_out')
                                            {{ $transaction->check_out }}
                                        @elseif ($filter === 'cleaned')
                                            {{ $transaction->cleaned_date }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        {{-- Order --}}
        <div class="col lg-12">
            <div class="card shadow-sm border">
                <div class="card-header">
                    <h4>Active Guest</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Room</th>
                                    <th>Order Date</th>
                                    <th>Total Price</th>
                                    <th>Payment Status</th>
                                    <th>Room Status</th>
                                    <th>Order Status</th>
                                    <th>Origin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ $transaction->customer->name }}</td>
                                        <td>{{ $transaction->room->number }}</td>
                                        <td>
                                            {{ Helper::dateFormat($transaction->check_in) }} - {{ Helper::dateFormat($transaction->check_out) }}
                                        </td>
                                        <td>{{ Helper::convertToRupiah($transaction->getTotalPrice()) }}
                                        </td>
                                        <td>
                                            @if ($transaction->isPaymentComplete())
                                                <span class="text-success">Paid off</span>
                                            @else
                                                <span class="text-danger">outstanding</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $transaction->room_status }}</>
                                        </td>
                                        <td>
                                            {{ $transaction->status }}</>
                                        </td>
                                        <td>
                                            {{ $transaction->origin }}</>
                                        </td>
                                        <td>
                                            <!-- Tombol Pay -->
                                            <a class="btn btn-light btn-sm rounded shadow-sm border p-1 m-0 {{$transaction->getTotalPrice($transaction->room->price, $transaction->check_in, $transaction->check_out) - $transaction->getTotalPayment() <= 0 ? 'disabled' : ''}}"
                                                href="{{ route('transaction.payment.create', ['transaction' => $transaction->id]) }}"
                                                data-bs-toggle="tooltip" data-bs-placement="top" title="Pay">
                                                Pay
                                            </a>

                                            <!-- Tombol Change Status -->
                                            <a class="btn btn-light btn-sm rounded shadow-sm border p-1 m-0"
                                            href="{{ route('transaction.changeRoomStatus', ['transaction' => $transaction->id]) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Change Status">
                                                Change Status
                                            </a>

                                            <!-- Tombol Details -->
                                            <a class="btn btn-light btn-sm rounded shadow-sm border p-1 m-0"
                                            href="{{ route('transaction.show', ['transaction' => $transaction->id]) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Details">
                                                Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center">
                                            There's no data in this table
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $transactions->onEachSide(2)->links('template.paginationlinks') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-2 mt-4 ms-1">
        <div class="col-lg-12">
            <h5>Expired: </h5>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Room</th>
                                    <th>Order Date</th>
                                    <th>Total Price</th>
                                    <th>Payment Status</th>
                                    <th>Room Status</th>
                                    <th>Order Status</th>
                                    <th>Origin</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactionsExpired as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->customer->name }}</td>
                                    <td>{{ $transaction->room->number }}</td>
                                    <td>
                                        {{ Helper::dateFormat($transaction->check_in) }} - {{ Helper::dateFormat($transaction->check_out) }}
                                    </td>
                                    <td>{{ Helper::convertToRupiah($transaction->getTotalPrice()) }}
                                    </td>
                                    <td>
                                        @if ($transaction->isPaymentComplete())
                                            <span class="text-success">Paid off</span>
                                        @else
                                            <span class="text-danger">Outstanding</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->room_status }}</>
                                    </td>
                                    <td>
                                        {{ $transaction->status }}</>
                                    </td>
                                    <td>
                                        {{ $transaction->origin }}</>
                                    </td>
                                    <td>
                                        <!-- Tombol Pay -->
                                        <a class="btn btn-light btn-sm rounded shadow-sm border p-1 m-0 {{$transaction->getTotalPrice($transaction->room->price, $transaction->check_in, $transaction->check_out) - $transaction->getTotalPayment() <= 0 ? 'disabled' : ''}}"
                                            href="{{ route('transaction.payment.create', ['transaction' => $transaction->id]) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Pay">
                                            Pay
                                        </a>

                                        <!-- Tombol Change Status -->
                                        <a class="btn btn-light btn-sm rounded shadow-sm border p-1 m-0
                                            {{ $transaction->status === 'Done' || $transaction->status === 'Transfer' ? 'disabled' : '' }}"
                                            href="{{ route('transaction.changeRoomStatus', ['transaction' => $transaction->id]) }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Change Status"
                                            {{ $transaction->status === 'Done' || $transaction->status === 'Transfer' ? 'aria-disabled=true tabindex=-1' : '' }}>
                                            Change Status
                                        </a>


                                        <!-- Tombol Details -->
                                        <a class="btn btn-light btn-sm rounded shadow-sm border p-1 m-0"
                                        href="{{ route('transaction.show', ['transaction' => $transaction->id]) }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Details">
                                            Details
                                        </a>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-center">
                                        There's no data in this table
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        {{ $transactions->onEachSide(2)->links('template.paginationlinks') }}
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Have any account?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-center">
                        <a class="btn btn-sm btn-primary m-1"
                            href="{{ route('transaction.reservation.createIdentity') }}">No, create
                            new account!</a>
                        <a class="btn btn-sm btn-success m-1"
                            href="{{ route('transaction.reservation.pickFromCustomer') }}">Yes, use
                            their account!</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
