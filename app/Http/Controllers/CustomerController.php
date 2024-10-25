<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Repositories\Interface\CustomerRepositoryInterface;
use App\Repositories\Interface\ImageRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;


class CustomerController extends Controller
{
    public function __construct(
        private CustomerRepositoryInterface $customerRepository
    ) {
    }

    public function index(Request $request)
    {
        $customers = $this->customerRepository->get($request);
        $time = $request->input('time', '1y'); // Default: 1 tahun
        $query = Transaction::with('customer');

        // Tentukan rentang tanggal berdasarkan pilihan waktu
        switch ($time) {
            case '1d':
                $startDate = now()->subDay();
                break;
            case '1w':
                $startDate = now()->subWeek();
                break;
            case '1m':
                $startDate = now()->subMonth();
                break;
            case '1y':
                $startDate = now()->subYear();
                break;
            case 'custom':
                $startDate = $request->input('start_date');
                $endDate = $request->input('end_date');
                break;
            default:
                $startDate = now()->subYear();
                break;
        }

        // Filter data transaksi berdasarkan waktu yang dipilih
        if ($time === 'custom' && $startDate && $endDate) {
            $transactions = $query->whereBetween('created_at', [$startDate, $endDate])->get();
        } else {
            $transactions = $query->where('created_at', '>=', $startDate)->get();
        }

        // Proses data untuk chart
        $originData = $transactions->pluck('origin')->countBy();
        $ageData = $transactions->map(fn($transaction) => Carbon::parse($transaction->customer->birthdate)->age);
        $ageCounts = $ageData->countBy();

        return view('customer.index', [
            'customers' => $customers,
            'ageLabels' => $ageCounts->keys(),
            'ageCounts' => $ageCounts->values(),
            'originLabels' => $originData->keys(),
            'originCounts' => $originData->values(),
            'selectedTime' => $time
        ]);
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerRepository->store($request);

        return redirect('customer')->with('success', 'Customer '.$customer->name.' created');
    }

    public function show(Customer $customer)
    {
        return view('customer.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit', ['customer' => $customer]);
    }

    public function update(Customer $customer, StoreCustomerRequest $request)
    {
        $customer->update($request->all());

        return redirect('customer')->with('success', 'customer '.$customer->name.' udpated!');
    }

    public function destroy(Customer $customer, ImageRepositoryInterface $imageRepository)
    {
        try {
            $user = User::find($customer->user->id);
            $avatar_path = public_path('img/user/'.$user->name.'-'.$user->id);

            $customer->delete();
            $user->delete();

            if (is_dir($avatar_path)) {
                $imageRepository->destroy($avatar_path);
            }

            return redirect('customer')->with('success', 'Customer '.$customer->name.' deleted!');
        } catch (\Exception $e) {
            $errorMessage = '';
            if ($e->errorInfo[0] == '23000') {
                $errorMessage = 'Data still connected to other tables';
            }

            return redirect('customer')->with('failed', 'Customer '.$customer->name.' cannot be deleted! '.$errorMessage);
        }
    }
}
