<?php
require 'vendor/autoload.php';

class Downloader{
   public function ReturnIDList(){
      $haplo_file = file_get_contents("data/Haplo.json");
      $haplo_data = json_decode($haplo_file);
      $orcid_url = "https://pub.orcid.org/v2.1/search/?q=";
      $client = new \GuzzleHttp\Client();
      foreach ($haplo_data as $person){
	     if ($person->orcid){
		     print(($person->orcid)[0].",".$person->names[0].",". "HAPLO DATA");
		     print("<br>");
	     }
	     else{
		$name = $person->names;
		if ($name){
			$encoded_name = urlencode($name[0]);
                	$response = $client->request('GET', $orcid_url.$encoded_name, ["headers" => [ "Accept" => "application/vnd.orcid+json"]]);
			print("<br>");
			$data = $response->getBody()->getContents();
			$orcid_data=json_decode($data);
			$orcid_identifier = "orcid-identifier";
			$orcid_id=$orcid_data->result[0]->$orcid_identifier->path;
		        print ($orcid_id.", ". $name[0].", "."ORCID API");	
		}
	     }
      }
   } 
}
