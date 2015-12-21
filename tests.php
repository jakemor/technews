<?php 

// MARK: environment setup
header('Content-Type: application/json');
error_reporting(-1);
ini_set('display_errors', 'On');
ini_set('html_errors', false);

// // MARK: require statements
require_once 'app/App.php';

$n = 10;

$start_time = microtime(true);

$func = $_GET["func"];
unset($_GET["func"]);

$app = new App();

for ($i=0; $i < $n; $i++) { 
	if (method_exists($app, $func)) {
		$app->startTimer();
		$app->$func($_GET); 
	} else {
		$e = new Exception("Endpoint not found.", CLIENT_ERROR);
		$app->response->addError($e);
	}
	$app->send(false);
}

$response = $app->response->get();

$average_time = array_sum($response["time"])/sizeof($response["time"]);

$response = [
	"iterations" => $n,
	"total_time" => microtime(true) - $start_time,
	"average_time" => $average_time,
	"responses" => $app->send(false)
];

echo json_encode($response);