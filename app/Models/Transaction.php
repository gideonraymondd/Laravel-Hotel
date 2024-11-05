<?php

namespace App\Models;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'room_id',
        'check_in',
        'check_out',
        'status',
        'origin',
        'created_by',
        'created_at',
        'checked_in_time',
        'checked_out_time',
        'cleaned_time',
        'group_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPrice()
    {
        $day = Helper::getDateDifference($this->check_in, $this->check_out);
        $room_price = $this->room->price;

        return $room_price * $day;
    }

    public function getDateDifferenceWithPlural()
    {
        $day = Helper::getDateDifference($this->check_in, $this->check_out);
        $plural = Str::plural('Day', $day);

        return $day.' '.$plural;
    }

    public function getTotalPayment()
    {
        $totalPayment = 0;
        foreach ($this->payment as $payment) {
            $totalPayment += $payment->price;
        }

        return $totalPayment;
    }

    public function getMinimumDownPayment()
    {
        $dayDifference = Helper::getDateDifference($this->check_in, $this->check_out);

        return ($this->room->price * $dayDifference) * 0.15;
    }

    public function isPaymentComplete()
    {
        // Dapatkan total harga transaksi
        $totalPrice = $this->getTotalPrice();

        // Dapatkan total pembayaran yang telah dilakukan
        $totalPayment = $this->getTotalPayment();

        // Periksa apakah total pembayaran sudah sama dengan atau lebih dari total harga
        return $totalPayment >= $totalPrice;
    }

    // Relasi dengan user
    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function cleanedBy()
    {
        return $this->belongsTo(User::class, 'cleaned_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }



}
