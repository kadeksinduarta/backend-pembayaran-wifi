<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\WifiBill;

class UserPayment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'bill_id', 'amount_due', 'amount_paid', 'status', 'payment_proof'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bill()
    {
        return $this->belongsTo(WifiBill::class, 'bill_id');
    }
}
