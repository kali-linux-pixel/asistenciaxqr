<?php
// Configuration constants (Reads from system environment or defaults to local)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'qr_aula_control');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

// URL Root (Dynamic detection fallback)
$defaultUrl = 'http://localhost/AsistenciaxQr/public';
define('URLROOT', getenv('URLROOT') ?: $defaultUrl);

// App Root
define('APPROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'app');

// Sitename
define('SITENAME', 'QR Aula Control');
