<?php

namespace Tests\Feature\Mutations\BackOffice\Admins\Avatar;

use App\GraphQL\Mutations\BackOffice\Admins\Avatar\AvatarDeleteMutation;
use App\Modules\Admin\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Builders\Admins\AdminBuilder;
use Tests\TestCase;
use Tests\Traits\Assert\AssertNotification;

class AvatarDeleteMutationTest extends TestCase
{
    use DatabaseTransactions;
    use AssertNotification;
    use AssertNotification;

    public const MUTATION = AvatarDeleteMutation::NAME;

    protected AdminBuilder $adminBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->langInit();

        $this->adminBuilder = resolve(AdminBuilder::class);
    }

    /** @test */
    public function success_delete(): void
    {
        Storage::fake('public');

        /** @var $mode Admin */
        $model = $this->loginAsAdmin();
        $model->addMedia(
            UploadedFile::fake()->image('avatar.png')
        )
        ->toMediaCollection(Admin::MEDIA_COLLECTION_NAME);

        $this->assertNotNull($model->avatar());

        $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr([
                $model->id,
                $model::MORPH_NAME,
            ])
        ])
            ->assertOk()
            ->assertJson([
                'data' => [
                    self::MUTATION => [
                        'success' => true,
                        'message' => __("messages.media.avatar.actions.delete.success"),
                    ]
                ]
            ])
        ;

        $model->refresh();

        $this->assertNull($model->avatar());
    }

    /** @test */
    public function not_auth(): void
    {
        Storage::fake('public');

        /** @var $mode Admin */
        $model = $this->adminBuilder->create();

        $this->assertNull($model->avatar());

        $res = $this->postGraphQLBackOffice([
            'query' => $this->getQueryStr([
                $model->id,
                $model::MORPH_NAME,
            ])
        ])
        ;

        $this->assertUnauthorized($res);
    }

    protected function getQueryStr(array $data): string
    {
        return sprintf(
            '
            mutation {
                %s (
                    model_id: "%s",
                    model_type: %s,
                ) {
                    message
                    success
                }
            }',
            self::MUTATION,
            data_get($data, '0'),
            data_get($data, '1'),
        );
    }
}
