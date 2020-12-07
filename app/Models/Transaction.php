<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable=['client', 'transaction_date', 'amount'];
    use HasFactory;

    public function clientData()
    {
    	return $this->belongsTo(Client::class, 'client');
    }
}
