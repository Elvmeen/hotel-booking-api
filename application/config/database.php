<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
|--------------------------------------------------------------------------
| Update these credentials to match your MySQL server configuration.
| For production, consider using environment variables.
*/
$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn'      => '',
    'hostname' => getenv('DB_HOST')     ?: 'localhost',
    'username' => getenv('DB_USER')     ?: 'root',
    'password' => getenv('DB_PASS')     ?: '',
    'database' => getenv('DB_NAME')     ?: 'hotel_booking',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8mb4',
    'dbcollat'  => 'utf8mb4_unicode_ci',
    'swap_pre'  => '',
    'encrypt'   => FALSE,
    'compress'  => FALSE,
    'stricton'  => FALSE,
    'failover'  => array(),
    'save_queries' => TRUE,
);
