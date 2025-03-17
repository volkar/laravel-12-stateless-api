<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Enums\ThemeEnum;
use App\Http\Payloads\Users\UpdateUserPayload;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    /** @return array<string,mixed[]> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'alpha_dash:ascii', 'min:2', 'max:100', Rule::unique('users')->ignore($this->user()?->id)],
            'theme' => ['required', 'in:' . implode(',', array_map(fn(ThemeEnum $enum) => $enum->value, ThemeEnum::cases()))],
            'user_groups' => ['present', 'array'],
            'user_groups.*' => ['array'],
            'user_groups.*.slug' => ['required', 'alpha_dash:ascii', 'min:2', 'max:100'],
            'user_groups.*.title' => ['required', 'string', 'min:2', 'max:100'],
            'user_groups.*.list' => ['required', 'array', 'max:100'],
            'user_groups.*.list.*' => ['required', 'email', 'max:255'],
        ];
    }

    public function payload(): UpdateUserPayload
    {
        return new UpdateUserPayload(
            name: $this->string('name')->toString(),
            slug: $this->string('slug')->toString(),
            theme: $this->string('theme')->toString(),
            userGroups: $this->array('user_groups'),
        );
    }
}
