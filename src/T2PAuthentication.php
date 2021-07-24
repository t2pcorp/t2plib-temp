<?php
namespace T2PAuthen;

use \T2PAuthen\T2PAuthenException;

class T2PAuthentication
{
    private $keys;

    const LIBVERSION="1.0.0";
 
    private function createHMAC($text_tohash)
    {
        return hash_hmac("sha512", $text_tohash, base64_decode(trim($this->keys['hmacKey'])));
    }

    private function createSignature($text_tosign)
    {
        openssl_sign($text_tosign, $signature, $this->keys['privateKey'], OPENSSL_ALGO_SHA512);
        return base64_encode($signature);
    }
    
    /**
     * parse key info from key contents
     * @param hashInfoString method/timestamp/calling_uri
     * @param bodyContentsString
     * @return header signature string
     */
    private function createHeaderSignature($hashInfo, $bodyContentsString)
    {
        if (!is_array($hashInfo)) {
            throw new T2PAuthenException("hashInfo is array type required!", 1666);
        }
        $isset=true;
        $isset=$isset && isset($hashInfo['method']);
        $isset=$isset && isset($hashInfo['uri']);
        $isset=$isset && isset($hashInfo['timestamp']);
        if (!isset($hashInfo['tokenType'])) {
            $hashInfo['tokenType']='H';
        }
        if (!$isset) {
            throw new T2PAuthenException("invalid header info data", 1666);
        }

        $timestamp=$hashInfo['timestamp'];
        if (strlen($timestamp)!=14) {
            throw new T2PAuthenException("invalid header info timestamp", 1666);
        }

        //set auth keycontents info
        $hashInfo['clientCode']=$this->keys['clientCode'];
        $hashInfo['keyCode']=$this->keys['keyCode'];
        $hashInfo['clientLibVersion']=self::LIBVERSION;

        //sort by key and conat and hash
        ksort($hashInfo);
        $hashInfoString = implode('', $hashInfo);
        $hashmac=$this->createHMAC($hashInfoString.$bodyContentsString);
        $hashInfo=base64_encode(json_encode($hashInfo));
        $header= base64_encode($hashInfo.':'.$hashmac);

        return $header;
    }

    /**
     * parameters
     * $message string message to encrypt
     * $keys  public/private key PEM String to use
     *
     * return
     *  base64 encrypted text , can use decryptMessage function with same public/private key to decrypt message
     */
    public function encryptData($message, $keyContents)
    {
        $keyContents=$this->extractKey($keyContents);
        $encrypted=$this->encryptAES256CBC($message);
        openssl_public_encrypt($encrypted['key'].':'.$encrypted['iv'], $key_encrypted, $this->keys['publicKey'], OPENSSL_PKCS1_OAEP_PADDING);
        return base64_encode($key_encrypted).":".$encrypted['encrypted'];
    }

    public function decryptData($enc_message, $keyContents)
    {
        $keyContents=$this->extractKey($keyContents);
        $data=preg_split("/:/", $enc_message);
        openssl_private_decrypt(base64_decode($data[0]), $aes_keys, $this->keys['privateKey'], OPENSSL_PKCS1_OAEP_PADDING);
        $data=$this->decryptAES256CBC($data[1], $aes_keys);
        return $data;
    }

    private function encryptMessage($message)
    {
        $encrypted=$this->encryptAES256CBC($message);
        openssl_public_encrypt($encrypted['key'].':'.$encrypted['iv'], $key_encrypted, $this->keys['publicKey'], OPENSSL_PKCS1_OAEP_PADDING);
        return base64_encode($key_encrypted).":".$encrypted['encrypted'];
    }

    /**
     * @param $data
     * @param $encryption_key
     * @return string
     */
    private function encryptAES256CBC($data)
    {
        $key = openssl_random_pseudo_bytes(32);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        $encrypted = $encrypted;
        $enc['key']=base64_encode($key);
        $enc['iv']=base64_encode($iv);
        $enc['encrypted']=$encrypted;
        return $enc;
    }
  
    /**
     * parse key info from key contents
     * @param $pem_keys
     * @return array
     */
    private function extractKey($pem_keys)
    {
        if (!isset($this->keys)) {
            $keys=preg_split("/:/", $pem_keys);
            $this->keys['clientCode']=$keys[0];
            $this->keys['keyCode']=$keys[1];
            $this->keys['hmacKey']=$keys[2];
            $this->keys['publicKey']=$keys[3];
            $this->keys['privateKey']=$keys[4];
        }
        return $this->keys;
    }
  
    private function getHashMac($input)
    {
        if (is_array($input) || is_object($input)) {
            $input = json_encode($input);
        }
        $hash = hash_hmac("sha512", $input, base64_decode(trim($this->TokenHashKey)));
        return $hash;
    }

    private function initOutput()
    {
        $output = json_decode("{}");
        $output->meta = json_decode("{}");
        $output->data = json_decode("{}");

        $output->meta->language="en_EN";
        $output->meta->version=self::LIBVERSION;
        return $output;
    }

