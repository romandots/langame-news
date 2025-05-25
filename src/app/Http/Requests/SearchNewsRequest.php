<?php

namespace App\Http\Requests;

use App\DTO\SearchNews;
use Illuminate\Foundation\Http\FormRequest;

class SearchNewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'string|nullable',
            'page' => 'int|min:1',
        ];
    }

    public function getDto(): SearchNews
    {
        return new SearchNews(
            search: $this->input('search'),
            page: max(1, (int)$this->input('page', 1)),
        );
    }
}
