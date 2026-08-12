<?php

namespace App\Models;

use Database\Factories\OfferFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string $code
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<OfferVariant> $variants
 * @property-read Collection<OfferImage> $images
 */
class Offer extends Model
{
    /** @use HasFactory<OfferFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Offer $offer) {
            if (empty($offer->code)) {
                $offer->code = self::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'ALFIA-'.strtoupper(Str::random(6));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function variants(): HasMany
    {
        return $this->hasMany(OfferVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(OfferImage::class)->orderBy('order');
    }

    public function getFirstImage(): ?OfferImage
    {
        return $this->images()->first();
    }
}
