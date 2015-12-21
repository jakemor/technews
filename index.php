<?php 

// MARK: environment setup
header('Content-Type: application/json');
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('html_errors', false);

// // MARK: require statements
require_once 'app/App.php';

$app = new App();

if (empty($_GET["func"])) {
	$e = new Exception("Specify an endpoint!", CLIENT_ERROR);
	$app->response->addError($e);
	$app->send();
	exit();
}

$func = trim($_GET["func"], "/");
unset($_GET["func"]);

$app->startTimer();

if (method_exists($app, $func)) {
	
	$working = $app->everytime($_GET, $_POST);

	if (!$working) {
		$e = new Exception("Something went wrong. Try loggin in again.", CLIENT_ERROR);
		$app->response->addError($e);
	}

	$app->$func($_GET, $_POST); 

} else {
	$e = new Exception("Endpoint not found.", CLIENT_ERROR);
	$app->response->addError($e);
}

$app->send();




























































































