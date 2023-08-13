<?php

namespace App\Services\Users;

use App\Dto\Users\UserDto;
use App\Events\Users\UserRegisteredEvent;
use App\Modules\User\Models\User;
use App\Services\Companies\CompanyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class UserService
{
    public function __construct() {
    }

    public function register(UserDto $dto): User
    {
        $company = $this->companyService->create($dto->getLang());

        $this->companyService->setOwner(
            $user = $this->create($dto),
            $company
        );

        event(new UserRegisteredEvent($user));

        return $user;
    }

    public function create(UserDto $dto): User
    {
        $user = new User();

        $this->fill($dto, $user);
        $user->setPassword($dto->getPassword());
        $user->save();

        return $user;
    }

    private function fill(UserDto $dto, User $user): void
    {
        $user->first_name = $dto->getFirstName();
        $user->last_name = $dto->getLastName();
        $user->middle_name = $dto->getMiddleName();
        $user->email = $dto->getEmail();
        $user->setLanguage($dto->getLang());
    }

    public function update(User $user, UserDto $dto): User
    {
        $this->fill($dto, $user);

        if ($dto->hasPassword()) {
            $user->setPassword($dto->getPassword());
        }

        if ($user->isDirty()) {
            $user->save();
        }

        return $user;
    }

    public function changePassword(User $user, string $password): bool
    {
        return $user
            ->setPassword($password)
            ->save();
    }

    public function createNewPassword(): string
    {
        $digitsCount = 2;

        $source = Str::lower(Str::random(User::MIN_LENGTH_PASSWORD - $digitsCount));

        $digits = substr(str_shuffle('1234567890'), 0, $digitsCount);

        return str_shuffle($source . $digits);
    }

    public function delete(Collection $users): bool
    {
        $users->each(fn (User $user) => $user->delete());

        return true;
    }
}
