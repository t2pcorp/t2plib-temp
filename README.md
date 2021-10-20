# Partner Library

## Client Token Gerneration
```
   #add library 
   composer require t2pcorp/t2plib
```

## How to Generate Toke Type H to use for Host to Host request
```
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

    $body='{"clientInfo":"BRAND0000122"}';                           //Example body text want to send 
    $clientKey = file_get_contents("./client-key-from-T2P.txt");     //Client Key from T2P
    $encryptedBody=true;                                             //request token need encrypted = true
    $result = $auth->prepareRequest($hashInfo, $body, $clientKey, $encryptedBody);  // call prepare function
    $result = json_decode($result);
    print_r($result);
```

## Result
```
stdClass Object
(
    [meta] => stdClass Object
        (
            [language] => en_EN
            [version] => 1.0.0
            [responseCode] => 1000
            [responseMessage] => Success
        )

    [data] => stdClass Object
        (
            [header] => ZXlKamJHbGxiblJEYjJSbElqb2lVVUZDV1ZCQlUxTmNiaUlzSW1Oc2FXVnVkRXhwWWxabGNuTnBiMjRpT2lJeExqQXVNQ0lzSW10bGVVTnZaR1VpT2lKY2JrdEZXVkZCUWxBd01EQXhYRzRpTENKdFpYUm9iMlFpT2lKUVQxTlVJaXdpZEdsdFpYTjBZVzF3SWpvaU1qQXlNVEEzTWpVd05ESXlOVEFpTENKMGIydGxibFI1Y0dVaU9pSklJaXdpZFhKcElqb2lYQzloZFhSb1pXNWNMM1l4WEM5amJHbGxiblJVYjJ0bGJsd3ZaMlZ1WlhKaGRHVWlmUT09OmI5NjA1Zjc1NjUyYTY0YTBjNTZiYThkMDkxZDlkOWQ0N2ZmMDM2YzhjZGFmMDczYmM5OTEzZDI2ZTdmNDg2NDAxNTcxNzY4YWMyZmQ2MjAyYmQ0MTVjZGMxZDU5M2E1NDEyOGNhYTY3ODY5MDE3M2YyNjI2YWU5NWQxZDI0OWNj
            [body] => yQuMWdeJ+NPjI7RyO7dCs7qM7BH4h+5lR4uySv0HfF4G5nYIOL9QiRGf1eg1qjQy6bjb3vAbt2Ue3/VrukFirX3LZNxfw5NxZgalNafYyR4S5r5I2OO+D+GXry5DyPz35nu1K+3hRjm03Zx2td6bWDtGL8Sj1fc0MROypx7Xn0UBgVzaN2CBm+JVuMac8ymkNq/vN++Ja5Sed3rLU0EpdPaz/rLIvqR4YSrJh1VhRRN5IKUpKX92Gmkco6XilKpkI18ZGZjKw1ulMKT6UfDLXoWIJh2PVultp8K593VEBwSELhUJM0TiYZHDLIpTklmm33lAHH74dr/78pyiz47f0Q==:RU+eGanybR3p8Sa2zj7OIoGA8xYfr14uyeVz3YQyQLo=
        )

)
```


## How to request for token type C for client use (Mobile/Web)

```
    1. on the server generate token type H for host to host with fix uri input (/authen/v1/clientToken/generate) of the request token
    2. use token that recieve post to request token to url : https://test-api-authen.t2p.co.th/authen/v1/clientToken/generate
```



# Decrypt the response from Host to Host request

```

<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once 'vendor/autoload.php';

    $auth = new T2PLib\T2PAuthen\T2PAuthentication();
    $clientKey = file_get_contents("./client-key-from-T2P.txt");     //Client Key from T2P

    //Make request to Host to Host and get the response here

    $result = $auth->decryptData($enc_message, $clientKey); 
    print_r($result);
```

# JobLibrary (Internal)
```
<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include 'vendor/autoload.php';
    use \T2PLib\JobLibrary\JobLibrary;

    $job = new JobLibrary();
    
    $job->setEmail("auth@email.com");
    $job->setPassword("PasswodAuthen");
    $job->setDomain('EXAMPLE');
    $job->setJobID('EXAMPLE001');
    $job->setName('Example Name');
    $job->setPeriodTypeMin();
    $job->setPeriodValue('1');
    $job->setScheduleTime('01:00:00');
    $job->setExecuteDuration('1');
    $job->setLINENotification('token');
    $job->setNotiFrequency('1');
    $job->setArchiveLogUnitDay();
    $job->setArchiveLogValue('1');
    
    // Do Job Process......

    $job->updateJobStatus(); // Done with Success Update
    or
    $job->updateJobStatus("Error Message"); // Done with Failed Update

```

# Jobs API Create Authen User
```
    php artisan user:register test1 test1@email.com password
```