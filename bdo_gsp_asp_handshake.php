<?php

function genBDOGSPAuthKey()
{
    $utxn = ''; // unique transaction id 
    $baseURL = ''; // BDO GSP sandbox auth URL
    $clientId = ''; //BDO GSP sandbox client id 
    $clientSec = ''; //BDO GSP sandbox client secret
    openssl_public_decrypt(base64_decode($clientSec), $decrypted, file_get_contents('GSPpublicKey.pem'));
    openssl_public_encrypt($decrypted, $encrypted, file_get_contents('GSPpublicKey.pem'));
    $clientSecEncoded = base64_encode($encrypted);
    $headers = array();
    array_push($headers, 'Content-Type: application/json');
    array_push($headers, 'txn: ' . $utxn);
    $data['clientid'] = $clientId;
    $data['clientsecretencrypted'] = $clientSecEncoded;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_URL, $baseURL );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $outData = json_decode($response);
    $return = false;
    if ($outData && $outData->gsp_auth_token) {
        $return = [$outData->gsp_auth_token, $outData->expiry]; // gsp_auth_token, expiry are returned on successful handshake.
    }
    return $return;
}
