<?php

namespace App\Models;

use Database\Factories\OfferVariantFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $offer_id
 * @property string $travel_date
 * @property string $airport
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Offer $offer
 * @property-read OfferPricing|null $pricing
 */
class OfferVariant extends Model
{
    /** @use HasFactory<OfferVariantFactory> */
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'travel_date',
        'airport',
    ];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
        ];
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function pricing(): HasOne
    {
        return $this->hasOne(OfferPricing::class, 'offer_variant_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'variant_id');
    }

    public function scopeFuture(Builder $query): Builder
    {
        return $query->where('travel_date', '>=', now()->toDateString());
    }
}
