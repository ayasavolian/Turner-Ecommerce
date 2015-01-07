<!--
Date: 12/22/2014
User: ayasavolian

- This action page is used to add the lead to the database from a server-side post so we can now associate
- the anonymous lead with an email address so they're known

-->
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
//getting the access token value
$response = file_get_contents('https://*****.mktorest.com/identity/oauth/token?grant_type=client_credentials&client_id='.$clientid.'&client_secret='.$client_secret.');
$response = json_decode($response);
  //grab the access_token value from the response
  $at = $response->access_token;
  //getting the Marketo tracking cookie value 
  $cook =  $_COOKIE['_mkto_trk'];

  $data['Email'] = $email;
  $data['_mkt_trk'] = $cook;
  $data['munchkinId'] = '******';
  $data['formid'] = $fid;
  $url = 'https://****/index.php/leadCapture/save';
  //pass the updated into marketo 
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
