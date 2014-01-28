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
  $now = time();
  $past = $now - (4 * 7 * 24 * 60 * 60);

  // top authors by commits
  $top_authors_sql = "SELECT count(*) as commit_count, `author` FROM `commits` WHERE `timestamp` BETWEEN :past AND :now GROUP BY `author` ORDER BY commit_count DESC LIMIT 5";
  $top_authors = array();
  $commit_data = $app['db']->fetchAll($top_authors_sql, array(':past' => $past, ':now' => $now));

  foreach( $commit_data as $key => $author ) {
    $member_sql = "SELECT `firstname`, `lastname` FROM members WHERE mail = ?";
    $member = $app['db']->fetchAssoc($member_sql, array($author['author']));
    array_push($top_authors, array('name' => $member['firstname'] . " " .  $member['lastname'], 'commits' => $author['commit_count']));
  }

  // top projects by commits
  $top_projects_sql = "SELECT count(*) as commit_count, `identifier` FROM `commits` WHERE `timestamp` BETWEEN :past AND :now GROUP BY `identifier` ORDER BY commit_count DESC LIMIT 5";
  $top_projects = array();
  $project_data = $app['db']->fetchAll($top_projects_sql, array(':past' => $past, ':now' => $now));

  foreach( $project_data as $key => $project ) {
    array_push($top_projects, array('identifier' => $project['identifier'], 'commits' => $project['commit_count']));
  }

  return $app['twig']->render('home.html', array('top_authors' => $top_authors, 'top_projects' => $top_projects));
});

$app->get('/projects', function() use($app) {
  $sql = "SELECT count(*) as commit_count, `identifier` FROM `commits` GROUP BY `identifier`";
  $project_data = $app['db']->fetchAll($sql);
  $data = array();

  foreach( $project_data as $key => $project ) {
    array_push($data, array('identifier' => $project['identifier'], 'commits' => $project['commit_count']));
  }

  return $app['twig']->render('projects.html', array('projects_data' => $data));
});

$app->get('/members', function() use($app) {
  $sql = "SELECT count(*) as commit_count, `author` FROM `commits` GROUP BY `author` ORDER BY `commit_count` DESC";
  $authors_data = $app['db']->fetchAll($sql);
  $data = array();

  foreach( $authors_data as $key => $author ) {
    $query = "SELECT `firstname`, `lastname` FROM members WHERE mail = ?";
    $member = $app['db']->fetchAssoc( $query, array($author['author']) );
    array_push($data, array('name' => $member['firstname'] . " " . $member['lastname'], 'commits' => $author['commit_count']));
  }

  return $app['twig']->render('members.html', array('members_data' => $data));
});

$app->post('/projects/reload', function() use($app, $config) {
  // TODO(abhikandoi2000@gmail.com): change code for all repositories
  $url = $config['base_url'] . "projects.json?limit=100&key=" . $config['api_key'];
  $response = Requests::get($url, array( "Accept" => "application/json" ),
    array(
      "key" => $config['api_key'],
      "limit" => 100
    )
  );

  $response = json_decode($response->body);

  foreach ($response->projects as $project) {
    $sql = "INSERT INTO `projects`(`name`, `description`, `homepage`, `identifier`) VALUES (:name, :description, :homepage, :identifier)";
    $result = $app['db']->executeUpdate($sql, array(
        ':name' => $project->name,
        ':description' => $project->description,
        ':homepage' => $project->homepage | "",
        ':identifier' => $project->identifier
      )
    );
  }

  return 'Check db for projects';
});

$app->post('/commits', function(Request $request) use($app) {
  $sql = "INSERT INTO `commits`(`hash`, `message`, `additions`, `deletions`, `files_affected`, `timestamp`, `author`, `identifier`) VALUES (:hash, :message, :additions, :deletions, :files_affected, :timestamp, :author, :identifier)";
  $result = $app['db']->executeUpdate($sql, array(
    ':hash' => $request->get('hash'),
    ':message' => $request->get('message'),
    ':additions' => $request->get('additions'),
    ':deletions' => $request->get('deletions'),
    ':files_affected' => $request->get('files_affected'),
    ':timestamp' => $request->get('timestamp'),
    ':author' => $request->get('author'),
    ':identifier' => $request->get('identifier'),
    )
  );
  return 'Author: ' . $request->get('author');
});

$app->delete('/commits', function() use($app) {
  $sql = "TRUNCATE `commits`";
  $result = $app['db']->executeUpdate($sql);
  return "{$result} rows affected.";
});

$app->delete('/commits/{identifier}', function($identifier) use($app) {
  $sql = "DELETE FROM `commits` WHERE identifier = ?";
  $result = $app['db']->executeUpdate($sql, array($identifier));
  return "{$result} rows affected.";
});

$app->delete('/members', function() use($app) {
  $sql = "TRUNCATE `members`";
  $result = $app['db']->executeUpdate($sql);
  return "{$result} rows affected.";
});

$app->post('/members/reload', function() use($app, $config) {
  $url = $config['base_url'] . "users.json?limit=100&key=" . $config['api_key'];
  $response = Requests::get($url, array( "Accept" => "application/json" ),
    array(
      "key" => $config['api_key']
    )
  );

  $response = json_decode($response->body);

  foreach ($response->users as $index => $user) {
    $sql = "INSERT INTO `members`(`firstname`, `lastname`, `mail`, `github_id`, `birthday`, `year`, `since`, `group`) VALUES (:firstname, :lastname, :mail, :github_id, :birthday, :year, :since, :group)";
    $since = str_replace("Z", "", str_replace("T", " ", $user->created_on));
    // TODO(abhikandoi2000@gmail.com): update group for member
    $result = $app['db']->executeUpdate($sql, array(
        ':firstname' => $user->firstname,
        ':lastname' => $user->lastname,
        ':mail' => $user->mail,
        ':github_id' => $user->custom_fields[3]->value | "",
        ':birthday' => $user->custom_fields[4]->value | "",
        ':year' => $user->custom_fields[1]->value | "1",
        ':since' => $since,
        ':group' => "developer"
      )
    );
  }

  return 'Check db';
});

$app->run();
?>