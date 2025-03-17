<?php

declare(strict_types=1);

namespace App\Http\Requests\Albums;

use App\Enums\AccessEnum;
use App\Enums\AlbumItemTypeEnum;
use App\Enums\ThemeEnum;
use App\Http\Payloads\Albums\UpdateAlbumPayload;
use App\Rules\AlbumAtlasMetaRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateAlbumRequest extends FormRequest
{
    /** @return array<string,mixed[]> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => ['required', 'alpha_dash:ascii', 'min:2', 'max:100', Rule::unique('albums')->where(fn(Builder $query) => $query->where('user_id', $this->user()?->id))->ignore($this->album_id)->withoutTrashed()],
            'atlas' => ['required', 'array'],
            'atlas.*' => ['array'],
            'atlas.*.type' => ['required', 'string', 'in:' . implode(',', array_map(fn(AlbumItemTypeEnum $enum) => $enum->value, AlbumItemTypeEnum::cases()))],
            'atlas.*.src' => ['required', 'string', 'min:1', 'max:10000'],
            'atlas.*.meta' => ['array', new AlbumAtlasMetaRule()],
            'atlas.*.meta.access' => ['in:' . implode(',', array_map(fn(AccessEnum $enum) => $enum->value, AccessEnum::cases()))],
            'atlas.*.meta.shared_for' => ['array', 'max:100'],
            'atlas.*.meta.shared_for.*' => ['string', 'min:2', 'max:255'],
            'theme' => ['required', 'in:' . implode(',', array_map(fn(ThemeEnum $enum) => $enum->value, ThemeEnum::cases()))],
            'access' => ['required', 'in:' . implode(',', array_map(fn(AccessEnum $enum) => $enum->value, AccessEnum::cases()))],
            'shared_for' => ['present', 'array', 'max:100'],
            'shared_for.*' => ['string', 'min:1', 'max:255'],
            'date_at' => ['required', 'date'],
        ];
    }

    public function payload(): UpdateAlbumPayload
    {
        /** @var array<int,string> */
        $sharedFor = $this->array('shared_for');
        /** @var array<int, array<string, mixed>> */
        $atlas = $this->array('atlas');

        return new UpdateAlbumPayload(
            name: $this->string('name')->toString(),
            slug: $this->string('slug')->toString(),
            atlas: $atlas,
            theme: $this->string('theme')->toString(),
            access: $this->string('access')->toString(),
            sharedFor: $sharedFor,
            dateAt: $this->date('date_at'),
        );
    }
}
