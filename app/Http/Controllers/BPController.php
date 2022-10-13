<?php

namespace App\Http\Controllers;


class BPController
{
    public function authenticate($accountId, $user, $password){
		$ch = curl_init();
		$authenticationDetails = array(
			'apiAccountCredentials' => array(
				'emailAddress' => $user,
				'password'     => $password,
			),
		);
		$encodedAuthenticationDetails = json_encode($authenticationDetails);
		$authenticationUrl = 'https://ws-eu1.brightpearl.com/'.$accountId.'/authorise';
		$header = array('Content-Type: application/json');

		curl_setopt($ch, CURLOPT_URL, $authenticationUrl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedAuthenticationDetails);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		if (false === $response) {
			echo 'Request unsuccessful' . PHP_EOL;
		curl_close($ch);
			exit(1);
		}
		$responseCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$responseBody = json_decode($response);
		curl_close($ch);

		if (200 !== $responseCode) {
			echo 'Authentication failed' . PHP_EOL;
		foreach ($responseBody->errors as $error) {
			echo $error->code . ': ' . $error->message . PHP_EOL;
		}
			exit(1);
		}
		$authorisationToken = $responseBody->response;
		return $authorisationToken;
	}

    public function getProductStock($accountId, $authToken, $productID){
		$header = array(
			'brightpearl-auth: '.$authToken,
			'content-Type: application/json'
		);
		$reqURL = 'https://ws-eu1.brightpearl.com/2.0.0/'.$accountId.'/warehouse-service/product-availability/'.implode(',', $productID);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec($ch);
		curl_close($ch);

		$productStock = json_decode($response, true);

		return $productStock['response'];
	}

    public function getProductIDFromSKU($accountId, $authToken, $SKU){

        $header = array(
			'brightpearl-auth: '.$authToken,
			'content-Type: application/json'
		);
		$reqURL = "https://ws-eu1.brightpearl.com/2.0.0/".$accountId."/product-service/product-search?SKU=".$SKU;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
        $productID = $response->response->results;
        return $productID;
    }
}
