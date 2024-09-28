@extends('template.master')
@section('title',' Detail Reservation' )
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-9 mt-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Transaction Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label class=" col-sm-2 col-form-label">Room</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ $transaction->room->number }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Order Date</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"
                                    value="{{ Helper::dateFormat($transaction->check_in) }} - {{ Helper::dateFormat($transaction->check_out) }}" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Total Price</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"
                                    value="{{ Helper::convertToRupiah($transaction->getTotalPrice($transaction->room->price, $transaction->check_in, $transaction->check_out)) }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Order Status</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control"
                                    value="{{ $transaction->status }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Room Status</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control"
                                    value="{{ $transaction->room_status }}"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Ordered By</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"
                                    value="{{ $transaction->createdBy ? $transaction->createdBy->name : '-' }}"
                                    readonly>
                            </div>
                            <label class="col-sm-2 col-form-label">Created At</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"
                                    value="{{ Helper::dateFormatTime($transaction->created_at)}}"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Check In By</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"
                                    value="{{ $transaction->checkedInBy ? $transaction->checkedInBy->name : '-' }}"
                                    readonly>
                            </div>
                            <label class="col-sm-2 col-form-label">Check In At</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"
                                    value="{{ Helper::dateFormatTime($transaction->checked_in_time)}}"
                                    readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Check Out Section -->
                            <label class="col-sm-2 col-form-label">Check Out By</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"
                                    value="{{ $transaction->checkedOutBy ? $transaction->checkedOutBy->name : '-' }}"
                                    readonly>
                            </div>
                            <label class="col-sm-2 col-form-label">Check Out At</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"
                                    value="{{ Helper::dateFormatTime($transaction->checked_out_time)}}"
                                    readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Clean Section -->
                            <label class="col-sm-2 col-form-label">Cleaned By</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"
                                    value="{{ $transaction->cleanedBy ? $transaction->cleanedBy->name : '-' }}"
                                    readonly>
                            </div>
                            <label class="col-sm-2 col-form-label">Cleaned At</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control"
                                    value="{{ Helper::dateFormatTime($transaction->cleaned_time)}}"
                                    readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 mt-2">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title">Customer Details</h5>
                    </div>
                    {{-- Image --}}

                    {{-- <img src="{{ $transaction->customer->user->getAvatar() }}"
                        style="border-top-right-radius: 0.5rem; border-top-left-radius: 0.5rem"> --}}

                    <div class="card-body">
                        <table>
                            <tr>
                                <td style="text-align: center; width:50px">
                                    <span>
                                        <p>Name:</p>
                                    </span>
                                </td>
                                <td>
                                    {{ $transaction->customer->name }}
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; ">
                                    <span>
                                        <p>Job:</p>
                                    </span>
                                </td>
                                <td>{{ $transaction->customer->job }}</td>
                            </tr>
                            <tr>
                                <td style="text-align: start; ">
                                    <span>
                                        <p>Birthday:</p>
                                    </span>
                                </td>
                                <td>
                                    {{ Helper::dateFormat($transaction->customer->birthdate) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center; ">
                                    <span>
                                        <p>Address:</p>
                                    </span>
                                </td>
                                <td>
                                    {{ $transaction->customer->address }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer')
<script>
    $('#payment').keyup(function() {
        $('#showPaymentType').text('Rp. ' + parseFloat($(this).val(), 10).toFixed(2).replace(
                /(\d)(?=(\d{3})+\.)/g, "$1,")
            .toString());
    });

</script>
@endsection
