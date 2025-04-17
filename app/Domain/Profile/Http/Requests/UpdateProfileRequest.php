<?php

namespace App\Domain\Profile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * @return array<string>
     */
    public function rules(): array
    {
        return [
            'last_name'  => 'sometimes|string|max:255',
            'first_name' => 'sometimes|string|max:255',
            'status'     => 'sometimes|in:inactive,pending,active',
            'image_path' => 'nullable|image|max:2048',
        ];
    }
}
