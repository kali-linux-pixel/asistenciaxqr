<?php
// Set Timezone
date_default_timezone_set('America/Lima');

// Load Config (Absolute Path)
require_once __DIR__ . '/../config/config.php';

// Helpers
require_once __DIR__ . '/helpers/session_helper.php';

// Autoload Core Libraries
spl_autoload_register(function($className){
    $file = __DIR__ . '/core/' . $className . '.php';
    if(file_exists($file)){
        require_once $file;
    }
});
