<?php
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

require_once dirname(dirname(__FILE__)).'/vendor/autoload.php';

$api_key = 'AIzaSyBPPLGL-RgI732EuVylSSFNaIGr2NRQYJ4';
$model = 'gemini-1.5-flash';
$api_version = 'v1';

$url = "https://generativelanguage.googleapis.com/{$api_version}/models/{$model}:generateContent?key={$api_key}";
?>