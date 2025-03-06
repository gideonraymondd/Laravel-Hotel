@extends('template.master')
@section('title', 'Choose Your Room')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
    <style>
        .room-card {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            background-color: #fff;
        }

        .room-image {
            width: 100%;
            height: 180px;
            background-color: #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .room-details {
            padding: 15px;
        }

        .room-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .room-number {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .room-price {
            font-size: 18px;
            font-weight: 600;
            color: #000;
            margin-bottom: 10px;
        }

        .room-capacity {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .room-description {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .room-amenities {
            display: flex;
            margin-bottom: 15px;
        }

        .room-amenities .amenity {
            margin-right: 15px;
        }

        .book-btn {
            width: 100%;
            padding: 10px;
            background-color: #c4985d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .book-btn:hover {
            background-color: #d1a56c; /* Warna lebih terang saat hover */
            color: white;
        }
        .filters-card {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-title {
            font-weight: 600;
            margin-bottom: 10px;
        }
        .reset-btn {
            width: 100%;
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-container {
            max-width: 300px;  /* Maksimal lebar input pencarian */
            width: 100%;  /* Agar responsif */
        }

    </style>
@endsection
@section('content')
    @include('transaction.reservation.progressbar')
    <div class="container mt-4">
        <div class="row">
            <div class="col-12 mb-4 d-flex justify-content-between align-items-center">
                <div class="col-12 mb-4">
                    <h2>Choose Your Room</h2>
                    <p>Select a room for your reservation. Ensure the room fits your preferences and number of guests.</p>

                    <!-- Pencarian Nomor Ruangan -->
                    <div class="d-flex justify-content-end">
                        <input type="text" id="room-search" class="form-control" placeholder="Search by Room Number" onkeyup="searchRooms()" style="max-width: 300px;">
                    </div>
                </div>
        </div>

        <div class="row">
            <!-- Filters Column -->
            <div class="col-md-3">
                <div class="filters-card">
                    <h4>Filters</h4>

                    <form method="GET" action="{{ route('transaction.reservation.chooseRoom', ['customer' => $customer->id]) }}">
                        <input type="text" hidden name="count_person" value="{{ request()->input('count_person') }}">
                        <input type="date" hidden name="check_in" value="{{ request()->input('check_in') }}">
                        <input type="date" hidden name="check_out" value="{{ request()->input('check_out') }}">

                        <!-- Room Type Filter -->
                        <div class="filter-section">
                            <div class="filter-title">Room Type</div>
                            <select class="form-select" name="room_type" id="room-type-select">
                                <option value="">All Types</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('room_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Capacity Filter -->
                        <div class="filter-section">
                            <div class="filter-title">Level / Lt</div>
                            <select class="form-select" name="capacity">
                                <option value="">Any</option>
                                <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ request('capacity') == '3' ? 'selected' : '' }}>3 People</option>
                            </select>
                        </div>

                        <button type="button" class="reset-btn" onclick="resetFilters()">Reset Filters</button>

                    </form>
                </div>
            </div>

            <!-- Rooms Column -->
            <div class="col-md-9">
                <div class="row">
                    @forelse ($rooms as $room)
                        <div class="col-md-6">
                            <div class="room-card shadow-sm">
                                <div class="room-image">
                                    @if($room->firstImage())
                                        <img src="{{ $room->firstImage() }}" alt="{{ $room->type->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        Room Image
                                    @endif
                                </div>
                                <div class="room-details">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="room-title">{{ $room->type->name }}</div>
                                            <div class="room-number">Room {{ $room->number }}</div>
                                        </div>
                                        <div class="room-price">Rp.{{ number_format($room->price) }}</div>
                                    </div>

                                    <div class="room-capacity">
                                        <i class="fas fa-user me-2"></i> Up to {{ $room->capacity }} {{ Str::plural('adult', $room->capacity) }}
                                        <span class="ms-3"><i class="fas fa-vector-square me-1"></i> {{ $room->size ?? 25 }}m²</span>
                                    </div>

                                    <div class="room-description">
                                        {{ Str::limit($room->view, 100) }}
                                    </div>

                                    <div class="room-amenities">
                                        <div class="amenity"><i class="fas fa-wifi"></i></div>
                                        <div class="amenity"><i class="fas fa-tv"></i></div>
                                        <div class="amenity"><i class="fas fa-snowflake"></i></div>
                                    </div>

                                    <a href="{{ route('transaction.reservation.confirmation', ['customer' => $customer->id, 'room' => $room->id, 'from' => request()->input('check_in'), 'to' => request()->input('check_out')]) }}" class="book-btn d-block text-center text-decoration-none">Book Now</a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info">
                                No available rooms found for {{ request()->input('count_person') }} or more person.
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3" id="pagination">
                    {{ $rooms->appends(request()->except('rooms_page'))->links('vendor.pagination.simple-bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function searchRooms() {
        const searchValue = document.getElementById('room-search').value.toLowerCase();  // Ambil input pencarian
        const rooms = document.querySelectorAll('.room-card');  // Ambil semua card ruangan
        const pagination = document.getElementById('pagination');  // Ambil elemen pagination

        rooms.forEach(function(room) {
            const roomNumber = room.querySelector('.room-number').textContent.toLowerCase();  // Ambil nomor ruangan
            if (roomNumber.includes(searchValue)) {
                room.style.display = '';  // Tampilkan jika cocok
            } else {
                room.style.display = 'none';  // Sembunyikan jika tidak cocok
            }
        });

        // Jika ada input pencarian, sembunyikan pagination
        if (searchValue.length > 0) {
            pagination.style.setProperty('display', 'none', 'important');
        } else {
            pagination.style.display = 'flex';  // Tampilkan kembali pagination
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Ambil semua elemen select dalam form filter
        const filterSelects = document.querySelectorAll('.filters-card select');

        // Tambahkan event listener untuk setiap select
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                // Submit form secara otomatis saat ada perubahan
                this.form.submit();
            });
        });

        // Fungsi untuk reset filter
        document.querySelector('.reset-btn').addEventListener('click', function() {
            // Reset semua select ke nilai default
            filterSelects.forEach(select => {
                select.selectedIndex = 0;
            });
            // Submit form
            this.form.submit();
        });
    });


</script>

