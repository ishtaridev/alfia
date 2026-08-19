<?php

namespace App\Models;

use App\Enums\RoomType;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'customer',
        'phone',
        'travellers_number',
        'wilaya',
        'room_type',
        'status',
        'include_feeding',
        'total_price',
    ];

    protected $casts = [
        'room_type' => RoomType::class,
        'include_feeding' => 'boolean',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(OfferVariant::class, 'variant_id');
    }
}