    /**
     * prepare token for Host to Host request see example below
     * @param $pem_keys
     * @return array
     */
    public function prepareRequest($hashInfoString, $bodyContentsString, $keyContents, $isEncryptBody)
    {
        $output = self::initOutput();

        try {
            $keyContents=$this->extractKey($keyContents);
            if (isset($hashInfoString['tokenType']) && $hashInfoString['tokenType']!='C') {
                if ($isEncryptBody) {
                    $bodyContentsString=$this->encryptMessage($bodyContentsString);
                }
            }
            
            $headerSignature=$this->createHeaderSignature($hashInfoString, $bodyContentsString);

            $output->meta->responseCode=1000;
            $output->meta->responseMessage="Success";
            $output->data->header=$headerSignature;
            $output->data->body=$bodyContentsString;
        } catch (T2PAuthenException $ex1) {
            $output->meta->responseCode=$ex1->getCode();
            $output->meta->responseMessage=$ex1->getMessage();
            $output->data->header="";
            $output->data->body="";
        } catch (\Exception $ex2) {
            $output->meta->responseCode=1888;
            $output->meta->responseMessage=$ex2->getMessage();
            $output->data->header="";
            $output->data->body="";
        }

        return json_encode($output);
    }
}

/*
########################################################################################
#Example Partners Host to T2P Host
########################################################################################
#new authen Object
$auth = new T2PAuthentication();

#1.Prepare Data
$method='POST';                     // request method
$timestamp=date('YmdHis');          // current client timestamp
$callingUri='/walletapi/v1/wallets';// Service URI endpoint want to call
$hashInfo=array(
                    "method"=>$method,
                    "uri"=>$callingUri,
                    "timestamp"=>$timestamp,
                    "tokenType"=>"H"
                );

$body='{"title":"Testing Client-Server Verification"}';                     // Example body text want to send : json text
$clientKey = file_get_contents("./client-key-from-T2P.txt");                // Client Key from T2P
$encryptedBody=false;                                                       // if service need encrypted body input = true
$result = $auth->prepareRequest($hashInfo, $body, $clientKey, $encryptedBody);  // call prepare function

#2. using respone to Header, body and Make A request
$result=json_decode($result);
if ($result->meta->responseCode==1000) {//Success
    echo "Use this data to request\r\n";
    print_r($result->data->header);
    echo "\r\n";
    print_r($result->data->body);
    echo "\r\n";
} else {
    echo "prepare failed !!";
    print_r($result);
}

*/


/*
########################################################################################
#Example request token for Mobile Client to T2P Host
########################################################################################
#Example Partners Host to T2P Host
#new authen Object
$auth = new T2PAuthentication();

#1.Prepare Data
$method='POST';                                 //request method
$timestamp=date('YmdHis');                      //current client timestamp
$callingUri='/authen/v1/clientToken/generate';  // Fixed Authen API URI endpoint to call
$hashInfo=array(
                    "method"=>$method,
                    "uri"=>$callingUri,
                    "timestamp"=>$timestamp,
                    "tokenType"=>"H"
                );

$body='{"clientInfo":"Some usefull client information"}';                       //Example body text want to send : json text
$clientKey = file_get_contents("./client-key-from-T2P.txt");                    //Client Key from T2P
$encryptedBody=true;                                                            //request token need encrypted = true
$result = $auth->prepareRequest($hashInfo, $body, $clientKey, $encryptedBody);  // call prepare function


#2. using respone to Header, body and Make A request for token
$result=json_decode($result);
if ($result->meta->responseCode==1000) {//Success
    print_r($result->data->header);
    print_r($result->data->body);

    #3.Curl to T2P Authen API to request Token with Body , Header from result above
    $AuthenApi = 'https://dev-api-authen.t2p.co.th/authen/v1/clientToken/generate';

    #4.response will be like:
    $tokenResponse = '"data": { "authToken": "ZXlKamJHbGxiblJEYjJSbElqb2lVVUZDV1ZCQlUxTWlMQ0pqYkdsbGJuUk1hV0pXWlhKemFXOXVJam9pTVM0d0xqQWlMQ0pyWlhsRGIyUmxJam9pUzBWWVVRkNVREF3TURFaUxDSnRaWFJvYjJRaU9pSlFUMU5VSWl3aWRHbHRaWE4wWVcxd0lqb2lNakF4T1RFeU1UQXhNekEzTlRRaUxDSjBiMnRsYmxSNWNHVWlPaUpESWl3aWRYSnBJam9pSW4wPTozNjM5YmI4MjlmNzY1MTVhNDg5YmY5YWMxNDA5NTNhY2NlNzNiYzY2YzMyMzRkOWY0ZDFjYzgzZGQ2NTI3MmU3ZDE2MTQ3ZjIyNTg4MDgzNjA4YWQ1ZjliNWVmZDE1ZWZhMmQ5OGU3ZGEwMWZlMTBkZDJjMzRkNjYyM2Q0N2VkZA==",        "dateOfExpire": "20191210180754",        "timeServer": "20191210130754"    }';

    #5.reponse "authToken"  to mobile to use (token has expiration timestamp)
} else {
    echo "prepare failed !!";
}

*/
