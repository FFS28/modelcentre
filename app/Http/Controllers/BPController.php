<?php

namespace App\Http\Controllers;

use PDO;

class BPController
{
    private $account = "";
    private $user = "";
    private $password = "";
    private $authToken = "";

    public function init(){
        $this->account = env('BP_ACCOUNT');
        $this->user = env('BP_USER');
        $this->password = env('BP_PASSWORD');
    }

    public function authenticate(){
        $ch = curl_init();
		$authenticationDetails = array(
			'apiAccountCredentials' => array(
				'emailAddress' => $this->user,
				'password'     => $this->password,
			),
		);
		$encodedAuthenticationDetails = json_encode($authenticationDetails);
		$authenticationUrl = 'https://ws-eu1.brightpearl.com/'.$this->account.'/authorise';
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
		$this->authToken = $responseBody->response;
	}

    public function getProductStock($productID){
		$header = array(
			'brightpearl-auth: '.$this->authToken,
			'content-Type: application/json'
		);
		$reqURL = 'https://ws-eu1.brightpearl.com/2.0.0/'.$this->account.'/warehouse-service/product-availability/'.implode(',', $productID);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec($ch);
		curl_close($ch);

		$productStock = json_decode($response, true);

		return $productStock['response'];
	}

    public function getProductIDFromSKU($SKU){

        $header = array(
			'brightpearl-auth: '.$this->authToken,
			'content-Type: application/json'
		);
		$reqURL = "https://ws-eu1.brightpearl.com/2.0.0/".$this->account."/product-service/product-search?SKU=".$SKU;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
        $productID = $response->response->results;
        return $productID;
    }

    public function isExistCustomer($email){
        $header = array(
			'brightpearl-auth: '.$this->authToken,
			'content-Type: application/json'
		);
		$reqURL = "https://ws-eu1.brightpearl.com/2.0.0/".$this->account."/contact-service/contact-search?primaryEmail=".$email;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);

        $productID = $response->response->results;

        return count($productID) === 0 ? false : true;
    }

    public function getCustomerId($email){
        $header = array(
			'brightpearl-auth: '.$this->authToken,
			'content-Type: application/json'
		);
		$reqURL = "https://ws-eu1.brightpearl.com/2.0.0/".$this->account."/contact-service/contact-search?primaryEmail=".$email;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = json_decode(curl_exec($ch));
		curl_close($ch);

        $productID = $response->response->results;

        return $productID[0][0];
    }

    public function createBPCustomer($customerInfo){

        $header = array(
			'brightpearl-auth: '.$this->authToken,
			'content-Type: application/json'
		);

        $reqURL = "https://ws-eu1.brightpearl.com/2.0.0/".$this->account."/contact-service/contact";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $customerInfo);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($ch));
		curl_close($ch);

        return $response->response;
    }

    public function updateBPCustomer($id, $customerInfo){

        $header = array(
			'brightpearl-auth: '.$this->authToken,
			'content-Type: application/json'
		);

        $reqURL = "https://ws-eu1.brightpearl.com/2.0.0/".$this->account."/contact-service/contact/".$id;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $customerInfo);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($ch));
		curl_close($ch);
        return $response->response->contactId;
    }

    public function createNewAddress($address) {
        $header = array(
			'brightpearl-auth: '.$this->authToken,
			'content-Type: application/json'
		);

        $reqURL = "https://ws-eu1.brightpearl.com/2.0.0/".$this->account."/contact-service/postal-address";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $address);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($ch));
		curl_close($ch);

        return $response->response;
    }

    public function createNewOrder($info){
        $header = array(
			'brightpearl-auth: '.$this->authToken,
			'content-Type: application/json'
		);

        $reqURL = "https://ws-eu1.brightpearl.com/2.0.0/".$this->account."/order-service/sales-order";

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $reqURL);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $info);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($ch));
		curl_close($ch);

    }
}
