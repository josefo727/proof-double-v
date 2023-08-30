<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['sku', 'name', 'price', 'quantity', 'user_id'];

    protected $searchableFields = ['*'];

    protected $cast = [
        'price' => 'float'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
