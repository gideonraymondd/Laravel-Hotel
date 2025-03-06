<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <ul class="progress-indicator m-4">
                    <li
                        class="{{ Route::currentRouteName() == 'transaction.reservation.createIdentity' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.pickFromCustomer' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.viewCountPerson' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.chooseRoom' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.confirmation' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.payDownPayment' ? 'completed' : '' }}">
                        <span class="bubble"></span> Customer Detail
                    </li>
                    <li
                        class="{{ Route::currentRouteName() == 'transaction.reservation.viewCountPerson' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.chooseRoom' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.confirmation' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.payDownPayment' ? 'completed' : '' }}">
                        <span class="bubble"></span> How many person?
                    </li>
                    <li
                        class="{{ Route::currentRouteName() == 'transaction.reservation.chooseRoom' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.confirmation' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.payDownPayment' ? 'completed' : '' }}">
                        <span class="bubble"></span> Pick a room
                    </li>
                    <li
                        class="{{ Route::currentRouteName() == 'transaction.reservation.confirmation' ? 'completed' : '' }} {{ Route::currentRouteName() == 'transaction.reservation.payDownPayment' ? 'completed' : '' }}">
                        <span class="bubble"></span> Confirmation
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .progress-indicator li.completed {
        color: #c4985d; /* Ubah warna teks */
    }

    .progress-indicator li.completed .bubble {
        background: #c4985d; /* Ubah warna latar belakang bubble */
        color: #fff; /* Ubah warna teks di dalam bubble jika ada */
        border-color: #c4985d; /* Ubah warna border bubble */
    }

    .progress-indicator > li.completed .bubble,
    .progress-indicator > li.completed .bubble:after,
    .progress-indicator > li.completed .bubble:before {
        background-color: #c4985d; /* Ganti dengan warna yang diinginkan */
        border-color: #c4985d; /* Ganti border dengan warna yang diinginkan */
    }

</style>

