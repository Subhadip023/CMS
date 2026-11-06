<?php

namespace App\Http\Requests\Website;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWebsiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $websiteId = $this->route('website')?->id ?? $this->route('website');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'subdomain' => [
                'sometimes',
                'required',
                'string',
                'max:60',
                'alpha_dash',
                Rule::unique('websites', 'subdomain')->ignore($websiteId),
            ],
            'custom_domain' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('websites', 'custom_domain')->ignore($websiteId),
            ],
            'category' => ['sometimes', 'string'],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', 'string', 'in:active,maintenance,suspended'],
            'logo_url' => ['nullable', 'string', 'url'],
            'favicon_url' => ['nullable', 'string', 'url'],
            'settings' => ['nullable', 'array'],
        ];
    }
}
