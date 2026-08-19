<?php

namespace App\Http\Requests;

use App\Enums\RoomType;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
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
            'customer' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:100'],
            'travellers_number' => ['required', 'integer', 'min:1'],
            'wilaya' => ['required', 'string', 'in:'.implode(',', config('wilayas'))],
            'room_type' => ['required', 'string', 'in:'.implode(',', array_column(RoomType::cases(), 'value'))],
            'status' => ['required', 'string', 'in:pending,confirmed,cancelled'],
            'include_feeding' => ['sometimes', 'boolean'],
        ];
    }
}
