<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;

class StoreWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'subdomain' => ['required', 'string', 'max:60', 'alpha_dash', 'unique:websites,subdomain'],
            'category' => ['required', 'string', 'in:business,portfolio,agency,ecommerce,blog,corporate,landing,personal,education,general'],
            'description' => ['nullable', 'string', 'max:500'],
            'template_slug' => ['nullable', 'string', 'exists:templates,slug'],
        ];
    }
}
