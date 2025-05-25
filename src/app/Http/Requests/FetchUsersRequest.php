<?php

namespace App\Http\Requests;

use App\DTO\FetchUsers;
use Illuminate\Foundation\Http\FormRequest;

class FetchUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    public function getDto(): FetchUsers
    {
        return new FetchUsers(
            page: (int)$this->input('page', 1),
            perPage: (int)$this->input('per_page', 10)
        );
    }
}
