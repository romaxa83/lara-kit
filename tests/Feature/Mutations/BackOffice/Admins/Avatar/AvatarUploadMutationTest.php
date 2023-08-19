<?php

namespace Tests\Feature\Mutations\BackOffice\Admins\Avatar;

use App\GraphQL\Mutations\BackOffice\Admins\AdminChangePasswordMutation;
use App\GraphQL\Mutations\BackOffice\Admins\Avatar\AvatarUploadMutation;
use App\Modules\Admin\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertNotification;

class AvatarUploadMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertNotification;

    public const MUTATION = AvatarUploadMutation::NAME;

    protected AdminBuilder $adminBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->adminBuilder = resolve(AdminBuilder::class);
    }

    /** @test */
    public function success_upload(): void
    {
        Storage::fake('public');

        /** @var $mode Admin */
        $model = $this->loginAsAdmin();

        $file = UploadedFile::fake()->image('avatar.png');

        $attributes = [
            'operations' => sprintf(
                '{"query": "mutation ($media: Upload!) {%s (model_id: \"%s\", model_type: %s, media: $media) {id}}"}',
                self::MUTATION,
                $model->id,
                $model::MORPH_NAME
            ),
            'map' => '{ "media": ["variables.media"] }',
            'media' => $file,
        ];

        $this->assertFalse($model->hasAvatar());
        $this->assertNull($model->avatar());

        $this->postGraphQlBackOfficeUpload($attributes)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    self::MUTATION => [
                        'id',
                    ]
                ]
            ])
        ;

        $model->refresh();

        $this->assertTrue($model->hasAvatar());
        $this->assertNotNull($model->avatar());
    }

// todo добавить валидацию
    public function fail_not_found_model(): void
    {
        Storage::fake('public');

        /** @var $mode Admin */
        $model = $this->loginAsAdmin();

        $file = UploadedFile::fake()->image('avatar.png');

        $attributes = [
            'operations' => sprintf(
                '{"query": "mutation ($media: Upload!) {%s (model_id: \"%s\", model_type: %s, media: $media) {id}}"}',
                self::MUTATION,
                $model->id + 1,
                $model::MORPH_NAME
            ),
            'map' => '{ "media": ["variables.media"] }',
            'media' => $file,
        ];

        $this->postGraphQlBackOfficeUpload($attributes)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    self::MUTATION => [
                        'id',
                    ]
                ]
            ])
        ;

        $model->refresh();

        $this->assertTrue($model->hasAvatar());
        $this->assertNotNull($model->avatar());
    }

    /** @test */
    public function not_auth(): void
    {
        Storage::fake('public');

        /** @var $mode Admin */
        $model = $this->adminBuilder->create();

        $file = UploadedFile::fake()->image('avatar.png');

        $attributes = [
            'operations' => sprintf(
                '{"query": "mutation ($media: Upload!) {%s (model_id: \"%s\", model_type: %s, media: $media) {id}}"}',
                self::MUTATION,
                $model->id,
                $model::MORPH_NAME
            ),
            'map' => '{ "media": ["variables.media"] }',
            'media' => $file,
        ];

        $res = $this->postGraphQlBackOfficeUpload($attributes);

        $this->assertUnauthorized($res);
    }
}

