<?php
session_start();
require 'servercall.php';
$email = $_POST['newsletter'];
$sessid = $_POST['sessid'];
$url = $_POST['url'];
$_SESSION["email"] = $email;
$fid = "1002";
$return_url = base64_decode($_POST["return_url"]);

$sqluser = "INSERT INTO `ecommerce`.`chosen` (`currdate`, `userid`, `firstname`, `lastname`, `email`, `street`, `city`, `state`, `zip`, `country`) 
        VALUES (CURRENT_TIMESTAMP, '$sessid', '', '', '$email', '', '', '', '', '')";
        $insertresults = mysqli_query($mysqli, $sqluser);

// values for the ReST API
// This can be gathered from within Marketo Admin

$clientid = '*****';
$client_secret = '****';
$identity = '******';
$endpoint = '******';

$response = file_get_contents('https://*****.mktorest.com/identity/oauth/token?grant_type=client_credentials&client_id=999fac05-cca8-45d2-862a-60f00c0ca0ff&client_secret=k0M7c8oIRkg5S1V9m2yJvdyFe0lQ8p6w');
$response = json_decode($response);
  //grab the access_token value
  $at = $response->access_token;
  $cook =  $_COOKIE['_mkto_trk'];
  //$cook = substr($cook, strpos($cook, 'token:') + 6, strlen($cook));

  $data['Email'] = $email;
  $data['_mkt_trk'] = $cook;
  $data['munchkinId'] = '******';
  $data['formid'] = $fid;
  $url = 'https://app-ab08.marketo.com/index.php/leadCapture/save';
  //$data = array('FirstName' => 'Steven', 'Company' => 'Naval Reactors', 'LastName' => 'Simoni', 'Email' => 'steven.m.simoni@gmail.com', 'munchkinId' => '226-FBL-320', 'formid' => '1015', '_mkto_trk' => 'id:226-FBL-320&token:_mch-marketosolutionsconsulting.com-1385595977896-52442');
  // use key 'http' even if you send the request to https://...
  $options = array(
   'http' => array(
       'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
       'method'  => 'POST',
       'content' => http_build_query($data),
    ),
  );
  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);

header('Location:'.$return_url);
?>