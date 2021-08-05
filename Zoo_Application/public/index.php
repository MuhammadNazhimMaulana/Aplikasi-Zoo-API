<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($app);

// Register middleware
$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);

// Register routes pakan
$routes_pakan = require __DIR__ . '/../src/routes/routes_pakan.php';
$routes_pakan($app);

// Register routes jenis hewan
$routes_jenis = require __DIR__ . '/../src/routes/routes_jenis_hewan.php';
$routes_jenis($app);

// Register routes pekerja
$routes_pegawai = require __DIR__ . '/../src/routes/routes_pegawai.php';
$routes_pegawai($app);

// Register routes kandang
$routes_kandang = require __DIR__ . '/../src/routes/routes_kandang.php';
$routes_kandang($app);

// Register routes hewan
$routes_hewan = require __DIR__ . '/../src/routes/routes_hewan.php';
$routes_hewan($app);

// Register routes perawatan
$routes_perawatan = require __DIR__ . '/../src/routes/routes_perawatan.php';
$routes_perawatan($app);

// Register routes auth
$routes_auth = require __DIR__ . '/../src/routes/routes_auth.php';
$routes_auth($app);

// Run app
$app->run();
