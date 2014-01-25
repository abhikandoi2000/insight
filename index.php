<?php
require_once __DIR__.'/vendor/autoload.php';


// loads config file
$config = parse_ini_file(__DIR__ . "/config/config.ini");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['debug'] = true;

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


$app->post('/members/update', function(Request $request) use($app) {
  $sql = "INSERT INTO `members`(`firstname`, `lastname`, `mail`, `github_id`, `year`, `group`) VALUES (:firstname, :lastname, :mail, :github_id, :year, :group)";
  $result = $app['db']->executeUpdate($sql, array(
    ':firstname' => $request->get('firstname'),
    ':lastname' => $request->get('lastname'),
    ':mail' => $request->get('mail'),
    ':github_id' => $request->get('github_id'),
    ':year' => $request->get('year'),
    ':group' => $request->get('group'),
    )
  );
  return $result . " rows affected.";
  // return $request->get('name');
});

$app->post('/commits/update', function() use($app) {
  return 'Hello '.$app->escape($name);
});

$app->run();
?>