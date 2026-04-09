<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS — PlanetScale
|--------------------------------------------------------------------------
| Credentials are loaded from environment variables.
| Set DB_HOST, DB_USER, DB_PASS, DB_NAME in your Render dashboard
| (or any other hosting environment).
|
| PlanetScale requires SSL — keep 'encrypt' => TRUE.
*/
$active_group  = 'default';
$query_builder = TRUE;

$db['default'] = array(
    'dsn'      => '',
    'hostname' => getenv('DB_HOST') ?: 'aws.connect.psdb.cloud',
    'username' => getenv('DB_USER') ?: '',
    'password' => getenv('DB_PASS') ?: '',
    'database' => getenv('DB_NAME') ?: 'hotel_booking',
    'dbdriver' => 'mysqli',
    'dbprefix' => '',
    'pconnect' => FALSE,
    'db_debug' => (ENVIRONMENT !== 'production'),
    'cache_on'  => FALSE,
    'cachedir'  => '',
    'char_set'  => 'utf8mb4',
    'dbcollat'  => 'utf8mb4_unicode_ci',
    'swap_pre'  => '',
    'encrypt'   => TRUE,   // Required for PlanetScale (forces TLS)
    'compress'  => FALSE,
    'stricton'  => FALSE,
    'failover'  => array(),
    'save_queries' => FALSE,
);
