<?php

namespace App\Http\Requests;

use App\Enums\RoomType;
use App\Models\Offer;
use App\Models\OfferVariant;
use Illuminate\Foundation\Http\FormRequest;

class StorePublicReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Offer $offer */
        $offer = $this->route('offer');

        return [
            'variant_id' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, \Closure $fail) use ($offer): void {
                    $variant = OfferVariant::find($value);
                    if (! $variant || $variant->offer_id !== $offer->id) {
                        $fail('The selected variant is invalid.');
                    }
                },
            ],
            'customer' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:100'],
            'travellers_number' => ['required', 'integer', 'min:1'],
            'wilaya' => ['required', 'string', 'in:'.implode(',', config('wilayas'))],
            'room_type' => ['required', 'string', 'in:'.implode(',', array_column(RoomType::cases(), 'value'))],
            'include_feeding' => ['sometimes', 'boolean'],
        ];
    }
}
