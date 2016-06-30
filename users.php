<?php

require __DIR__ . '/ckan/vendor/autoload.php';
$config = require __DIR__ . '/config.php';
$s = Guzzle\Service\Builder\ServiceBuilder::factory($config['sites'])->get('tainan');
$groups = $s->GetGroups();
$users = $s->GetUsers();
$details = array();
foreach ($users['result'] AS $user) {
    $details[$user['id']] = $user;
}

$fh = fopen(__DIR__ . '/tmp/users.csv', 'w');
fputcsv($fh, array('單位', '角色', '名稱', '信箱', '登入帳號'));

foreach ($groups['result'] AS $group) {
    $group = $s->GetGroup(array('id' => $group, 'include_datasets' => 'false'));
    foreach ($group['result']['users'] AS $user) {
        if(isset($details[$user['id']])) {
            fputcsv($fh, array($group['result']['title'], $user['capacity'], $user['fullname'], $details[$user['id']]['email'], $details[$user['id']]['name']));
        }
    }
}