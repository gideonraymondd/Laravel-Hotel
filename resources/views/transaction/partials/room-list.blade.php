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
    {{ $allRooms->appends(request()->except('rooms_page'))->links('vendor.pagination.simple-bootstrap-5') }}
</div>
