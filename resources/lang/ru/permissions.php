<?php

$list = 'Список';
$create = 'Создание';
$update = 'Изменение';
$delete = 'Удаление';

$baseGrants = [
    'list' => $list,
    'create' => $create,
    'update' => $update,
    'delete' => $delete,
];

return [
    'admin' => [
        'group' => 'Администраторы',
        'grants' => $baseGrants,
    ],

    'admin-actions' => [
        'grants' => [
            'login-as-user' => 'Войти как пользователь'
        ],
    ],

    'user' => [
        'group' => 'Пользователи',
        'grants' => $baseGrants,
    ],

    'employee' => [
        'group' => 'Сотрудники',
        'grants' => $baseGrants,
    ],

    'employee-admin' => [
        'group' => 'Сотрудники',
        'grants' => $baseGrants,
    ],

    'translate' => [
        'group' => 'Переводы',
        'grants' => [
            'list' => $list,
            'update' => $update,
            'delete' => $delete,
        ],
    ],

    'role' => [
        'group' => 'Роли',
        'grants' => $baseGrants,
    ],

    'company' => [
        'group' => 'Компании',
        'grants' => [
            'list' => $list,
            'update' => $update,
        ],
    ],

    'ip-access' => [
        'group' => 'Разрешенные IP',
        'grants' => [
            'list' => $list,
            'create' => $create,
            'update' => $update,
            'delete' => $delete,
        ],
    ],
];
