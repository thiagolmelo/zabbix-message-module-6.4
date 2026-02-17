<?php

/**
 * @var array $data
 * Outputs JSON config for the banner JS.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

echo json_encode([
    'enabled'     => $data['enabled'],
    'message'     => $data['message'],
    'type'        => $data['type'],
    'dismissible' => $data['dismissible'],
    'show_to'     => $data['show_to'],
    'user_type'   => $data['user_type']
]);
