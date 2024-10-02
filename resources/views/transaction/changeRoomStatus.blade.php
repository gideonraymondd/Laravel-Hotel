@extends('template.master')

@section('title', 'Change Room Status')

@section('content')
    <div class="container">
        <form action="{{ route('transaction.changeRoomStatus', $transaction->id) }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-12">
                <label for="room_status" class="form-label">Change Room Status:</label>
                <select name="room_status" id="room_status" class="form-control select2">
                    <!-- Opsi awal sesuai status transaksi -->
                    @if ($transaction->room_status === 'booked')
                        <option value="booked">Booked</option>
                        <option value="check-in">Check in</option>
                        <option value="cancel">Cancel</option>
                    @elseif ($transaction->room_status === 'check-in')
                        <option value="check-in">Check in</option>
                        <option value="check-out">Check out</option>
                    @elseif ($transaction->room_status === 'check-out')
                        <option value="check-out">Check out</option>
                        <option value="cleaned">Clean</option>
                    @else
                        <option value="available" {{ $transaction->room_status === 'available' ? 'selected' : '' }}>Available</option>
                        <option value="booked" {{ $transaction->room_status === 'booked' ? 'selected' : '' }}>Book</option>
                        <option value="maintenance" {{ $transaction->room_status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="check-in" {{ $transaction->room_status === 'check-in' ? 'selected' : '' }}>Check in</option>
                        <option value="check-out" {{ $transaction->room_status === 'check-out' ? 'selected' : '' }}>Check out</option>
                        <option value="cleaned" {{ $transaction->room_status === 'cleaned' ? 'selected' : '' }}>Clean</option>
                        <option value="transfer" {{ $transaction->room_status === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    @endif
                </select>
                <div id="error_room_status" class="text-danger error"></div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>

    @section('scripts')
    <script>
        $(document).ready(function() {
            // Set opsi berdasarkan status yang ada
            var roomStatus = "{{ $transaction->room_status }}"; // ambil status transaksi
            var selectElement = $('#room_status');

            // Hapus semua opsi yang ada
            selectElement.empty();

            // Tambahkan opsi berdasarkan logika yang diinginkan
            if (roomStatus === 'booked') {
                selectElement.append('<option value="check-in">Check in</option>');
                selectElement.append('<option value="cancel">Cancel</option>');
            } else if (roomStatus === 'check-in') {
                selectElement.append('<option value="check-out">Check out</option>');
            } else if (roomStatus === 'check-out') {
                selectElement.append('<option value="cleaned">Clean</option>');
            } else {
                selectElement.append('<option value="available">Available</option>');
                selectElement.append('<option value="booked">Book</option>');
                selectElement.append('<option value="maintenance">Maintenance</option>');
                selectElement.append('<option value="check-in">Check in</option>');
                selectElement.append('<option value="check-out">Check out</option>');
                selectElement.append('<option value="cleaned">Clean</option>');
                selectElement.append('<option value="transfer">Transfer</option>');
            }

            // Set nilai default berdasarkan status saat ini
            selectElement.val(roomStatus);
        });
    </script>
    @endsection
@endsection
