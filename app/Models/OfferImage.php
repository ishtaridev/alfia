<?php

namespace App\Models;

use Database\Factories\OfferImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $offer_id
 * @property string $path
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Offer $offer
 * @property-read string $url
 */
class OfferImage extends Model
{
    /** @use HasFactory<OfferImageFactory> */
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'path',
        'order',
    ];

    protected $appends = ['url'];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('s3')->url($this->path);
    }
}
