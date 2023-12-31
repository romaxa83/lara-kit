<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Языковые ресурсы напоминания пароля
    |--------------------------------------------------------------------------
    |
    | Последующие языковые строки возвращаются брокером паролей на неудачные
    | попытки обновления пароля в таких случаях, как ошибочный код сброса
    | пароля или неверный новый пароль.
    |
    */

    'reset'     => 'Ваш пароль был сброшен!',
    'sent'      => 'Ссылка на сброс пароля была отправлена!',
    'throttled' => 'Пожалуйста, подождите перед повторной попыткой.',
    'token'     => 'Ошибочный код сброса пароля.',
    'user'      => 'Не удалось найти пользователя с указанным электронным адресом.',

    'email' => [
        'greeting' => 'Добрый день, :name!',
        'registration_subject' => 'Регистрация на '.config('app.name'),
        'change_subject' => 'Изменения учетной записи на '.config('app.name'),
        'registration_success' => 'Вы были успешно зарегистрированы на '.config('app.name'),
        'password_changed' => 'Пароль для Вашей учетной записи был изменен',
        'your_login' => 'Ваш логин: :email',
        'password' => 'Ваш пароль для входа: :password',
        'change_password' => 'Рекомендуем изменить его после авторизации в системе.',
        'login' => 'Войти'
    ]
];
