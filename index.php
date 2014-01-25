<?php
require_once __DIR__.'/vendor/autoload.php';

/* Twig File loader and Environment */
/*
$loader = new Twig_Loader_Filesystem(__DIR__ . '/views');
$twig = new Twig_Environment($loader, array(
  'cache' => __DIR__ . '/cache',
));
*/

$app = new Silex\Application();

// registers Twig for use as $app['twig']
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
    'twig.options' => array(
      'cache' => __DIR__ . '/cache',
    )
));

// loads config file
$config = parse_ini_file(__DIR__ . "/config/config.ini");

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