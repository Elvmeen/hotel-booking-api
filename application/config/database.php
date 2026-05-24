<?php
  defined('BASEPATH') OR exit('No direct script access allowed');

  /*
  |--------------------------------------------------------------------------
  | DATABASE — Supabase (PostgreSQL) via IPv4 Session Pooler
  |--------------------------------------------------------------------------
  | Direct connection (db.*.supabase.co) is IPv6-only — unreachable on Render/Replit.
  | This uses the IPv4-compatible Session Pooler instead.
  |
  | Required env vars / secrets:
  |   DB_PASS  → your Supabase database password
  |   DB_USER  → postgres  (optional, defaults to postgres)
  */
  $active_group  = 'default';
  $query_builder = TRUE;

  $base_user   = explode('.', (getenv('DB_USER') ?: 'postgres'))[0];
  $db_user     = $base_user . '.gjvrebzljhwaheydcbol';

  $db['default'] = array(
      'dsn'      => '',
      'hostname' => 'aws-0-eu-west-1.pooler.supabase.com',
      'port'     => '5432',
      'username' => $db_user,
      'password' => getenv('DB_PASS') ?: '',
      'database' => 'postgres',
      'dbdriver' => 'postgre',
      'dbprefix'    => '',
      'pconnect'    => FALSE,
      'db_debug'    => (ENVIRONMENT !== 'production'),
      'cache_on'    => FALSE,
      'cachedir'    => '',
      'char_set'    => 'utf8',
      'dbcollat'    => '',
      'swap_pre'    => '',
      'encrypt'     => FALSE,
      'compress'    => FALSE,
      'stricton'    => FALSE,
      'failover'    => array(),
      'save_queries' => FALSE,
  );
  