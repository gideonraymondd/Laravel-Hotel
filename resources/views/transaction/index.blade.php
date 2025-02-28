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
                    <form action="{{ route('transaction.index') }}" method="GET" class="mb-3">
                        <div class="form-group">
                            <label for="date">Pilih Tanggal:</label>
                            <input type="date" id="date" name="date" class="form-control" value="{{ $date }}"
                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        </div>
                        <button type="submit" class="btn mt-2" style="background-color: #c4985d; border-color: #c4985d; color: white;">Cek Status Kamar</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($allRooms as $room)
                            <div class="col-6 col-md-3 mb-4">
                                <div class="card text-center room-card" onclick="fetchRoomDetails({{ $room->id }})">
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
                        {{ $allRooms->appends(request()->except('rooms_page'))->links('vendor.pagination.simple-bootstrap-5') }}                    </div>
                </div>
            </div>
        </div>

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
    </div>

    <div class="row mb-3">
        {{-- Room Status --}}
        <div class="col-lg-12">
            <!-- Tabel Status Kamar -->
            <div class="card shadow-sm border p-6">
                <div class="card-header">
                    <h4>Room Status Today</h4>
                </div>
                <div class="card-body">

                    <div class="row">
                        <!-- Tombol Filter -->
                        <div class="col-12 col-md-2 mb-3">
                            <p class='form-label'>Status</p>
                            <select class="form-select" id="filter-select">
                                <option value="reservation" {{ $filter === 'reservation' ? 'selected' : '' }}>Reservation</option>
                                <option value="check_in" {{ $filter === 'check_in' ? 'selected' : '' }}>Check-In</option>
                                <option value="check_out" {{ $filter === 'check_out' ? 'selected' : '' }}>Check-Out</option>
                                <option value="cleaned" {{ $filter === 'cleaned' ? 'selected' : '' }}>Cleaned</option>
                            </select>
                        </div>

                    </div>
                    <hr>
                    <div class="row">
                        <table class="table table-no-vertical-border">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>NOMOR RUANGAN</th>
                                    <th>NAMA PETUGAS</th>
                                    <th>TANGGAL DAN JAM</th>
                                </tr>
                            </thead>
                            <tbody id="room-table">
                                @include('transaction.partials.room-table', ['filterData' => $filterData, 'filter' => $filter])
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{-- {{ $filterData->appends(request()->query())->links('vendor.pagination.simple-bootstrap-5') }} --}}
                        {{ $filterData->appends(request()->except('filter_page'))->links('vendor.pagination.simple-bootstrap-5') }}                    </div>
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

    <script>
        const allRooms = @json($allRooms);  // Ini adalah Paginator objek yang berisi 'data'
        const occupiedRooms = @json($occupiedRooms);  // Ini adalah array ID kamar yang terisi

        function fetchRoomDetails(roomId) {
            if (roomId) {
                // Menemukan data kamar berdasarkan ID
                const room = allRooms.data.find(r => r.id == roomId);  // Mengakses 'data' di dalam Paginator

                if (room) {
                    // Menampilkan detail kamar
                    document.getElementById('roomNumber').innerText = room.number || '-';
                    document.getElementById('transactionStatus').innerText = room.transaction_status || '-';  // Pastikan atribut ini sesuai
                    document.getElementById('roomStatus').innerText = occupiedRooms.includes(room.id) ? 'Terisi' : 'Kosong';  // Cek apakah kamar terisi
                }
            } else {
                // Kosongkan detail kamar jika tidak ada kamar yang dipilih
                document.getElementById('roomNumber').innerText = '-';
                document.getElementById('transactionStatus').innerText = '-';
                document.getElementById('roomStatus').innerText = '-';
            }
        }

        function filterTransactions(value) {
            window.location.href = "{{ route('transaction.index') }}?filter=" + value;
        }

    </script>

@endsection
