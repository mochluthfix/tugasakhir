<?php

namespace App\Models;

use App\Enums\OrderGender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'nomeja',
        'phone',
        'total_price',
        'payment_status',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'integer',
            'nomeja' => 'integer',
        ];
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }


}
