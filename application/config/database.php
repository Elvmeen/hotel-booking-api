<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group  = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn'        => 'mysqli:host=' . getenv('DB_HOST') . ';port=3306;dbname=' . getenv('DB_NAME') . ';charset=utf8mb4',
    'hostname'   => getenv('DB_HOST'),
    'username'   => getenv('DB_USER'),
    'password'   => getenv('DB_PASS'),
    'database'   => getenv('DB_NAME'),
    'port'       => 3306,
    'dbdriver'   => 'mysqli',
    'dbprefix'   => '',
    'pconnect'   => FALSE,
    'db_debug'   => (ENVIRONMENT !== 'production'),
    'cache_on'   => FALSE,
    'cachedir'   => '',
    'char_set'   => 'utf8mb4',
    'dbcollat'   => 'utf8mb4_unicode_ci',
    'swap_pre'   => '',
    'encrypt'    => FALSE,
    'compress'   => FALSE,
    'stricton'   => FALSE,
    'failover'   => array(),
    'save_queries' => FALSE,
);
