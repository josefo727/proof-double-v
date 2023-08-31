<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use App\Services\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['total', 'status', 'customer_id', 'user_id'];

    protected $searchableFields = ['*'];

    protected $appends = [
        'state'
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($order) {
            $order->status = OrderStatus::CREATED;
            $order->total = 0;
        });
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'price', 'subtotal');
    }

    /**
     * @return void
     * @param mixed $newStatus
     */
    public function changeStatus($newStatus): void
    {
        if (!$this->state->canChangeStatus($newStatus)) {
            throw new \Exception('Estado de transición no válido');
        }
        $this->status = $newStatus;
        $this->save();
    }

    /**
     * @return OrderStatus
     */
    public function getStateAttribute(): OrderStatus
    {
        return new OrderStatus($this->status);
    }
}
