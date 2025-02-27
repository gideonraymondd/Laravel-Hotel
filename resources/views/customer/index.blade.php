@extends('template.master')

@section('title', 'Customer')

@section('content')

<style>
    /* Custom background style */
    .mybg {
        background-image: linear-gradient(#1975d1, #1975d1);
    }

    /* Styling for numbering elements */
    .numbering {
        width: 50px;
        height: 50px;
        align-items: center;
        justify-content: center;
        padding-top: 12px;
        text-align: center;
        border-bottom-right-radius: 30px;
        border-top-left-radius: 5px;
    }

    /* Icon styling */
    .icon {
        font-size: 1.5rem;
        margin-right: -10px;
        color: #212529;
    }

    /* Styling for the chart canvas */
    .chart-canvas {
        width: 100% !important;
        max-width: 300px; /* Limit chart size */
        max-height: 300px;
        aspect-ratio: 1 / 1;
    }

    /* Margin adjustment for rows */
    .row {
        margin-right: 1%;
    }
</style>

<div class="row">
    <!-- Title for small screens -->
    <h4 class="text-center p-2 d-block d-sm-none">Customer</h4>
    <div class="col-lg-12">
        {{-- Header section --}}
        <div class="row mt-2 mb-2">
            <div class="col-lg-6 mb-2">
                <!-- Add Customer Button -->
                <a href="{{ route('customer.create') }}" class="btn btn-md shadow-sm myBtn border rounded fw-semibold fs-6">
                    Add Customer
                </a>
            </div>
        </div>

        {{-- Body section --}}
        <div class="row">
            {{-- Time filter dropdown --}}
            <form method="GET" id="timeFilterForm" class="d-inline-block mb-2">
                <div class="input-group input-group-sm">
                    <select name="time" id="timeFilter" class="form-select form-select-sm" onchange="document.getElementById('timeFilterForm').submit();" style="width: auto; padding: 4px; margin: 4px;">
                        <option value="1d" {{ $selectedTime == '1d' ? 'selected' : '' }}>1 Hari</option>
                        <option value="1w" {{ $selectedTime == '1w' ? 'selected' : '' }}>1 Minggu</option>
                        <option value="1m" {{ $selectedTime == '1m' ? 'selected' : '' }}>1 Bulan</option>
                        <option value="1y" {{ $selectedTime == '1y' ? 'selected' : '' }}>1 Tahun</option>
                        <option value="custom" {{ $selectedTime == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
            </form>

            {{-- Chart section --}}
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer Origin</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <canvas id="originChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Customer Age</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <canvas id="ageChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Table --}}
        <div class="card shadow-sm border">
            <div class="card-header">
                <h4 class="card-title">Customers</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Address</th>
                                <th scope="col">Birth Date</th>
                                <th scope="col">Gender</th>
                                <th scope="col">Job</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $customer)
                                <tr>
                                    <td>
                                        <a href="{{ route('customer.show', ['customer' => $customer->id]) }}">
                                            {{ $customer->name }}
                                        </a>
                                    </td>
                                    <td>{{ $customer->address }}</td>
                                    <td>{{ $customer->birthdate }}</td>
                                    <td>{{ $customer->gender }}</td>
                                    <td>{{ $customer->job }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="{{ route('customer.edit', ['customer' => $customer->id]) }}" class="btn btn-sm btn-primary">Edit</a>

                                        <!-- Delete Button with SweetAlert -->
                                        <button class="btn btn-sm btn-danger delete"
                                                data-customer-id="{{ $customer->id }}"
                                                data-customer-name="{{ $customer->name }}"
                                                data-customer-url="{{ route('customer.destroy', ['customer' => $customer->id]) }}">
                                            Delete
                                        </button>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No customer data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')

<script>
    // Ensure time filter dropdown value is correctly set
    document.addEventListener('DOMContentLoaded', function () {
        const timeFilter = document.getElementById('timeFilter');
        const selectedTime = "{{ $selectedTime }}"; // Get selected time value from backend

        // Set selected value for the dropdown
        if (["1d", "1w", "1m", "1y", "custom"].includes(selectedTime)) {
            timeFilter.value = selectedTime;
        }
    });

    // Handle delete confirmation via SweetAlert
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.delete').forEach(button => {
            button.addEventListener('click', function () {
                let customerId = this.getAttribute('data-customer-id');
                let customerName = this.getAttribute('data-customer-name');
                let customerUrl = this.getAttribute('data-customer-url');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to delete ${customerName}. This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(customerUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Content-Type': 'application/json'
                            }
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    // Hapus baris dari tabel tanpa reload
                                    let row = document.querySelector(`button[data-customer-id='${customerId}']`).closest('tr');
                                    row.remove();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        })
                        .catch(error => console.error('Error:', error));
                    }
                });
            });
        });
    });
</script>

{{-- Import Chart.js library --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Chart.js for displaying age distribution --}}
<script>
    var ageChart = new Chart(document.getElementById('ageChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($ageLabels), // Age labels
            datasets: [{
                label: 'Number of Customers',
                data: @json($ageCounts), // Customer counts by age
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

{{-- Chart.js for displaying customer origin --}}
<script>
    var originChart = new Chart(document.getElementById('originChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($originLabels), // Origin labels
            datasets: [{
                label: 'Number of Transactions by Origin',
                data: @json($originCounts), // Customer counts by origin
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

@endsection
