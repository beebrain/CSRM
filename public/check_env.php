<?php
header('Content-Type: text/plain');
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require $paths->systemDirectory . '/Boot.php';

$config = new Config\App();
echo "BaseURL from Config: " . $config->baseURL . "\n";
echo "IndexPage from Config: " . $config->indexPage . "\n";
echo "URIProtocol from Config: " . $config->uriProtocol . "\n";
echo "CI_ENVIRONMENT constant: " . (defined('ENVIRONMENT') ? ENVIRONMENT : 'not defined') . "\n";
echo "CI_ENVIRONMENT env: " . (getenv('CI_ENVIRONMENT') ?: 'not set') . "\n";
