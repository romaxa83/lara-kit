<?php

namespace App\Dto\Permission;

class RoleDto
{
    /**
     * @var array|RoleTranslateDto[]
     */
    private array $translates;

    private array $permissions;

    private string $name;

    public static function makeByArgs(array $args): self
    {
        $self = new self();

        $self->name = $args['name'];
        $self->translates = $self::generateTranslatesDto($args['translates']);
        $self->permissions = $args['permissions'];

        return $self;
    }

    private static function generateTranslatesDto(array $translates): array
    {
        $translatesDto = [];
        foreach ($translates as $translate) {
            $translatesDto[] = RoleTranslateDto::fromArgs($translate);
        }
        return $translatesDto;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTranslates(): array
    {
        return $this->translates;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}
