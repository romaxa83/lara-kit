<?php

namespace App\GraphQL\Mutations\Common\Avatars;

use App\GraphQL\Types\Enums\Avatars\AvatarModelsTypeEnum;
use App\GraphQL\Types\NonNullType;
use App\Modules\Utils\Media\Contracts\HasAvatar;
use Closure;
use Core\Exceptions\TranslatedException;
use Core\GraphQL\Mutations\BaseMutation;
use Core\Models\BaseModel;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class BaseAvatarMutation extends BaseMutation
{
    public function __construct()
    {
        $this->setMutationGuard();
    }

    abstract protected function setMutationGuard(): void;

    public function authorize(
        mixed $root,
        array $args,
        mixed $ctx,
        ResolveInfo $info = null,
        Closure $fields = null
    ): bool {
        return $this->authCheck();
    }

    protected function avatarArgs(): array
    {
        return [
            'model_id' => [
                'type' => NonNullType::id(),
            ],
            'model_type' => [
                'type' => AvatarModelsTypeEnum::nonNullType(),
            ],
        ];
    }

    protected function resolveModel(string $modelType, int $modelId): BaseModel|HasAvatar
    {
        if (!$model = Relation::getMorphedModel($modelType)) {
            throw new TranslatedException(
                sprintf('"%s" does not support uploading avatars', $modelType)
            );
        }

        /** @var BaseModel|HasAvatar $model */
        $model = $model::query()->findOrFail($modelId);

        if (!$model instanceof HasAvatar) {
            throw new TranslatedException(
                sprintf('"%s" must implements "HasAvatar" interface', $modelType)
            );
        }

        $this->assertCanInteractsWithAvatar($model);

        return $model;
    }

    protected function assertCanInteractsWithAvatar(HasAvatar $model): void
    {
        if (
            ($model->getKey() !== $this->authId())
            || ($model->getMorphClass() !== $this->user()?->getMorphClass())
        ) {
            throw new TranslatedException(
                __('You cannot interact with other users\' avatars')
            );
        }
    }
}

