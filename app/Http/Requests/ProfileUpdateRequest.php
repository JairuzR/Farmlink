<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $user = $this->user();

        $rules = [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'string', 'lowercase', 'email', 'max:255',
                          Rule::unique(User::class)->ignore($user->id)],
            'phone'   => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ];

        if ($user->isFarmer()) {
            $rules = array_merge($rules, [
                'farm_name'       => ['nullable', 'string', 'max:120'],
                'bio'             => ['nullable', 'string', 'max:1000'],
                'latitude'        => ['nullable', 'numeric', 'between:-90,90'],
                'longitude'       => ['nullable', 'numeric', 'between:-180,180'],
                'social_platform' => ['nullable', 'array'],
                'social_platform.*' => ['nullable', 'string', 'max:50'],
                'social_label'    => ['nullable', 'array'],
                'social_label.*'  => ['nullable', 'string', 'max:100'],
                'social_url'      => ['nullable', 'array'],
                'social_url.*'    => ['nullable', 'url', 'max:500'],
            ]);
        }

        return $rules;
    }
}