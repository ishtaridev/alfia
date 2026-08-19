<?php

namespace App\Http\Requests;

use App\Enums\RoomType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'string', 'max:100'],
            'travellers_number' => ['sometimes', 'integer', 'min:1'],
            'wilaya' => ['sometimes', 'string', 'in:'.implode(',', config('wilayas'))],
            'room_type' => ['sometimes', 'string', 'in:'.implode(',', array_column(RoomType::cases(), 'value'))],
            'status' => ['sometimes', 'string', 'in:pending,confirmed,cancelled'],
            'include_feeding' => ['sometimes', 'boolean'],
        ];
    }
}
