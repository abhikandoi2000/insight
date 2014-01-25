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
});

$app->post('/commits', function(Request $request) use($app) {
  $sql = "INSERT INTO `commits`(`hash`, `message`, `additions`, `deletions`, `files_affected`, `timestamp`, `member_id`, `project_id`) VALUES (:hash, :message, :additions, :deletions, :files_affected, :timestamp, :member_id, :project_id)";
  $result = $app['db']->executeUpdate($sql, array(
    ':hash' => $request->get('hash'),
    ':message' => $request->get('message'),
    ':additions' => $request->get('additions'),
    ':deletions' => $request->get('deletions'),
    ':files_affected' => $request->get('files_affected'),
    ':timestamp' => $request->get('timestamp'),
    ':member_id' => $request->get('member_id'),
    ':project_id' => $request->get('project_id'),
    )
  );
  return 'Author: ' . $request->get('author');
});

$app->delete('/commits', function() use($app) {
  $sql = "TRUNCATE `commits`";
  $result = $app['db']->executeUpdate($sql);
  return "{$result} rows affected.";
});

$app->delete('/commits/{project_id}', function($project_id) use($app) {
  $sql = "DELETE FROM `commits` WHERE project_id = ?";
  $result = $app['db']->executeUpdate($sql, array($project_id));
  return "{$result} rows affected.";
});

$app->run();
?>