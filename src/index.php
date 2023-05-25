<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

use RafaelDuarte\OlhoVivo;

if ($_ENV['APP_DEBUG']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

$client = new OlhoVivo();

$response = $client->getAllBusLanes();

dump($response);
