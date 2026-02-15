<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\UserPayment;

class WifiBill extends Model
{
    use HasFactory;

    protected $fillable = ['month', 'total_amount', 'created_by', 'wifi_password'];
    protected $hidden = ['wifi_password'];

    public function payments()
    {
        return $this->hasMany(UserPayment::class, 'bill_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
