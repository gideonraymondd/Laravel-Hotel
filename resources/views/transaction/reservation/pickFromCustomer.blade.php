@extends('template.master')
@section('title', 'User')
@section('head')
    <link rel="stylesheet" href="{{ asset('style/css/progress-indication.css') }}">
@endsection
@section('content')
    @include('transaction.reservation.progressbar')
    <div class="container mt-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Select Customer</h2>
        </div>

        <p class="text-muted mb-4">Please choose an existing customer or add a new one.</p>

        <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" class="search-input" placeholder="Search by Name or ID">
        </div>

        <div class="customer-list">
            @foreach($customers as $customer)
                <div class="customer-card">
                    <div class="customer-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="customer-info">
                        <div class="customer-name">{{ $customer->name }}</div>
                        <div class="customer-id">{{ $customer->gender }} • {{ $customer->phone }}</div>
                    </div>

                    <a href="{{ route('transaction.reservation.viewCountPerson', ['customer' => $customer->id]) }}" class="select-btn">Select</a>                </div>
            @endforeach
        </div>


        <div class="new-customer-card">
            <div class="add-icon">
                <i class="fas fa-plus"></i>
            </div>
            <div class="new-customer-title">Create New Customer</div>
            <div class="new-customer-subtitle">Add a new customer to the system</div>
            <button class="create-btn">Create New Customer</button>
        </div>

    </div>
@endsection
<style>
    body {
        background-color: #f8f9fa;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }
    .container {
        max-width: 800px;
        margin: 40px auto;
        padding: 20px;
    }
    .customer-card {
        display: flex;
        align-items: center;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
        margin-bottom: 15px;
        background-color: white;
    }
    .customer-icon {
        width: 40px;
        height: 40px;
        background-color: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    .customer-info {
        flex-grow: 1;
    }
    .customer-name {
        font-weight: 500;
        margin-bottom: 2px;
    }
    .customer-id {
        color: #6c757d;
        font-size: 14px;
    }
    .select-btn {
        background-color: #c4985d;
        color: white;
        border: none;
        padding: 6px 20px;
        border-radius: 4px;
        font-weight: 500;
    }
    .new-customer-card {
        border: 1px dashed #dee2e6;
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        margin-top: 25px;
        background-color: white;
    }
    .add-icon {
        width: 40px;
        height: 40px;
        background-color: #e7f1ff;
        color: #c4985d;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 20px;
    }
    .new-customer-title {
        font-weight: 500;
        margin-bottom: 5px;
    }
    .new-customer-subtitle {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 15px;
    }
    .create-btn {
        background-color: #c4985d;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 4px;
        font-weight: 500;
    }
    .search-box {
        position: relative;
        margin-bottom: 30px;
    }
    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .search-input {
        padding: 12px 12px 12px 40px;
        border-radius: 4px;
        border: 1px solid #ced4da;
        width: 100%;
    }
</style>
