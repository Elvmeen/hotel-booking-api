<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS -- Supabase PostgreSQL
| -------------------------------------------------------------------
|
| Your Supabase connection details are found at:
| Supabase Dashboard > Project Settings > Database > Connection string
|
| Use the "PHP PDO" or "URI" tab and extract the values below.
|
| IMPORTANT: Do not commit this file to Git.
| Add application/config/database.php to your .gitignore
| and keep database.php.example as the safe template.
| -------------------------------------------------------------------
*/

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(

    // -------------------------------------------------------
    // Fill these 5 values from your Supabase dashboard:
    // Project Settings > Database > Connection parameters
    // -------------------------------------------------------

    'hostname' => 'db.gjvrebzljhwaheydcbol.supabase.co',   // Your project host
    'username' => 'postgres',                                 // Always 'postgres' on Supabase
    'password' => 'Alameenu@1234567',            // Set during project creation
    'database' => 'postgres',                                 // Always 'postgres' on Supabase
    'port'     => '5432',                                     // Direct connection port

    // -------------------------------------------------------
    // Supabase uses PostgreSQL -- use the 'postgre' driver
    // DO NOT use 'mysqli' -- that is MySQL only
    // -------------------------------------------------------
    'dbdriver' => 'postgre',

    // -------------------------------------------------------
    // Leave the rest as-is
    // -------------------------------------------------------
    'dsn'        => '',
    'dbprefix'   => '',
    'pconnect'   => FALSE,
    'db_debug'   => (ENVIRONMENT !== 'production'),
    'cache_on'   => FALSE,
    'cachedir'   => '',
    'char_set'   => 'utf8',
    'dbcollat'   => 'utf8_general_ci',
    'swap_pre'   => '',
    'encrypt'    => FALSE,
    'compress'   => FALSE,
    'stricton'   => FALSE,
    'failover'   => array(),
    'save_queries' => TRUE,
);

/*
| -------------------------------------------------------------------
| CONNECTION POOLING (Optional but recommended for Supabase free tier)
| -------------------------------------------------------------------
|
| Supabase free tier has a limit of 60 direct connections.
| If you start hitting connection errors, switch to the
| Supabase connection pooler (Transaction mode) instead:
|
|   'hostname' => 'aws-0-eu-west-1.pooler.supabase.com',
|   'port'     => '6543',
|   'username' => 'postgres.gjvrebzljhwaheydcbol',  (note the project ref prefix)
|
| Find the exact pooler string at:
| Supabase Dashboard > Project Settings > Database > Connection pooling
| -------------------------------------------------------------------
*/
