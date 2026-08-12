<?php

namespace App\Models;

use Database\Factories\OfferPricingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $offer_variant_id
 * @property int $collectif_room
 * @property int $room_of_four
 * @property int $room_of_three
 * @property int $room_of_two
 * @property int $feeding
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read OfferVariant $variant
 */
class OfferPricing extends Model
{
    /** @use HasFactory<OfferPricingFactory> */
    use HasFactory;

    protected $fillable = [
        'offer_variant_id',
        'collectif_room',
        'room_of_four',
        'room_of_three',
        'room_of_two',
        'feeding',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(OfferVariant::class, 'offer_variant_id');
    }
}
