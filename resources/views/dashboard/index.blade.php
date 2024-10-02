@extends('template.master')
@section('title', 'Dashboard')
@section('content')
    <div id="dashboard">
        <div class="row">
            <div class="col-lg-6 mb-3">
                {{-- Room Status --}}
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card shadow-sm border">
                            <div class="card-header">
                                <h3>Rooms Status Today</h3>
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
                                                    <h5 class="card-title">Nomor: {{ $room->number }}</h5>
                                                    <p class="card-text">Lantai: {{ $room->floor }}</p>
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
                </div>
                {{-- Today Guest --}}
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card shadow-sm border">
                            <div class="card-header">
                                <div class="row ">
                                    <div class="col-lg-12 d-flex justify-content-between">
                                        <h3>Today Guests</h3>
                                        <div>
                                            <a href="#" class="btn btn-tool btn-sm">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <a href="#" class="btn btn-tool btn-sm">
                                                <i class="fas fa-bars"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Name</th>
                                            <th>Room</th>
                                            <th class="text-center">Stay</th>
                                            <th>Day Left</th>
                                            <th>Debt</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($transactions as $transaction)
                                            <tr>
                                                <td>
                                                    <img src="{{ $transaction->customer->user->getAvatar() }}"
                                                        class="rounded-circle img-thumbnail" width="40" height="40"
                                                        alt="">
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ route('customer.show', ['customer' => $transaction->customer->id]) }}">
                                                        {{ $transaction->customer->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <a href="{{ route('room.show', ['room' => $transaction->room->id]) }}">
                                                        {{ $transaction->room->number }}
                                                    </a>
                                                </td>
                                                <td>
                                                    {{ Helper::dateFormat($transaction->check_in) }} ~
                                                    {{ Helper::dateFormat($transaction->check_out) }}
                                                </td>
                                                <td>{{ Helper::getDateDifference(now(), $transaction->check_out) == 0 ? 'Last Day' : Helper::getDateDifference(now(), $transaction->check_out) . ' ' . Helper::plural('Day', Helper::getDateDifference(now(), $transaction->check_out)) }}
                                                </td>
                                                <td>
                                                    {{ $transaction->getTotalPrice() - $transaction->getTotalPayment() <= 0 ? '-' : Helper::convertToRupiah($transaction->getTotalPrice() - $transaction->getTotalPayment()) }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="justify-content-center badge {{ $transaction->getTotalPrice() - $transaction->getTotalPayment() == 0 ? 'bg-success' : 'bg-warning' }}">
                                                        {{ $transaction->getTotalPrice() - $transaction->getTotalPayment() == 0 ? 'Success' : 'Progress' }}
                                                    </span>
                                                    @if (Helper::getDateDifference(now(), $transaction->check_out) < 1)
                                                        <span class="justify-content-center badge bg-danger">
                                                            must finish payment
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">
                                                    There's no data in this table
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {{-- <div class="row justify-content-md-center mt-3">
                                    <div class="col-sm-10 d-flex mx-auto justify-content-md-center">
                                        <div class="pagination-block">
                                            {{ $transactions->onEachSide(1)->links('template.paginationlinks') }}
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="container">
                            <h1>Room Status for {{ $date }}</h1>

                            <!-- Tombol Filter -->
                            <div class="btn-group mb-3">
                                <a href="{{ route('dashboard.index', ['filter' => 'check_in']) }}" class="btn btn-primary">Check-In</a>
                                <a href="{{ route('dashboard.index', ['filter' => 'check_out']) }}" class="btn btn-success">Check-Out</a>
                                <a href="{{ route('dashboard.index', ['filter' => 'cleaned']) }}" class="btn btn-warning">Cleaned</a>
                            </div>

                            <!-- Tabel Status Kamar -->
                            <div class="card mt-3">
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
                </div>


                {{-- Monthly Guests Chart --}}
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card shadow-sm border">
                            <div class="card-header border-0">
                                <div class="d-flex justify-content-between">
                                    <h3 class="card-title">Monthly Guests Chart</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <p class="d-flex flex-column">
                                        {{-- <span class="text-bold text-lg">Belum</span> --}}
                                        {{-- <span>Total Guests at {{ Helper::thisMonth() . '/' . Helper::thisYear() }}</span> --}}
                                    </p>
                                    {{-- <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> Belum
                                    </span>
                                    <span class="text-muted">Since last month</span>
                                </p> --}}
                                </div>
                                <div class="position-relative mb-4">
                                    <canvas this-year="{{ Helper::thisYear() }}" this-month="{{ Helper::thisMonth() }}"
                                        id="visitors-chart" height="400" width="100%" class="chartjs-render-monitor"
                                        style="display: block; width: 249px; height: 200px;"></canvas>
                                </div>
                                <div class="d-flex flex-row justify-content-between">
                                    <span class="mr-2">
                                        <i class="fas fa-square text-primary"></i> {{ Helper::thisMonth() }}
                                    </span>
                                    <span>
                                        <i class="fas fa-square text-gray"></i> Last month
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
{{-- @section('footer')
    <script src="{{ asset('style/js/chart.min.js') }}"></script>
    <script src="{{ asset('style/js/guestsChart.js') }}"></script>
    <script>
        function reloadJs(src) {
            src = $('script[src$="' + src + '"]').attr("src");
            $('script[src$="' + src + '"]').remove();
            $('<script/>').attr('src', src).appendTo('head');
        }

        Echo.channel('dashboard')
            .listen('.dashboard.event', (e) => {
                $("#dashboard").hide()
                $("#dashboard").load(window.location.href + " #dashboard");
                $("#dashboard").show(150)
                reloadJs('style/js/guestsChart.js');
                toastr.warning(e.message, "Hello, {{ auth()->user()->name }}");
            })
    </script>
@endsection --}}
