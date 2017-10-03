<?php

class RestApi{
	public $uri;

	public function rest_get_method(){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->uri,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
		  ),
		));

		$response= curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
		  $error= "cURL Error #:" . $err;
		  return $error;
		} else {
		  return $response;
		}
	}
	public function rest_put($post, $token){
		$data = $this->prepare_insert_data_array($post);
		// 1. ADDING CONTACT.
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.infusionsoft.com/crm/rest/v1/contacts?access_token=".$token,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-Type: application/json; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
		  ),
		));

		$response = curl_exec($curl);
		$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  if($httpcode == 201){ 
		  	// Created
			// echo "<br>".$httpcode. " Contact Created.<br>".$response;
		  	return $response; 
		  }else { 
		  	// Error
		  	return false;
		  }
		}
	}
	public function rest_update($ID, $post, $token){
		$data = $this->prepare_insert_data_array($post);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.infusionsoft.com/crm/rest/v1/contacts/".$ID."?access_token=".$token,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "PATCH",
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: application/json; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return false;
			// echo "cURL Error #:" . $err;
		} else {
			return $response;
		}
	}
	public function rest_delete($ID, $token){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.infusionsoft.com/crm/rest/v1/contacts/".$ID."?access_token=".$token,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "DELETE",
		  CURLOPT_POSTFIELDS => "",
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) { return false; } 
		else { return $response; }
	}
	public function prepare_insert_data_array($post){
		$result = $temp = [];
        //Phone
        if ( isset($post['phone']) && !empty(trim($post['phone'])) ) {
            $temp = [];
            $temp[] = array(
                "number" => trim($post['phone']),
                "extension" => null,
                "field" => "PHONE1",
                "type" => "Other"
                );
            $result['phone_numbers'] = $temp;
        }
        //Email
        if ( isset($post['email']) && !empty(trim($post['email'])) ) {
            $temp = [];
            $temp[] = array(
                "email" => trim($post['email']),
                "field" => "EMAIL1"
                );
            $result['email_addresses'] = $temp;
        }
        // $result['website'] = isset($post['website']) && !empty(trim($post['website'])) ? trim($post['website']) : '';

        $result['given_name'] = isset($post['given_name']) && !empty(trim($post['given_name'])) ? trim($post['given_name']) : '';
        $result['family_name'] = isset($post['family_name']) && !empty(trim($post['family_name'])) ? trim($post['family_name']) : '';
        // $result['tags'] = isset($post['tags']) && !empty(trim($post['tags'])) ? trim($post['tags']) : '';
        // $result['special_tags'] = isset($post['special_tags']) && !empty(trim($post['special_tags'])) ? trim($post['special_tags']) : '';
        return json_encode($result);
	}
	public function prepare_insert_tags_array($post){
		$result = $temp = [];
        return null;
	}
	public function add_campaign(){
		$ch = curl_init();

	    curl_setopt_array($ch, array(
	      CURLOPT_URL => $this->uri,
	      CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_ENCODING => "",
	      CURLOPT_MAXREDIRS => 10,
	      CURLOPT_TIMEOUT => 30,
	      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	      CURLOPT_CUSTOMREQUEST => "POST",
	      CURLOPT_HTTPHEADER => array(
	        "cache-control: no-cache",
	      ),
	    ));

	    $response_camp = curl_exec($ch);
	    $httpco = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    echo $httpco;
	    $errca = curl_error($ch);
	    curl_close($ch);

	    if ($errca) {
	      echo "cURL Error #:" . $errca;
	    } else {
	      if($httpco == 204){
	        return true;
	      }else{
	      	return $response_camp;
	      }
	    }
	}

	public function add_tags($tags){
		// echo "<pre>";print_r($tags);echo "</pre>";
		foreach ($tags as $key => $value) {
			foreach ($value as $ind => $val) {
				$ids.=$val.',';
			}
		}
		$tagIds = rtrim($ids,',');
		$data = '
			{
			  "tagIds": [
			    '.$tagIds.'
			  ]
			}
		';
		// print_r($data);

		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->uri,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => array(
		    "cache-control: no-cache",
		    "content-Type: application/json;"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		  return false;
		} else {
		  if($response){
		  	return true;
		  }
		}
	}
} 