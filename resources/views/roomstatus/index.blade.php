@extends('template.master')
@section('title', 'Room')
@section('head')
    <style>
        .text {
            display: block;
            width: 150px;
            height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dataTables_wrapper .dataTables_paginate .page-link {
            color: #c4985d !important;
        }

        /* Warna tombol dan teks saat aktif atau diklik */
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link,
        .dataTables_wrapper .dataTables_paginate .page-link:focus,
        .dataTables_wrapper .dataTables_paginate .page-link:active {
            background-color: #c4985d !important;
            border-color: #c4985d !important;
            color: white !important;
        }

        /* Warna tombol saat hover */
        .dataTables_wrapper .dataTables_paginate .page-link:hover {
            background-color: #d8a677 !important;
            border-color: #d8a677 !important;
            color: white !important;
        }

    </style>
@endsection
@section('content')
    <div class="row">
        <h4 class="text-center p-2 d-block d-sm-none">Room Status</h4>
        <div class="col-lg-12">
            <div class="row mt-2 mb-2">
                <div class="col-lg-12 mb-2">
                    <div class="d-grid gap-2 d-md-block">
                        <button id="add-button" type="button" class="btn btn-md shadow-sm myBtn border rounded fw-semibold fs-6">
                            Add Room Status
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow-sm border">
                        <div class="card-header">
                            <h3>Room Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="roomstatus-table" class="table table-sm table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Code</th>
                                            <th scope="col">Information</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
