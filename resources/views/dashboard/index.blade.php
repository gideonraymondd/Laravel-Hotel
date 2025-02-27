@extends('template.master')
@section('title', 'Dashboard')
@section('content')
    <div id="dashboard">
        <h4 class="text-center p-2 d-block d-sm-none">Dashboard</h4>

        <div class="row m-1">
            <div class="col-sm-12 col-md-6 col-lg-4 p-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Total Revenue</h5>
                    </div>
                    <div class="card-body">
                        <h2 class="card-text text-center">{{ Helper::convertToRupiah ($totalRevenueThisMonth) }}</h2>
                        @if($revenueDifference > 0)
                            <p class="card-text text-center text-success">
                                <i class="fas fa-arrow-up"></i> {{ Helper::convertToRupiah(($revenueDifference )) }} compared to last month
                            </p>
                        @else
                            <p class="card-text text-center text-danger">
                                <i class="fas fa-arrow-down"></i> {{ Helper::convertToRupiah ($revenueDifference ) }}% compared to last month
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 p-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Total Customers</h5>
                    </div>
                    <div class="card-body">
                        <h2 class="card-text text-center">{{ ($totalCustomersThisMonth) }}</h2>
                        @if($customersDifference > 0)
                            <p class="card-text text-center text-success">
                                <i class="fas fa-arrow-up"></i> {{ (($customersDifference )) }} compared to last month
                            </p>
                        @else
                            <p class="card-text text-center text-danger">
                                <i class="fas fa-arrow-down"></i> {{ ($customersDifference ) }}% compared to last month
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-4 p-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Total Bookings</h5>
                    </div>
                    <div class="card-body">
                        <h2 class="card-text text-center">{{  ($totalBookingsThisMonth) }}</h2>
                        @if($bookingsDifference > 0)
                            <p class="card-text text-center text-success">
                                <i class="fas fa-arrow-up"></i> {{ (($bookingsDifference )) }} compared to last month
                            </p>
                        @else
                            <p class="card-text text-center text-danger">
                                <i class="fas fa-arrow-down"></i> {{ ($bookingsDifference ) }}% compared to last month
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-1">
            {{-- Monthly Guests Chart --}}
            <div class="col-lg-5 col-md-6 col-12 mb-3">
                <div class="card shadow-sm border h-100" style="max-height: 400px;">
                    <div class="card-header border-0 py-2">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Monthly Guests Chart</h3>
                        </div>
                    </div>
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between">
                            <p class="d-flex flex-column">
                                <span>Total Bookings at {{ Helper::thisMonth() . '/' . Helper::thisYear() }}</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                                @if($difference > 0)
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> {{ $difference }} Orders
                                    </span>
                                @else
                                    <span class="text-danger">
                                        <i class="fas fa-arrow-down"></i> {{ $difference }} Orders
                                    </span>
                                @endif
                                <span class="text-muted">Since last month</span>
                            </p>
                        </div>
                        <div class="position-relative mb-4">
                            <canvas this-year="{{ Helper::thisYear() }}" this-month="{{ Helper::thisMonth() }}"
                                id="visitors-chart" height="200" width="100%" class="chartjs-render-monitor"></canvas>
                        </div>
                        {{-- <div class="d-flex flex-row justify-content-between">
                            <span class="mr-2">
                                <i class="fas fa-square text-primary"></i> {{ Helper::thisMonth() }}
                            </span>
                            <span>
                                <i class="fas fa-square text-gray"></i> Last month
                            </span>
                        </div> --}}
                    </div>
                </div>
            </div>

            {{-- Reservation Pie Chart --}}
            <div class="col-lg-4 col-md-6 col-12 mb-3">
                <div class="card shadow-sm border h-100" style="max-height: 400px;">
                    <div class="card-header d-flex justify-content-between align-items-center ">
                        <!-- Form Reservation -->
                        <form id="reservationForm" method="GET" action="{{ route('dashboard.index') }}" class="mb-0 w-100">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h4 class="mb-0">Reservations</h4>
                                <select id="timePeriod" name="timePeriod" class="form-select" onchange="this.form.submit()" style="width: auto; margin-left: 10px; min-width: 150px;">
                                    <option value="1d" {{ $timePeriod === '1d' ? 'selected' : '' }}>1D</option>
                                    <option value="1m" {{ $timePeriod === '1m' ? 'selected' : '' }}>1M</option>
                                    <option value="1w" {{ $timePeriod === '1w' ? 'selected' : '' }}>1W</option>
                                    <option value="custom" {{ $timePeriod === 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>

                            <div class="row">
                                <!-- Custom date inputs -->
                                <div id="customDateInput"
                                    style="visibility: {{ $timePeriod === 'custom' ? 'visible' : 'hidden' }};
                                            opacity: {{ $timePeriod === 'custom' ? '1' : '0' }};
                                            height: {{ $timePeriod === 'custom' ? 'auto' : '0' }};
                                            transition: opacity 0.3s ease; background-color: #f8f9fa;
                                            border-radius: 0.25rem; width: 100%; z-index: 0;">
                                    <div class="d-flex flex-column flex-md-row justify-content-start align-items-start align-items-md-center mb-2">
                                        <input type="date" id="startDate" name="startDate" class="form-control d-inline-block w-auto me-md-3 mb-2 mb-md-0" required>
                                        <input type="date" id="endDate" name="endDate" class="form-control d-inline-block w-auto" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-2 py-1">Terapkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body ">
                        @if ($dataForPieChart['checkin'] == 0 && $dataForPieChart['checkout'] == 0 && $dataForPieChart['reserved'] == 0)
                            <!-- Tampilkan pesan jika data tidak ada -->
                            <p class="text-center">Data belum ada.</p>
                        @else
                            <!-- Tampilkan chart jika ada data -->
                            <canvas id="reservationPieChart" style="max-width: 10rem; height: 8rem;"></canvas>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Room Status --}}
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card shadow-sm border h-100" style="max-height: 400px;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Rooms</h4>
                    </div>
                    <div class="card-body">
                        <h5>Twin</h5>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ ($TwinOccupiedRooms / $totalTwin) * 100 }}%;"
                                aria-valuenow="{{ $TwinOccupiedRooms }}"
                                aria-valuemin="0"
                                aria-valuemax="{{ $totalTwin }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p>Total Rooms</p>
                            <p>{{$totalTwin}}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p>Fill Rooms</p>
                            <p>{{$TwinOccupiedRooms}}</p>
                        </div>
                        <br>
                        <h5>Double</h5>
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ ($DoubleOccupiedRooms / $totalDouble) * 100 }}%;"
                                aria-valuenow="{{ $DoubleOccupiedRooms }}"
                                aria-valuemin="0"
                                aria-valuemax="{{ $totalDouble }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p>Total Rooms</p>
                            <p>{{$totalDouble}}</p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <p>Fill Rooms</p>
                            <p>{{$DoubleOccupiedRooms}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row m-1">
            {{-- Today Guest --}}
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
@endsection

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('reservationPieChart').getContext('2d');
        var reservationPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Check-In', 'Check-Out', 'Reserved'],
                datasets: [{
                    data: [{{ $dataForPieChart['checkin'] }}, {{ $dataForPieChart['checkout'] }}, {{ $dataForPieChart['reserved'] }}],
                    backgroundColor: ['#007bff', '#28a745', '#ffc107'],
                    hoverBackgroundColor: ['#0056b3', '#1e7e34', '#d39e00']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>

<style>

    #dashboard {
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



