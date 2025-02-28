@if ($filterData->isEmpty())
    <tr>
        <td colspan="5" class="text-center">Tidak ada data yang ditemukan.</td>
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
</script>

