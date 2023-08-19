<?php

namespace App\GraphQL\Mutations\Common\Avatars;

use App\GraphQL\Types\Media\MediaType;
use App\Modules\Admin\Models\Admin;
use App\Modules\Localization\Models\Language;
use App\Modules\Permissions\Models\Role;
use App\Modules\Utils\Phones\Rules\PhoneRule;
use App\Modules\Utils\Phones\Rules\PhoneUniqueRule;
use Core\GraphQL\Types\FileType;
use Core\Rules\NameRule;
use Core\Rules\PasswordRule;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\SelectFields;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

abstract class BaseAvatarUploadMutation extends BaseAvatarMutation
{
    public const NAME = 'avatarUpload';

    public function args(): array
    {
        return array_merge(
            $this->avatarArgs(),
            [
                'media' => [
                    'type' => FileType::nonNullType(),
                ],
            ]
        );
    }

    public function type(): Type
    {
        return MediaType::type();
    }

    public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): Media
    {
        $model = $this->resolveModel($args['model_type'], $args['model_id']);

        $model->uploadAvatar($args['media']);

        return $model->avatar();
    }
}
