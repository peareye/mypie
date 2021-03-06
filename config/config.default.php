<?php
/**
 * Default Configuration Settings
 *
 * Define all instance specific settings in config.local.php.
 */

/**
 * Production boolean variable controls debug and environment modes
 */
$config['production'] = true;

/**
 * Default Domain
 * Note: Do not include a trailing slash
 */
$config['baseUrl'] = '';

/**
 * Administration Email Address
 */
$config['user']['adminEmail'] = [];

/**
 * Basics
 */
$config['site']['title'] = '';
$config['site']['sendFromEmail'] = '';
$config['site']['sendToEmail'] = [];

/**
 * Database Settings
 */
$config['database']['host'] = 'localhost';
$config['database']['dbname'] = '';
$config['database']['username'] = '';
$config['database']['password'] = '';

/**
 * Sessions
 */
$config['session']['cookieName'] = ''; // Name of the cookie
$config['session']['checkIpAddress'] = true;
$config['session']['checkUserAgent'] = true;
$config['session']['salt'] = ''; // Salt key to hash
$config['session']['secondsUntilExpiration'] = 7200;

/**
 * Pagination Options
 */
$config['pagination']['admin']['rowsPerPage'] = 10;
$config['pagination']['admin']['numberOfLinks'] = 2;
$config['pagination']['menu']['rowsPerPage'] = 5;
$config['pagination']['menu']['numberOfLinks'] = 2;
