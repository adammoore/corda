<html>
<body>
<?php
require 'vendor/autoload.php';

function listToColumn($list){
	$row_string = "<th>";
	foreach ($list as $item){
		$row_string .= $item."<br/>";
	}
	$row_string .= "</th>";
	return $row_string;
}


		$table_string = "<table>";
		$headers_string = "<tr><th>Source</th><th>ORCID id</th><th>Name</th><th>Emails</th><th>Affiliations</th></tr>";
		$table_string .= $headers_string;
		$haplo_file = file_get_contents("data/Haplo_small.json");
     		$haplo_data = json_decode($haplo_file);
     		$orcid_url = "https://pub.orcid.org/v2.1/search/?q=";
     		$orcid_employment_url = "https://pub.orcid.org/v2.1/";
     		$client = new \GuzzleHttp\Client();
     		$data = array();
     		foreach ($haplo_data as $person){
	     		$person_data = array();
			$haplo_data = array();
			$haplo_string = "<tr><th>Haplo</th>";
			$orcid_data = array();
			$orcid_string = "<tr><th>ORCID</th>";
			if ($person->orcid){
		      		$person_orcid = ($person->orcid)[0];
		    		$haplo_data["orcid"] = $person_orcid;
		     		$haplo_data["source"] = "Haplo";
				$haplo_data["names"] = $person->names;
				$haplo_name_string = listToColumn($person->names);
				$haplo_data["typeOfPerson"] = $person->typeOfPerson;
				$haplo_type_string = listToColumn($person->typeOfPerson);
				$haplo_data["emails"] = $person->emails;
				$haplo_email_string = listToColumn($person->emails);
		     		$response = $client->request('GET', $orcid_employment_url.$person_orcid, ["headers" => [ "Accept" => "application/vnd.orcid+json"]]);
		     		$raw_orcid = json_decode($response->getBody()->getContents());
		     		$h1 = 'given-names';
				$h2 = 'family-name';
				$orcid_name_string = ($raw_orcid->person->name->$h1->value.' '.$raw_orcid->person->name->$h2->value);
		     		$orcid_data["names"] = $orcid_name_string;
		     		$orcid_emails = array();
		     		foreach ($raw_orcid->person->emails->email as $email_data){
			     		array_push($orcid_emails, $email_data->email);
		     		}
				$orcid_data["emails"] = $orcid_emails;
				$orcid_email_string = listToColumn($orcid_emails);
		     		$hacky_string = 'activities-summary';
		     		$hacky_string_2 = 'employment-summary';
		     		$all_orgs = array();
		     		foreach ($raw_orcid->$hacky_string->employments->$hacky_string_2 as $employment){
			     		array_push($all_orgs, $employment->organization->name);
				}
				$orcid_employment_string = listToColumn($all_orgs);
				$orcid_data["employment"] = $all_orgs;
				$orcid_data["source"] = "ORCID";
				array_push($data, $haplo_data, $orcid_data);
	     		}
	     		else{
		     		// TODO: Ideally this returns the same data as above, so you can find ORCIDs, affiliations, and emails for people at your instittuion even if you don't have their ORCID iD
		     		// ... then maybe you can look at their email and see why you don't have their iD, etc. etc.
				$name = $person->names;
				if ($name){
					$encoded_name = urlencode($name[0]);
                			$response = $client->request('GET', $orcid_url.$encoded_name, ["headers" => [ "Accept" => "application/vnd.orcid+json"]]);
					$data = $response->getBody()->getContents();
					$orcid_data=json_decode($data);
					$orcid_identifier = "orcid-identifier";
					$orcid_id=$orcid_data->result[0]->$orcid_identifier->path;
				}
			}
	                $haplo_string .= "<th>".$person_orcid."</th>".$haplo_name_string.$haplo_email_string."<th>University of Haplo</th></tr>";
			$orcid_string .= "<th>".$person_orcid."</th><th>".$orcid_name_string."</th>".$orcid_email_string.$orcid_employment_string."</tr>";
			$table_string .= $haplo_string.$orcid_string;
		}
		$table_string .= "</table>";
      		file_put_contents("data/output.json", json_encode($data, JSON_PRETTY_PRINT));
		echo $table_string;
?>
</body>
</html>
