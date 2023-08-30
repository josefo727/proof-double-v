<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use App\Services\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['total', 'status', 'customer_id', 'user_id'];

    protected $searchableFields = ['*'];

    protected $appends = [
        'state'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->status = OrderStatus::CREATED;
            if(!app()->runningInConsole()) {
                $order->total = 0;
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price', 'subtotal');
    }

    public function changeStatus($newStatus)
    {
        if ($this->state->canChangeStatus($newStatus)) {
            $this->status = $newStatus;
            $this->save();
        }
    }

    public function getStateAttribute()
    {
        return new OrderStatus($this->status);
    }
}
