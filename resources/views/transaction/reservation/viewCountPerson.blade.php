@extends('template.master')
@section('title', 'Count Person')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
@endsection
@section('content')
    @include('transaction.reservation.progressbar')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Reservation Details </h2>
        </div>

        <p class="text-muted mb-4">Please provide the necessary details for your reservation.</p>
        <form class="row g-3" method="GET" action="{{ route('transaction.reservation.chooseRoom', ['customer' => $customer->id]) }}">
            <div class="reservation-card">
                <h2 class="section-title">Reservation Dates </h2>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-2 date-label">Check-in Date</div>
                        <div class="date-input-group">
                            <input type="date" class="date-input"  name="check_in">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2 date-label">Check-out Date</div>
                        <div class="date-input-group">
                            <input type="date" class="date-input"  name="check_out">
                        </div>
                    </div>
                </div>

                <h2 class="section-title">Number of Guests</h2>
                <div class="guest-help-text">Number of Guests (max. 2 guests per reservation)</div>
                <div class="mb-4">
                    <select class="dropdown-select" name="count_person">
                        <option value="2">2 Guests</option>
                        <option value="1">1 Guest</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button type="button" class="cancel-btn" onclick="window.location.href='cancel-reservation.php'">
                    <i class="fas fa-arrow-left"></i> Cancel Reservation
                </button>
                <button type="submit" class="next-btn">
                    Next Step <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>

    </div>
@endsection
<style>
    .container {
        max-width: 800px;
        margin: 16px auto;
        padding: 20px;
    }
    .reservation-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 25px;
        margin-bottom: 20px;
    }
    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .date-label {
        font-size: 14px;
        color: #333;
        margin-bottom: 8px;
    }
    .date-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 16px;
    }
    .date-input-group {
        position: relative;
    }
    .calendar-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .dropdown-select {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 16px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23212529' viewBox='0 0 16 16'%3E%3Cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }
    .guest-help-text {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 8px;
    }
    .special-requests-textarea {
        width: 100%;
        min-height: 100px;
        padding: 10px 12px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 16px;
        resize: vertical;
    }
    .button-group {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    .cancel-btn {
        display: flex;
        align-items: center;
        color: #6c757d;
        background: none;
        border: none;
        padding: 10px 16px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
    }
    .cancel-btn i {
        margin-right: 8px;
    }
    .next-btn {
        background-color: #c4985d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    .next-btn i {
        margin-left: 8px;
    }
</style>
