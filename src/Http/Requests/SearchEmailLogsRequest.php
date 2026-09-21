<?php

namespace jeremykenedy\LaravelEmailDatabaseLog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchEmailLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['q' => ['nullable', 'string', 'max:200']];
    }
}
