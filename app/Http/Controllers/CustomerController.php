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
use Illuminate\Database\QueryException;


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
        try {
            // Validasi email tidak duplikat
            $request->validate([
                'email' => 'unique:users,email,' . $customer->user_id, // Menambahkan pengecekan unik untuk email
            ]);

            // Update customer data
            $customer->update($request->all());

            // Update email di tabel users berdasarkan user_id di customer
            User::where('id', $customer->user_id)->update([
                'email' => $request->email
            ]);

            return redirect('customer')->with('success', 'Customer '.$customer->name.' updated!');
        } catch (\Illuminate\Database\QueryException $e) {
            // Tangkap error jika email sudah digunakan
            if ($e->errorInfo[1] == 1062) { // 1062 adalah kode error MySQL untuk duplicate entry
                return redirect()->back()->withInput()->withErrors(['email' => 'Email ' . $request->email . ' sudah digunakan oleh user lain!']);
            }

            // Tangkap error lain
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat update customer!']);
        }
    }


    public function destroy(Customer $customer, ImageRepositoryInterface $imageRepository)
    {
        try {
            // Cek apakah customer memiliki transaksi terkait
            if ($customer->transactions()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer '.$customer->name.' cannot be deleted! There are transactions related to this customer.'
                ]);
            }

            $user = User::find($customer->user->id);
            $avatar_path = public_path('img/user/'.$user->name.'-'.$user->id);

            $customer->delete();
            $user->delete();

            if (is_dir($avatar_path)) {
                $imageRepository->destroy($avatar_path);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer '.$customer->name.' deleted!'
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->errorInfo[0] == '23000' ? 'Data still connected to other tables' : '';

            return response()->json([
                'success' => false,
                'message' => 'Customer '.$customer->name.' cannot be deleted! '.$errorMessage
            ]);
        }
    }

}
