<?php
require 'vendor/autoload.php';


class DemoConnector {
	private $connectorName="Haplo";
	
	function hello($to='world') {
		return "Hello $to!";
	}
	function hi($to) {
		return  "Hi $to!";
	}


	function connect_haplo(){
        	$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', 'https://dev7a26.infomanaged.co.uk/api/demojisc-hackday/get-people-data');

		echo $response->getStatusCode(); # 200
		echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
		$data = json_decode( $response->getBody()); # '{"id": 1420053, "name": "guzzle", ...}'
		var_dump($response->getBody());
		file_put_contents("data/".$this->connectorName.".json", $response->getBody());
	}
	function connect_eprints(){
		$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', 'http://63.35.7.234/eprints/cgi/users/orcid_api/creators/users', ['auth' => ['admin', 'Zzr1161']]);
		echo $response->getStatusCode();
		echo $response->getHeaderLine('content-type');
		
                $data = json_decode( $response->getBody()); # '{"id": 1420053, "name": "guzzle", ...}'
                var_dump($response->getBody());
		file_put_contents("data/Eprints.json", $response->getBody());
	}
}
