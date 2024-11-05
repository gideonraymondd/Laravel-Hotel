{{-- resources/views/group_booking.blade.php --}}
@extends('template.master')
@section('title', 'Group Booking')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12 mt-2">
            <div class="card">
                <div class="card-header">
                    <h3>Group Booking</h3>
                </div>
                <div class="card-body">

                    {{-- Tampilkan pesan sukses jika ada --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Form Group Booking --}}
                    <form action="{{ route('group.booking.store') }}" method="POST">
                        @csrf
                        
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer</label>
                            <input type="text" id="customer-search" class="form-control" placeholder="Cari Customer" required>
                            <ul id="customer-list" class="list-group mt-1" style="display: none;"></ul>
                            <input type="hidden" name="customer_id" id="customer_id">
                        </div>

                        <div class="row mb-3">
                            <label for="room_ids" class="col-sm-2 col-form-label">Room</label>
                            <div class="col-sm-10">
                                <input type="text" id="room-search" class="form-control" placeholder="Masukkan Nomor Kamar (pisahkan dengan koma)" required disabled>
                                <ul id="room-list" class="list-group mt-1" style="display: none;"></ul>
                                <input type="hidden" name="room_ids[]" id="room_ids">
                                <div id="selected-rooms" class="mt-2"></div> <!-- Tempat menampilkan kamar yang dipilih -->
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="check_in" class="col-sm-2 col-form-label">Check In</label>
                            <div class="col-sm-10">
                                <input type="date" name="check_in" class="form-control" id="check_in" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="check_out" class="col-sm-2 col-form-label">Check Out</label>
                            <div class="col-sm-10">
                                <input type="date" name="check_out" class="form-control" id="check_out" required>
                            </div>
                        </div>

                        {{-- <div class="row mb-3">
                            <label for="special_price" class="col-sm-2 col-form-label">Special Price</label>
                            <div class="col-sm-10">
                                <input type="number" name="special_price" class="form-control" step="0.01">
                            </div>
                        </div> --}}

                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">Book Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Include jQuery Scripts --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        const customers = @json($customers); // Mengambil data customer dari server
        const rooms = @json($availableRooms); // Mengambil data room dari server

        // Pencarian Customer
        $('#customer-search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            const filteredCustomers = customers.filter(customer =>
                customer.name.toLowerCase().includes(searchTerm)
            );

            $('#customer-list').empty(); // Kosongkan daftar customer sebelumnya
            if (filteredCustomers.length > 0) {
                filteredCustomers.forEach(customer => {
                    $('#customer-list').append(
                        `<li class="list-group-item customer-item" data-id="${customer.id}">${customer.name}</li>`
                    );
                });
                $('#customer-list').show();
            } else {
                $('#customer-list').hide(); // Sembunyikan jika tidak ada hasil
            }
        });

        // Klik pada customer untuk memilihnya
        $(document).on('click', '.customer-item', function() {
            const customerId = $(this).data('id');
            const customerName = $(this).text();
            $('#customer-search').val(customerName);
            $('#customer_id').val(customerId);
            $('#customer-list').hide();
        });

        // Sembunyikan daftar customer saat mengklik di luar
        $(document).click(function(event) {
            if (!$(event.target).closest('#customer-search').length) {
                $('#customer-list').hide();
            }
        });

        // Pencarian Room
        $('#room-search').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            const filteredRooms = rooms.filter(room =>
                room.number.toLowerCase().includes(searchTerm) // Menggunakan nomor kamar
            );

            $('#room-list').empty(); // Kosongkan daftar room sebelumnya
            if (filteredRooms.length > 0) {
                filteredRooms.forEach(room => {
                    $('#room-list').append(
                        `<li class="list-group-item room-item" data-id="${room.id}">${room.number}</li>` // Menggunakan nomor kamar
                    );
                });
                $('#room-list').show();
            } else {
                $('#room-list').hide(); // Sembunyikan jika tidak ada hasil
            }
        });

        // Enable room search setelah mengisi check-in dan check-out
        $('#check_in, #check_out').on('change', function() {
            if ($('#check_in').val() && $('#check_out').val()) {
                $('#room-search').prop('disabled', false); // Meng-enable input room
            } else {
                $('#room-search').prop('disabled', true); // Disable jika salah satu kosong
                $('#room-search').val(''); // Kosongkan input room
                $('#room-list').hide(); // Sembunyikan daftar room
                $('#selected-rooms').empty(); // Kosongkan kamar yang dipilih
                $('#room_ids').val(''); // Kosongkan input hidden room_ids
            }
        });

        // Klik pada room untuk memilihnya
        let selectedRoomIds = [];
        $(document).on('click', '.room-item', function() {
            const roomId = $(this).data('id');
            const roomNumber = $(this).text();

            if (!selectedRoomIds.includes(roomId)) {
                selectedRoomIds.push(roomId);
                $('#room_ids').val(selectedRoomIds.join(',')); // Simpan ID kamar dalam bentuk string
                $('#selected-rooms').append(`<span class="badge bg-secondary me-1">${roomNumber} <button class="btn-close btn-close-white" aria-label="Close" data-id="${roomId}"></button></span>`); // Menampilkan nomor kamar yang dipilih
                $('#room-list').hide();
            }
        });

        // Hapus kamar yang dipilih
        $(document).on('click', '.btn-close', function() {
            const roomId = $(this).data('id');
            selectedRoomIds = selectedRoomIds.filter(id => id !== roomId);
            $('#room_ids').val(selectedRoomIds.join(',')); // Update input hidden room_ids
            $(this).parent().remove(); // Hapus elemen badge
        });

        // Sembunyikan daftar room saat mengklik di luar
        $(document).click(function(event) {
            if (!$(event.target).closest('#room-search').length) {
                $('#room-list').hide();
            }
        });
    });
</script>

@endsection
