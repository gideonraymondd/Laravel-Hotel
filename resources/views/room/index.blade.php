@extends('template.master')
@section('title', 'Room')
@section('head')
    <style>
        #container-fluid {
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
        }

        /* Warna teks pada pagination saat tidak aktif */
        .dataTables_wrapper .dataTables_paginate .page-link {
            color: #c4985d !important;
        }

        /* Warna tombol dan teks saat aktif atau diklik */
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link,
        .dataTables_wrapper .dataTables_paginate .page-link:focus,
        .dataTables_wrapper .dataTables_paginate .page-link:active {
            background-color: #c4985d !important;
            border-color: #c4985d !important;
            color: white !important;
        }

        /* Warna tombol saat hover */
        .dataTables_wrapper .dataTables_paginate .page-link:hover {
            background-color: #d8a677 !important;
            border-color: #d8a677 !important;
            color: white !important;
        }



    </style>
@endsection
@section('content')
    <div id="container-fluid">
        <h4 class="text-center p-2 d-block d-sm-none">Rooms</h4>
        <div class="row">
            <div class="col-12">
                <div class="row mt-2 mb-2">
                    <div class="col-12 mb-2">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <button id="add-button" type="button" class="btn btn-md shadow-sm myBtn border rounded fw-semibold fs-6">
                                Add Room
                            </button>
                            <button id="price-button" type="button" class="btn btn-md shadow-sm myBtn border rounded fw-semibold fs-6" data-bs-toggle="modal" data-bs-target="#priceModal">
                                Update Prices
                            </button>
                            <button id="reset-button" type="button" class="btn btn-md shadow-sm myBtn border rounded fw-semibold fs-6" data-bs-toggle="modal" data-bs-target="#resetPriceModal">
                                Reset to Normal Price
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border">
                            <div class="card-header">
                                <h3>Rooms</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select id="status" class="form-select" aria-label="Choose status">
                                            <option selected>All</option>
                                            @forelse ($roomStatuses as $roomStatus)
                                                <option value="{{ $roomStatus->id }}">{{ $roomStatus->name }}</option>
                                            @empty
                                                <option value="">No room status</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label for="type" class="form-label">Type</label>
                                        <select id="type" class="form-select" aria-label="Choose type">
                                            <option selected>All</option>
                                            @forelse ($types as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @empty
                                                <option value="">No type</option>
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table id="room-table" class="table table-sm table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th scope="col">Number</th>
                                                <th scope="col">Type</th>
                                                <th scope="col">Capacity</th>
                                                <th scope="col">Price / Day</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Modal -->
                            {{-- <div class="modal fade" id="priceModal" tabindex="-1" aria-labelledby="priceModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('rooms.updatePrices') }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="priceIncrement" class="form-label">New Price</label>
                                                    <input type="number" id="priceIncrement" name="price_increment" class="form-control" placeholder="Enter new price">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update Prices</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="modal fade" id="priceModal" tabindex="-1" aria-labelledby="priceModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('rooms.updatePrices') }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="priceIncrement" class="form-label">New Price</label>
                                                    <input type="text" id="priceIncrement" name="price_increment" class="form-control" placeholder="Enter new price">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" id="updatePricesButton">Update Prices</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal Reset -->
                            <div class="modal fade" id="resetPriceModal" tabindex="-1" aria-labelledby ="resetPriceModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="resetPriceModalLabel">Reset Room Prices</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to reset all room prices to 250,000?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <form action="{{ route('rooms.resetPrices') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-warning">Reset Prices</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const priceInput = document.getElementById("priceIncrement");

        function formatNumber(value) {
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        priceInput.addEventListener("input", function (e) {
            // Simpan posisi kursor
            let cursorPosition = e.target.selectionStart;
            let value = e.target.value.replace(/\./g, ""); // Hapus semua titik sebelum format ulang

            // Cegah input selain angka
            if (!/^\d+$/.test(value) && value !== "") {
                return;
            }

            // Format ulang dengan titik ribuan
            e.target.value = formatNumber(value);

            // Atur kembali posisi kursor agar tidak meloncat ke akhir input
            let diff = e.target.value.length - value.length;
            e.target.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
        });

        // Pastikan angka dikirim tanpa titik saat form dikirim
        document.querySelector("form").addEventListener("submit", function () {
            priceInput.value = priceInput.value.replace(/\./g, ""); // Hapus titik sebelum submit
        });
    });


    document.getElementById('updatePricesButton').addEventListener('click', function() {
        let incrementValue = document.getElementById('priceIncrement').value;

        // Remove dots for processing the value before sending it to the server
        incrementValue = incrementValue.replace(/\./g, '');

        if (incrementValue) {
            alert('Prices will be updated by ' + incrementValue);
            // Logic to update prices (e.g., AJAX request or form submission)
            let priceModal = new bootstrap.Modal(document.getElementById('priceModal'));
            priceModal.hide();
        } else {
            alert('Please enter an increment value.');
        }
    });
</script>

