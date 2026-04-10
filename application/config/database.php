<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group  = 'default';
$query_builder = TRUE;

// Force SSL at the PHP level before CI3 initializes the connection
mysqli_report(MYSQLI_REPORT_OFF);
$ssl = mysqli_init();
$ssl->ssl_set(
    NULL,
    NULL,
    '/etc/ssl/certs/ca-certificates.crt',
    NULL,
    NULL
);

$db['default'] = array(
    'hostname'     => getenv('DB_HOST'),
    'username'     => getenv('DB_USER'),
    'password'     => getenv('DB_PASS'),
    'database'     => getenv('DB_NAME'),
    'port'         => 3306,
    'dbdriver'     => 'mysqli',
    'dbprefix'     => '',
    'pconnect'     => FALSE,
    'db_debug'     => (ENVIRONMENT !== 'production'),
    'cache_on'     => FALSE,
    'cachedir'     => '',
    'char_set'     => 'utf8mb4',
    'dbcollat'     => 'utf8mb4_unicode_ci',
    'swap_pre'     => '',
    'encrypt'      => array(
        'ssl_verify' => TRUE,
        'ssl_ca'     => '/etc/ssl/certs/ca-certificates.crt',
    ),
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => array(),
    'save_queries' => FALSE,
);
