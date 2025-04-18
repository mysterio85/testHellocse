<?php

namespace App\Domain\Profile\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'last_name'  => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'image_path' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'     => 'required|in:inactive,pending,active',
        ];
    }
}
