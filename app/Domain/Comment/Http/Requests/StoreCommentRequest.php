<?php

namespace App\Domain\Comment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'text'       => 'required|string',
            'profile_id' => 'required|integer|exists:profiles,id',
        ];
    }
}
