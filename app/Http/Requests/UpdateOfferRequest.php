<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'variants' => ['sometimes', 'array', 'min:1'],
            'variants.*.id' => ['nullable', 'integer', 'exists:offer_variants,id'],
            'variants.*.travel_date' => ['required', 'date', 'after_or_equal:today'],
            'variants.*.airport' => ['required', 'string', 'max:100'],
            'variants.*.pricing.collectif_room' => ['required', 'integer', 'min:0'],
            'variants.*.pricing.room_of_four' => ['required', 'integer', 'min:0'],
            'variants.*.pricing.room_of_three' => ['required', 'integer', 'min:0'],
            'variants.*.pricing.room_of_two' => ['required', 'integer', 'min:0'],
            'variants.*.pricing.feeding' => ['required', 'integer', 'min:0'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'deleted_image_ids' => ['nullable', 'array'],
            'deleted_image_ids.*' => ['integer', 'exists:offer_images,id'],
        ];
    }
}
