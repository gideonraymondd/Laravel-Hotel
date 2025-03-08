@if ($filterData->isEmpty())
    <tr>
        <td colspan="6" class="text-center">Tidak ada data yang ditemukan.</td>
    </tr>
@else
    @foreach ($filterData as $transaction)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $transaction->room->number }}</td>
            <td>
                @if ($filter === 'check_in')
                    {!! $transaction->checkedInBy->name ?? '<i>Nama staff tidak ter-assign</i>' !!}
                @elseif ($filter === 'check_out')
                    {!! $transaction->checkedOutBy->name ?? '<i>Nama staff tidak ter-assign</i>' !!}
                @elseif ($filter === 'cleaned')
                    {!! $transaction->cleanedBy->name ?? '<i>Nama staff tidak ter-assign</i>' !!}
                @elseif ($filter === 'reservation')
                    {!! $transaction->createdBy->name ?? '<i>Nama staff tidak ter-assign</i>' !!}
                @endif
            </td>
            <td>
                @if ($filter === 'check_in')
                    {{ $transaction->check_in }}
                @elseif ($filter === 'check_out')
                    {{ $transaction->check_out }}
                @elseif ($filter === 'cleaned')
                    {{ $transaction->cleaned_date }}
                @elseif ($filter === 'reservation')
                    {{ $transaction->created_at }}
                @endif
            </td>
            <td>
                @if($transaction->payment->isNotEmpty())  <!-- Cek apakah ada pembayaran -->
                    {{ $transaction->payment->first()->status }}  <!-- Ambil status dari pembayaran pertama -->
                @else
                    <i>Belum ada pembayaran</i>
                @endif
            </td>
            <td>
                <!-- Tombol Update Payment Status -->
                <button class="btn btn-warning btn-sm update-payment-status" data-id="{{ $transaction->id }}">
                    Update Payment Status
                </button>
            </td>
        </tr>
    @endforeach
@endif



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

    $(document).ready(function() {
        $('#filter-select').change(function() {
            let filterValue = $(this).val();

            $.ajax({
                url: "{{ route('transaction.index') }}",
                type: "GET",
                data: { filter: filterValue },
                success: function(response) {
                    $('#room-table').html(response);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });
    });

    $(document).ready(function() {
        // Ketika tombol Update Payment Status diklik
        $('.update-payment-status').click(function() {
            let id = $(this).data('id');  // Ambil ID transaksi dari data-id

            // Mengonfirmasi update pembayaran
            if (confirm("Apakah Anda yakin ingin memperbarui status pembayaran?")) {
                // Kirim request AJAX ke server
                $.ajax({
                    url: "{{ route('transaction.updatePaymentStatus') }}",  // Ganti dengan route yang sesuai
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",  // Kirim CSRF token untuk keamanan
                        id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            alert("Status pembayaran berhasil diperbarui.");
                            location.reload();  // Reload halaman untuk melihat perubahan
                        } else {
                            alert("Gagal memperbarui status pembayaran.");
                        }
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);
                        alert("Terjadi kesalahan: " + xhr.responseText);
                    }

                });
            }
        });
    });
</script>

