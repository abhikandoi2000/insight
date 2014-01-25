<?php
require_once __DIR__.'/vendor/autoload.php';


// loads config file
$config = parse_ini_file(__DIR__ . "/config/config.ini");


$app = new Silex\Application();

// registers Twig for use as $app['twig']
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
    'twig.options' => array(
      'cache' => __DIR__ . '/cache',
      'debug' => true,
    )
));

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => $config['mysql_host'],
        'user' => $config['mysql_user'],
        'password' => $config['mysql_password'],
        'dbname' => $config['mysql_database'],
    ),
));


/**
 * Landing Page
 */
$app->get('/', function() use($app) {
  return $app['twig']->render('home.html', array('name' => 'Insight'));
});

$app->get('/users/update', function() use($app) {
  return 'Hello '.$app->escape($name);
});

$app->get('/commits/update', function() use($app) {
  return 'Hello '.$app->escape($name);
});

$app->run();
?>