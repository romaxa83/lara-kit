<?php

namespace Tests\Traits\Assert;

use Illuminate\Support\Facades\Lang;
use Illuminate\Testing\TestResponse;

trait AssertErrors
{
    protected function assertResponseHasValidationMessage(
        TestResponse $result,
        string $attribute,
        array $messages
    ): void {
        $validationMessages = $result->json('errors.0.extensions.validation')[$attribute];

        self::assertTrue(count($validationMessages) > 0);
        self::assertTrue(count($messages) > 0);

        foreach ($messages as $message) {
            $validationMessage = array_shift($validationMessages);
            self::assertEquals($message, $validationMessage);
        }
    }

    protected function assertResponseHasNoValidationErrors(TestResponse $result): void
    {
        self::assertNull($result->json("errors.0.extensions.validation"));
    }

    protected function assertErrorMessage(TestResponse $result, string $msg): void
    {
        self::assertEquals($msg, $result->json('errors.0.message'));
    }

    protected function assertErrorMessageFirst(TestResponse $result, string $msg): void
    {
        self::assertEquals($msg, $result->json('errors.0.messages.0'));
    }

    protected function assertTranslatedMessage(TestResponse $result, string $msg): void
    {
        self::assertEquals($msg, $result->json('errors.0.message'));
        self::assertEquals('translated', $result->json('errors.0.extensions.category'));
    }
    protected function assertGraphQlUnauthorized(TestResponse $result): void
    {
        $errors = $result->json('errors');

        self::assertEquals('authorization', array_shift($errors)['extensions']['category']);
    }

    protected function assertGraphQl404(TestResponse $result): void
    {
        $this->assertGraphQlDebugMessage($result, 'No query results for model');
    }

    protected function assertGraphQlDebugMessage(TestResponse $result, string $message): void
    {
        $errors = $result->json('errors');

        $debugMessage = array_shift($errors)['debugMessage'];

        self::assertStringContainsString($message, $debugMessage);
    }

    protected function assertServerError(TestResponse $result, string $message = 'Internal server error'): void
    {
        self::assertEquals($message, $result->json('errors.0.message'));
    }

    protected function validationError(string $rule, string $attribute, array $args = [], string $type = '', string $locale = 'uk'): string
    {
        $rule = $type
            ? 'validation.' . $rule . '.' . $type
            : 'validation.' . $rule;

        $attribute = Lang::has('validation.attributes.' . $attribute)
            ? trans('validation.attributes.' . $attribute, [], $locale)
            : str_replace(['_', '-'], ' ', $attribute);

        $args = array_merge(
            $args,
            [ 'attribute' => $attribute ]
        );

        return trans($rule, $args, $locale);
    }
}
