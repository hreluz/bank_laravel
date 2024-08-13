<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'amount', 'city'];

    const TYPE_ADD = 'ADD';
    const TYPE_SUBTRACT = 'SUBTRACT';

    /**
     * @return mixed
     */
    public function getDateAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }

    /**
     * @return mixed
     */
    public function getTimeAttribute()
    {
        return $this->created_at->format('H:i:s');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bank_account()
    {
        return $this->belongsTo(Transaction::class);
    }
}
