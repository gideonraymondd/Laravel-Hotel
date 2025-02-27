@extends('template.auth')
@section('title', 'Login')

@section('content')
    <!-- Link ke file CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">

    <div class="container-fluid d-flex flex-column align-items-center justify-content-center min-vh-100">
        <div class="form-logo text-center mb-2">
            <img src="{{ asset('img/Logo.png') }}" alt="Kana Logo" style="max-width: 180px; height: auto; margin-left:6px;">
        </div>
        <div class="form-container">
            <h3 class="form-title"> Kanna Backoffice </h3>
            <h5 class="form-title"> Login ke Akun Anda </h5>
            <form onsubmit="return disableButton()" class="form-signin" action="/postLogin" method="POST">
                @csrf
                <div class="group-divide">
                    <label for="email" class="font-weight-bold">
                        @lang('Email') <span class="text-danger">*</span>
                    </label>
                    <div class="form-label-group">
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="group-divide">
                    <label for="password" class="font-weight-bold">
                        @lang('Password') <span class="text-danger">*</span>
                    </label>
                    <div class="form-label-group">
                        <input type="password" id="password" name="password" autocomplete="new-password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="w-100 d-flex justify-content-center">
                            <button id="btn_submit" class="btn btn-lg btn-primary text-white fw-bold p-2"
                                    type="submit" style="border-radius: 0.5rem; background-color:#c4985d; border: none">
                                <div id="loading_submit" class="spinner-border hide" role="status"
                                    style="width: 15px; height: 15px">
                                </div>
                                <div id="text_submit">
                                    Login
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function disableButton() {
            $("#loading_submit").removeClass("hide");
            $("#text_submit").addClass("hide");
            $("#btn_submit").addClass("isLoading").attr('disabled', 'disabled');
        }
    </script>
@endsection
