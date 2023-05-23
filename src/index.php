<?php
# Arquivo de entrada utilizado para testes de desenvolvimento utilizando o docker

require_once dirname(__DIR__, 1) . '/vendor/autoload.php';

if ($_ENV['APP_DEBUG']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

use RafaelDuarte\OlhoVivo;

$api = new OlhoVivo();

$response = $api->buscarLinhas('Vila sabrina');

foreach ($response as $linha) {
    echo $linha?->cl . '<br>';
    echo $linha?->tp . '<br>';
    echo $linha?->ts . '<br>';
    echo '<br>';
}
