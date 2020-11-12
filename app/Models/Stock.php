<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\UseCases\TrackStock;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $casts = [
            'in_stock' => 'boolean'
        ];

    public function track()
    {
        TrackStock::dispatch($this);
    }

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
