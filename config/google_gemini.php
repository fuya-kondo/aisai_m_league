<?php
use GeminiAPI\Client;
use GeminiAPI\Resources\Parts\TextPart;

$api_key = 'AIzaSyBPPLGL-RgI732EuVylSSFNaIGr2NRQYJ4';
$model = 'gemini-1.5-flash';
$api_version = 'v1';

$url = "https://generativelanguage.googleapis.com/{$api_version}/models/{$model}:generateContent?key={$api_key}";
?>