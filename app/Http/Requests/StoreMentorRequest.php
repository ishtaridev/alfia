<?php

namespace App\Http\Requests;

use App\Models\Mentor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMentorRequest extends FormRequest
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
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:100',
                'regex:/^(0[5-7][0-9]{8})$/',
                Rule::unique(Mentor::class, 'phone'),
            ],
            'commune' => ['required', 'string', 'in:'.implode(',', config('communes.bordj_bou_arreridj'))],
        ];
    }
}
