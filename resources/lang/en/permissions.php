<?php

$list = 'List';
$create = 'Create';
$update = 'Update';
$delete = 'Delete';
$restore = 'Restore';

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
        'grants' => $baseGrants + [
            'restore' => $restore
            ],
    ],
    'role' => [
        'group' => 'Roles',
        'grants' => $baseGrants,
    ],
];
