<?php
// Set Default Global Timezone (Lima/Peru)
date_default_timezone_set('America/Lima');
// Smart detection for Docker/Production environment variables
function get_env_var($key, $default = '') {
    $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return $val !== false && $val !== null && $val !== '' ? $val : $default;
}

// Configuration constants
define('DB_HOST', get_env_var('DB_HOST', 'localhost'));
define('DB_NAME', get_env_var('DB_NAME', 'qr_aula_control'));
define('DB_USER', get_env_var('DB_USER', 'root'));
define('DB_PASS', get_env_var('DB_PASS', ''));

// Auto-detect current domain for cloud zero-config fallback
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$currentHost = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (strpos($currentHost, 'localhost') !== false) {
    $defaultUrl = 'http://localhost/AsistenciaxQr/public';
} else {
    $defaultUrl = $protocol . $currentHost; // Automatically detect live Render domain
}

define('URLROOT', get_env_var('URLROOT', $defaultUrl));

// App Root
define('APPROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'app');

// Sitename
define('SITENAME', 'QR Aula Control');
