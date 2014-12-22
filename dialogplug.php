<!--
	This is the dialog plug
	REST API Parameters
	token: 9fc522e0-c5e2-4eb5-9056-fb5aeceaa02d:ab
	client service: k0M7c8oIRkg5S1V9m2yJvdyFe0lQ8p6w
	client id: 999fac05-cca8-45d2-862a-60f00c0ca0ff

	http://100-AEK-913.mktorest.com/rest/v1/lead/{id}.json
	http://100-AEK-913.mktorest.com/rest/v1/lead/60.json?access_token=cdf01657-110d-4155-99a7-f986b2ff13a0:int
	https://314-QVX-610.mktorest.com/rest/v1/lead/60.json?access_token=9fc522e0-c5e2-4eb5-9056-fb5aeceaa02d:ab

-->
<?php
 
  $debug = true;
 
  $marketoSoapEndPoint     = "https://314-QVX-610.mktoapi.com/soap/mktows/2_7"; 
  $marketoUserId           = "mktodemoaccount2271_953382685460FF2A57DAE8";
  $marketoSecretKey        = "699028073596399455446600FFFF22BC5577EE3BCC64";
  $marketoNameSpace        = "http://www.marketo.com/mktows/";
 
  // Create Signature
  $dtzObj = new DateTimeZone("America/Los_Angeles");
  $dtObj  = new DateTime('now', $dtzObj);
  $timeStamp = $dtObj->format(DATE_W3C);
  $encryptString = $timeStamp . $marketoUserId;
  $signature = hash_hmac('sha1', $encryptString, $marketoSecretKey);
 
  // Create SOAP Header
  $attrs = new stdClass();
  $attrs->mktowsUserId = $marketoUserId;
  $attrs->requestSignature = $signature;
  $attrs->requestTimestamp = $timeStamp;
  $authHdr = new SoapHeader($marketoNameSpace, 'AuthenticationHeader', $attrs);
  $options = array("connection_timeout" => 20, "location" => $marketoSoapEndPoint);
  if ($debug) {
    $options["trace"] = true;
  }

  // Create Request for getLead
  /*
  $leadKey = array("keyType" => "EMAIL", "keyValue" => "ayasavolian@marketo.com");
  $leadKeyParams = array("leadKey" => $leadKey);
  $params = array("paramsGetLead" => $leadKeyParams);
  $soapClient = new SoapClient($marketoSoapEndPoint ."?WSDL", $options);
  try {
    $lead = $soapClient->__soapCall('getLead', $params, $options, $authHdr);
    $debug = false;
  }
  catch(Exception $ex) {
    var_dump($ex);
  }
 
  if ($debug) {
    print "RAW request:\n" .$soapClient->__getLastRequest() ."\n";
    print "RAW response:\n" .$soapClient->__getLastResponse() ."\n";
  }
 
  //print_r($lead);
  */
  // Create Request to createLead
?>