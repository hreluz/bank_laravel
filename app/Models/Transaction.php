<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'amount'];

    const TYPE_ADD = 'ADD';
    const TYPE_SUBTRACT = 'SUBTRACT';

    public function getDateAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    public function getTimeAttribute()
    {
        return $this->created_at->format('H:i:s');
    }
}
