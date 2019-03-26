<?php
require 'vendor/autoload.php';


class DownloaderFromRinggold{
   public function ReturnIDListByRingold($ringgold){
      $orcid_url = "https://pub.orcid.org/v2.1/search/?q=ringgold-org-id:$ringgold&rows=100";
      $client = new \GuzzleHttp\Client();
      $data = array();
      $response = $client->request('GET', $orcid_url, ["headers" => [ "Accept" => "application/vnd.orcid+json"]]);
      $data = $response->getBody()->getContents();
      $orcid_data=json_decode($data);
      $orcids = array();
    //  print_r($orcid_data->result);
      $offset=0;
      $max=0;
      while (!empty($orcid_data->result)){
	 foreach ($orcid_data->result as $item){
              	$orcid_identifier="orcid-identifier";
		$orcid = $item->$orcid_identifier->path;
		print ($orcid);
		$orcids[]=$orcid;
     	 	}
	      file_put_contents("output".$offset.".json", json_encode($orcids));
	      $offset= $offset+100;
	      $response = $client->request('GET', $orcid_url."&start=".$offset, ["headers" => [ "Accept" => "application/vnd.orcid+json"]]);
      	      $data = $response->getBody()->getContents();
	      $orcid_data=json_decode($data);

      }
      
	file_put_contents("output.csv", implode("\n", $orcids));
        print_r(json_encode($orcids));
  }
} 
    /*  foreach ($haplo_data as $person){
	     if ($person->orcid){
		     print(($person->orcid)[0].",".$person->names[0].",". "HAPLO DATA");
		     print("<br>");
		     $data["orcid"] = ($person->orcid)[0];
		     $data["source"] = "Haplo";
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
      file_put_contents("output.json", json_decode($data));*/
