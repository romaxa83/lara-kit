<?php

$list = 'List';
$create = 'Create';
$update = 'Update';
$delete = 'Delete';

$baseGrants = [
    'list' => $list,
    'create' => $create,
    'update' => $update,
    'delete' => $delete,
];

return [
    'admin' => [
        'group' => 'Admins',
        'grants' => $baseGrants,
    ],
    'user' => [
        'group' => 'Users',
        'grants' => $baseGrants,
    ],
    'role' => [
        'group' => 'Roles',
        'grants' => $baseGrants,
    ],
];
