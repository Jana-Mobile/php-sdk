<?php

require_once "../jana-php-sdk/jana.php";

$secretKey = '293af117b8f14232ad86099f730629bc';
$clientId = 'gta2i';
$offerId = 'irl_mim6lf';

$jana = new Jana($clientId, $secretKey);

$response = $jana->getJIALink($offerId);

if ($response && $response['success']) {
   $url = $response['link'];
   echo "JIA Link retrieved: <a href=\"$url\">$url</a>";
} elseif ($response && $response['error']) {
   $error = $response['error'];
   echo "Error retrieving link: $error";  
} else {
   echo "Unexpected error retrieving link";
}
