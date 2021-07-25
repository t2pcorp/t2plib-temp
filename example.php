<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

$auth = new T2PLib\T2PAuthen\T2PAuthentication();

$method='POST';                                 //request method
$timestamp=date('YmdHis');                      //current client timestamp
$callingUri='/authen/v1/clientToken/generate';  // Fixed Authen API URI endpoint to call
$hashInfo=array(
                    "method"=>$method,
                    "uri"=>$callingUri,
                    "timestamp"=>$timestamp,
                    "tokenType"=>"H"
                );

$body='{"clientInfo":"BRAND0000122"}';                       //Example body text want to send : json text  *********** usefull for tracking error
$clientKey = file_get_contents("./client-key-from-T2P.txt");                    //Client Key from T2P
$encryptedBody=true;                                                            //request token need encrypted = true
$result = $auth->prepareRequest($hashInfo, $body, $clientKey, $encryptedBody);  // call prepare function
$result = json_decode($result);

print_r($result);