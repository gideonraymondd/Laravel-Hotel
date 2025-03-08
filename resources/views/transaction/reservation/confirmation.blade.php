@extends('template.master')
@section('title', 'Payment Confirmation')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
    <style>
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e0e0e0;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 20px;
            position: relative;
        }
        .step.active {
            background-color: #4a90e2;
            color: white;
        }
        .step::before {
            content: '';
            position: absolute;
            height: 2px;
            width: 40px;
            background-color: #e0e0e0;
            right: 100%;
            top: 50%;
            transform: translateY(-50%);
        }
        .step:first-child::before {
            display: none;
        }
        .payment-method {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }
        .payment-method label {
            margin-left: 10px;
            font-weight: 500;
        }
        .card-details-section {
            margin-top: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
        }
        .billing-section {
            margin-top: 20px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
        }
        .summary-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
        .confirm-btn {
            width: 100%;
            padding: 12px;
            background-color: #0f172a;
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 500;
        }
        .back-btn {
            width: 100%;
            padding: 12px;
            background-color: white;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: 500;
        }
    </style>
@endsection
@section('content')
    @include('transaction.reservation.progressbar')
    <div class="container mt-4">
        <h4 class="mb-2">Payment</h4>
        <p class="text-muted mb-4">Review your reservation details and proceed to payment.</p>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Reservation Summary</h5>
                        <div class="row mb-3">
                            <div class="col-6">
                                <p class="mb-1 fw-bold">{{ $room->type->name }}</p>
                                <p class="text-muted">{{ Helper::convertToRupiah($room->price) }} per night</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Check-In</p>
                                    <p class="fw-bold">{{ date('F j, Y', strtotime($stayFrom)) }}</p>
                                </div>
                                <div>
                                    <p class="mb-1 text-muted">Total Nights</p>
                                    <p class="fw-bold">{{ $dayDifference }} {{ Helper::plural('Night', $dayDifference) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <p class="mb-1 text-muted">Check-Out</p>
                                    <p class="fw-bold">{{ date('F j, Y', strtotime($stayUntil)) }}</p>
                                </div>
                                <div>
                                    <p class="mb-1 text-muted">Guests</p>
                                    <p class="fw-bold">{{ $room->capacity }} {{ Helper::plural('Adult', $room->capacity) }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- <h5 class="card-title mb-3 mt-4">Payment Method</h5> --}}
                        <form method="POST" action="{{ route('transaction.reservation.payDownPayment', ['customer' => $customer->id, 'room' => $room->id]) }}">
                            @csrf
                            <input type="hidden" name="check_in" value="{{ $stayFrom }}">
                            <input type="hidden" name="check_out" value="{{ $stayUntil }}">

                            {{-- <div class="payment-method">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="creditCard" checked>
                                    <label class="form-check-label" for="creditCard">
                                        <i class="fas fa-credit-card"></i> Credit/Debit Card
                                    </label>
                                </div>
                            </div>

                            <div class="payment-method">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="bankTransfer" value="bankTransfer">
                                    <label class="form-check-label" for="bankTransfer">
                                        <i class="fas fa-university"></i> Bank Transfer
                                    </label>
                                </div>
                            </div>

                            <div class="payment-method">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <i class="fab fa-paypal"></i> PayPal
                                    </label>
                                </div>
                            </div> --}}

                            <div class="row mb-3">
                                <label for="origin" class="form-label mt-3">Booking Origin</label>
                                <div class="col-sm-12">
                                    <select class="form-control @error('origin') is-invalid @enderror" id="origin" name="origin">
                                        <option value="">Select origin</option>
                                        <option value="traveloka" {{ old('origin') == 'traveloka' ? 'selected' : '' }}>Traveloka</option>
                                        <option value="tiket.com" {{ old('origin') == 'tiket.com' ? 'selected' : '' }}>Tiket.com</option>
                                        <option value="booking.com" {{ old('origin') == 'booking.com' ? 'selected' : '' }}>Booking.com</option>
                                        <option value="offline" {{ old('origin') == 'offline' ? 'selected' : '' }}>Offline</option>
                                    </select>
                                    @error('origin')
                                        <div class="text-danger mt-1">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Price Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <p>Room Price ({{ $dayDifference }} {{ Helper::plural('night', $dayDifference) }})</p>
                            <p>{{ Helper::convertToRupiah($room->price * $dayDifference) }}</p>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2 fw-bold">
                            <p>Total</p>
                            <p>{{ Helper::convertToRupiah(Helper::getTotalPayment($dayDifference, $room->price)) }}</p>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <p>Minimum Down Payment</p>
                            <p>{{ Helper::convertToRupiah($downPayment) }}</p>
                        </div>

                        <div class="mb-3">
                            <label for="downPayment" class="form-label fw-bold">Payment Amount</label>
                            <input type="text" class="form-control @error('downPayment') is-invalid @enderror"
                                    id="downPayment" name="downPayment" placeholder="Input payment here"
                                    value="{{ old('downPayment', $downPayment) }}">
                            <div id="showPaymentType" class="form-text mt-2"></div>
                            @error('downPayment')
                                <div class="text-danger mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="confirm-btn">Confirm Payment</button>
                        </form>

                        <div class="d-flex align-items-center justify-content-center mt-3">
                            <i class="fas fa-lock me-2"></i>
                            <small class="text-muted">Secure payment via SSL encryption</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
<script>
    $(document).ready(function() {
        // Format currency input
        $('#downPayment').keyup(function() {
            $('#showPaymentType').text('Rp. ' + parseFloat($(this).val(), 10).toFixed(2).replace(
                    /(\d)(?=(\d{3})+\.)/g, "$1.")
                .toString());
        });

        // Show/hide payment details based on selected method
        $('input[name="paymentMethod"]').change(function() {
            if ($(this).val() === 'creditCard') {
                $('#cardDetails').show();
            } else {
                $('#cardDetails').hide();
            }
        });
    });
</script>
@endsection
