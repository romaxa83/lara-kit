<?php

declare(strict_types=1);

namespace Core\Traits\GraphQL;

use Core\Exceptions\TranslatedException;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;
use Throwable;

trait ThrowableResolverTrait
{
    /**
     * @throws Throwable
     */
    public function resolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): mixed {
        try {
            return app()->call([$this, 'doResolve'], [
                'root' => $root,
                'args' => $this->initArgs($args),
                'context' => $context,
                'info' => $info,
                'fields' => $fields
            ]);
        } catch (TranslatedException $e) {
            throw $e;
        } catch (Throwable $e) {
            if (is_prod()) {
                logger($e);
                throw new TranslatedException(__('exceptions.default'));
            }

            throw $e;
        }
    }

    protected function initArgs(array $args): array
    {
        return $args;
    }

    abstract public function doResolve(
        mixed $root,
        array $args,
        mixed $context,
        ResolveInfo $info,
        SelectFields $fields
    ): mixed;
}
