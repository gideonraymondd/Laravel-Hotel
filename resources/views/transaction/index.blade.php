@extends('template.master')
@section('title', 'Reservation')
@section('content')

<div id="page">
    <h4 class="text-center p-2 d-block d-sm-none">Dashboard</h4>

    {{-- Head  --}}
    <div class="row mt-2 mb-2">
        <div class="col-lg-6 col-md-12 mb-2">
            <div class="d-flex justify-content-start gap-2">
                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Add Room Reservation">
                    <button type="button" class="btn btn-md shadow-sm myBtn border rounded  fw-semibold fs-6" data-bs-toggle="modal"
                        data-bs-target="#staticBackdrop">
                        Add Room Reservation
                    </button>
                </span>
                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Payment History">
                    <a href="{{route('payment.index')}}" class="btn btn-md shadow-sm myBtn border rounded  fw-semibold fs-6">
                        Payment History
                    </a>
                </span>
                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Transaction History">
                    <a href="{{route('transaction.history')}}" class="btn btn-md shadow-sm myBtn border rounded  fw-semibold fs-6">
                        Transaction History
                    </a>
                </span>
                <span data-bs-toggle="tooltip" data-bs-placement="right" title="Group Order">
                    <a href="{{route('group.booking.index')}}" class="btn btn-md shadow-sm myBtn border rounded  fw-semibold fs-6">
                        Group Order
                    </a>
                </span>

            </div>
        </div>
        <div class="col-lg-6 col-md-12 mb-2">
            <form class="d-flex justify-content-end" method="GET" action="{{ route('transaction.index') }}">
                <input class="form-control me-2" type="search" placeholder="Search by ID" aria-label="Search"
                    id="search-user" name="search" value="{{ request()->input('search') }}">
                <button class="btn btn-outline-dark" type="submit">Search</button>
            </form>
        </div>
    </div>

    {{-- Body --}}
    <div class="row mb-3">
        {{-- Room --}}
        <div class="col-lg-6 d-flex mb-2">
            <div class="card shadow-sm border h-100 w-100">
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
                        {{ $allRooms->links('vendor.pagination.simple-bootstrap-5') }} <!-- Menampilkan navigasi pagination -->
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Table --}}
        {{-- <div class="col-lg-7 d-flex mb-2">
            <div class="card shadow-sm border h-100 w-100">
                <div class="card-header">
                    <h4>Active Guest</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
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
                                            {{ $transaction->room_status }}</td>
                                        </td>
                                        <td>
                                            {{ $transaction->status }}</td>
                                        </td>
                                        <td>
                                            {{ $transaction->origin }}</td>
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
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div> --}}

        <div class="col-lg-6 d-flex mb-2">
            <div class="card shadow-sm border h-100 w-100">
                <div class="card-header">
                    <h4>Quick Action</h4>
                </div>
                <div class="card-body">
                    <!-- Room Name Select Option -->
                    <div class="mb-3">
                        <label for="roomName" class="form-label">Nama Kamar</label>
                        <select id="roomName" class="form-control" onchange="fetchRoomDetails(this.value)">
                            <option value="">Pilih Nama Kamar</option>
                            @foreach($unexpiredTransactions as $transaction)
                                <option value="{{ $transaction->id }}">{{$transaction->room->number}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Room Details Section -->
                    <div class="room-details mt-4">
                        <h5>Detail Kamar</h5>
                        <p>Nomor Kamar: <span id="roomNumber">-</span></p>
                        <p>Status Transaksi: <span id="transactionStatus">-</span></p>
                        <p>Status Kamar: <span id="roomStatus">-</span></p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        <button id="checkInBtn" class="btn btn-primary" onclick="updateTransaction('check-in')">Check In</button>
                        <button id="checkOutBtn" class="btn btn-warning" onclick="updateTransaction('check-out')">Check Out</button>
                        <button id="cleanedBtn" class="btn btn-success" onclick="updateTransaction('cleaned')">Cleaned</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const unexpiredTransactions = @json($unexpiredTransactions);
            console.log('unexpiredTransactions:', unexpiredTransactions)

            function fetchRoomDetails(transactionId) {
                if (transactionId) {
                    // Mendapatkan detail transaksi
                    const transaction = unexpiredTransactions.find(t => t.id == transactionId);
                    if (transaction) {
                        document.getElementById('roomNumber').innerText = transaction.room.number || '-';
                        document.getElementById('transactionStatus').innerText = transaction.status || '-';
                        document.getElementById('roomStatus').innerText = transaction.room_status || '-'; // Ganti dengan atribut yang benar jika berbeda
                    }
                } else {
                    // Kosongkan detail kamar jika tidak ada kamar yang dipilih
                    document.getElementById('roomNumber').innerText = '-';
                    document.getElementById('transactionStatus').innerText = '-';
                    document.getElementById('roomStatus').innerText = '-';
                }
            }

            function updateTransaction(newStatus) {
                const roomNameSelect = document.getElementById('roomName');
                const transactionId = roomNameSelect.value; // Make sure this variable is correctly defined

                if (!transactionId) {
                    alert('Silakan pilih nama kamar terlebih dahulu.');
                    return;
                }

                const transaction = unexpiredTransactions.find(t => t.id == transactionId);

                // Check conditions before updating
                if (newStatus === 'check-in' && (transaction.room_status === 'check-out' || transaction.room_status === 'cleaned')) {
                    alert('Tidak bisa Check In, kamar sudah Check Out atau sudah dibersihkan.');
                    return;
                }

                if (newStatus === 'check-out' && transaction.room_status !== 'check-in') {
                    alert('Tidak bisa Check Out, kamar belum Check In.');
                    return;
                }

                // Proceed with the update
                fetch(`/transaction/${transactionId}/change-room-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ room_status: newStatus })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Refresh details
                        fetchRoomDetails(transactionId);
                        alert(data.success);
                    } else {
                        alert(data.error || 'Terjadi kesalahan saat memperbarui status kamar.');
                    }
                })
                .catch(error => console.error('Error updating room status:', error));
            }
        </script>



    </div>

    <div class="row mb-3">
        {{-- Room Status --}}
        <div class="col-lg-12">
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
                            @if ($filterData->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data yang ditemukan.</td>
                                </tr>
                            @else
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
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $filterData->appends(request()->query())->links('vendor.pagination.simple-bootstrap-5') }}
                    </div>
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

    <style>

        #page {
        overflow-x: hidden; /* Mencegah horizontal scroll */
        max-width: 100vw; /* Pastikan elemen tidak lebih lebar dari viewport */
        padding: 0 15px; /* Beri sedikit padding untuk responsivitas */
        box-sizing: border-box; /* Menghitung padding dan border dalam width/height elemen */
        }

        .card {
            max-width: 100%; /* Pastikan card tidak meluap dari container */
        }

        .row {
            margin-right: 0;
            margin-left: 0; /* Hapus margin negatif pada row untuk mencegah meluap */
        }

    </style>

@endsection
