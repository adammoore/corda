<?php
require 'vendor/autoload.php';
use Luracast\Restler\Format\HtmlFormat;

function listToColumn($list){
	$row_string = "<th>";
	foreach ($list as $item){
		$row_string .= $item."<br/>";
	}
	$row_string .= "</th>";
	return $row_string;
}

function getOrcidData($orcid){
	// gets ORCID data for a given orcid id and appiies it to our JSON format and returns that data
        $client = new \GuzzleHttp\Client();
	$orcid_employment_url = "https://pub.orcid.org/v2.1/";
	try{
		$response = $client->request('GET', $orcid_employment_url.$orcid, ["headers" => [ "Accept" => "application/vnd.orcid+json"]]);
	} catch (Exception $e){
		return null;
	}
	if ($response->getStatusCode()!=200){
		return null;
	}
	$raw_orcid = json_decode($response->getBody()->getContents());
	$orcid_data = array();
	$orcid_data["orcid"] = array($orcid);
        $orcid_name_string =  array($raw_orcid->person->name->{given-names}->value.' '.$raw_orcid->person->name->{family-name}->value);
        $orcid_data["names"] = $orcid_name_string;
        $orcid_emails = array();
        foreach ($raw_orcid->person->emails->email as $email_data){
	        array_push($orcid_emails, $email_data->email);
        }
        $orcid_data["emails"] = $orcid_emails;
        $h3 = 'activities-summary';
        $h4 = 'employment-summary';
        $all_orgs = array();
        foreach ($raw_orcid->$h3->employments->$h4 as $employment){
       		array_push($all_orgs, $employment->organization->name);
	}
        $orcid_data["employment"] = $all_orgs;
        $orcid_data["source"] = "ORCID";
        return $orcid_data;
}

function deriveOrcidUser($person){
	/* implement this function to use data in the person record to derive ORCID data- current code commented out represents 
	an early prototype of using the name in a person to make a request to the orcid api to retrieve a full orcid record 
	
		$encoded_name = urlencode($name[0]);
		$response = $client->request('GET', $orcid_url.$encoded_name, ["headers" => [ "Accept" => "application/vnd.orcid+json"]]);
		print("<br>");
		$data = $response->getBody()->getContents();
		$orcid_data=json_decode($data);
		$orcid_identifier = "orcid-identifier";
		$orcid_id=$orcid_data->result[0]->$orcid_identifier->path;
		print ($orcid_id.", ". $name[0].", "."ORCID API");	
	*/
}


function addOrcidToData($data, $source){
	// Takes some data (Haplo or EPrints) and returns a list of JSON objects with ORCID data added in
	$output_data = array();
	foreach ($data as $person){
		$person_data = $person;
		$person_data["source"] = $source;
		// if this person already has an ORCID, make a request to the ORCID API to find the contents of this user's ORCID record additionally
		if ($person["orcid"]){
			$person_orcid = ($person["orcid"])[0];
			$orcid_data = getOrcidData($person_orcid);
			if ($orcid_data){
				array_push($output_data, $orcid_data);
			}
		}
		else{
			deriveOrcidUser($person);
		}
		array_push($output_data, $person_data);
	}
	return $output_data;
}

function convertDataToHtml($data){
	// Converts the provided data object into html and returns it as a string
	$html = "<table><tr><th>Source</th><th>ORCID id</th><th>Names</th><th>Emails</th><th>Employment</th><th>typeOfPerson</th></tr>";
	foreach($data as $datatable){
		// haplo and orcid are currently their own lists within the returned JSON object so iterate over these separately
		foreach($datatable as $person){
			$person_html = "<tr>";
			$person_html .= "<th>".$person->source."</th>";
			// This ought to be replaced by ternaries ?
			if ($person->orcid){
				$person_html .= "<th>".($person->orcid)[0]."</th>";
			}
			else {
				$person_html .= "<th></th>";
			}
			if ($person->names){
				$person_html .= listToColumn($person->names);
			}
			else {
				$person_html .= "<th></th>";
			}
			if ($person->emails){
				$person_html .= listToColumn($person->emails);
			}
			else{
				$person_html .= "<th></th>";
			}
			if ($person->employment){
				$person_html .= listToColumn($person->employment);
			}
			else {
				$person_html .= "<th></th>";
			}
			if ($person->typeOfPerson){
				$person_html .= "<th>".($person->typeOfPerson)[0]."</th>";
			}
			else{
				$person_html .= "<th></th>";
			}
			$person_html .= "</tr>";
			$html .= $person_html;
		}
	}
	$html .= "</table>";
	return $html;
}

class Downloader{
	public function ReturnIDList(){
		$haplo_file = file_get_contents("data/Haplo.json");
		$haplo_data = json_decode($haplo_file, true);
		$haplo_data = addOrcidToData($haplo_data, "Haplo");
		$eprints_file = file_get_contents("data/Eprints.json");
		$eprints_data = json_decode($eprints_file, true);
		$eprints_data = addOrcidToData($haplo_data, "EPrints");
		$data = array();
		array_push($data, $eprints_data, $haplo_data);
      		file_put_contents("data/output.json", json_encode($data, JSON_PRETTY_PRINT));
	}
	public function PrintOutput(){
		$output_file = file_get_contents("data/output.json");
		$output_string = convertDataToHtml(json_decode($output_file));
		echo $output_string;
	}
}
?>
