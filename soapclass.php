<!--
Date: 12/22/2014
User: ayasavolian

- This action page is used to add the product chosen to Marketo through the SOAP API

-->
<?php
class soap
{
	//declare variables used in object
	public $marketoSoapEndPoint;
	public $MarketoUserId;
	public $MarketoSecretKey;
	public $MarketoNameSpace;
	public $mysqli;

	//getproduct function variables

	function __construct($marketoSoapEndPoint, $marketoUserId, $marketoSecretKey, $marketoNameSpace, $mysqli)
	{
		$this->endpoint = $marketoSoapEndPoint;
		$this->userid = $marketoUserId;
		$this->secretkey = $marketoSecretKey;
		$this->namespace = $marketoNameSpace;
		$this->mysqli = $mysqli;
		//call function to gather the product chosen's information
		$this->soapconnect($this->endpoint, $this->userid, $this->secretkey, $this->namespace);
	}


	//SOAP Header which will be referencecd into all methods

	function soapconnect($endpoint, $userid, $secretkey, $namespace)
	{
		$dtzObj = new DateTimeZone("America/Los_Angeles");
		$dtObj  = new DateTime('now', $dtzObj);
		$timeStamp = $dtObj->format(DATE_W3C);
		$encryptString = $timeStamp . $userid;
		$signature = hash_hmac('sha1', $encryptString, $secretkey);

		// Create SOAP Header

		$attrs = new stdClass();
		$attrs->mktowsUserId = $userid;
		$attrs->requestSignature = $signature;
		$attrs->requestTimestamp = $timeStamp;
		$authHdr = new SoapHeader($namespace, 'AuthenticationHeader', $attrs);
		$options = array("connection_timeout" => 20, "location" => $endpoint);
		if ($debug) {
			$options["trace"] = true;
		}
		$soapvars = array("options"=>$options, "authHdr"=>$authHdr);
		return $soapvars;
	}
	//All vars used in the new item class
	//These will be referenced into the methods below
	function pushvars()
	{
		foreach($_SESSION["completevars"] as $varvalues)
		{
			$vars = array("idofsc"=>$varvalues['idofsc'], "purchaseofsc"=>$varvalues['purchaseofsc'], "dateofsc"=>$varvalues['dateofsc'], 
						"quantityofsc"=>$varvalues['quantityofsc'], "email"=>$varvalues['email'], "userid"=>$varvalues['userid'], "skuprice"=>$varvalues['skuprice'], 
						"quantityentered"=>$varvalues['quantityentered'], "skuid"=>$varvalues['skuid'], "sku"=>$varvalues['sku'], 
						"skudescription"=>$varvalues['skudescription'], "skuimage"=>$varvalues['skuimage'], "orderid"=>$varvalues['orderid']);
		}
		return $vars;
	}

	//updates the user with the correct shoppingcart Id

	function pushuser($vars)
	{
		//use the soap header
		$soapvars = $this->soapconnect($this->endpoint, $this->userid, $this->secretkey, $this->namespace);
		$vars = $this->pushvars();

		$options = $soapvars['options'];
		$authHdr = $soapvars['authHdr'];

		$endpoint = $this->endpoint;
		$leadKey = new stdClass();
		$leadKey->Email = $vars['email'];

		// Lead attributes to update
		$attr1 = new stdClass();
		$attr1->attrName  = "ecommuserid";
		$attr1->attrValue = $vars['userid'];
		$attr2= new stdClass();
		//this will use the munchkin cookie id to connect the lead to the cart products
		$attr2->attrName  = "userid+shoppingcart";
		$attr2->attrValue = $vars['userid'];
		$attrArray = array($attr1, $attr2);
		$attrList = new stdClass();
		$attrList->attribute = $attrArray;
		$leadKey->leadAttributeList = $attrList;

		$leadRecord = new stdClass();
		$leadRecord->leadRecord = $leadKey;
		$leadRecord->returnLead = false;
		$params = array("paramsSyncLead" => $leadRecord);

		$soapClient = new SoapClient($endpoint ."?WSDL", $options);
		$result = $soapClient->__soapCall('syncLead', $params, $options, $authHdr);
		//call the next method to push the shopping cart items chosen into marketo 
		$this->pushshoppingcart($vars, $options, $authHdr);
	}

	//push the shopping cart items into the shopping cart object

	function pushshoppingcart($vars, $options, $authHdr)
	{
		$endpoint = $this->endpoint;
		$keyAttrib1 = new stdClass();
        $keyAttrib1->attrName = "userid+shoppingcart";
        $keyAttrib1->attrValue = $vars['userid'];
        $keyAttrib2 = new stdClass();
        $keyAttrib2->attrName = "orderid+productid";
        $keyAttrib2->attrValue = $vars['idofsc'];
        $keyAttrList = array($keyAttrib1, $keyAttrib2);
        $keyList = new stdClass();
        $keyList->attribute = $keyAttrList;
        $attr1 = new stdClass();
        $attr1->attrName = "productname";
        $attr1->attrValue = $vars['sku'];
        $attr2 = new stdClass();
        $attr2->attrName = "unitprice";
        $attr2->attrValue = $vars['skuprice'];
        $attr3 = new stdClass();
        $attr3->attrName = "productdescription";
        $attr3->attrValue = $vars['skudescription'];
        $attr4 = new stdClass();
        $attr4->attrName = "purchased";
        $attr4->attrValue = "false";
        $attr5 = new stdClass();
        $attr5->attrName = "date";
        $attr5->attrValue = $vars['dateofsc'];
        $attr6 = new stdClass();
        $attr6->attrName = "productid";
        $attr6->attrValue = $vars['skuid'];
        $attr7 = new stdClass();
        $attr7->attrName = "quantity";
        $attr7->attrValue = $vars['quantityentered'];
        $attr8 = new stdClass();
        $attr8->attrName = "productimage";
        $attr8->attrValue = $vars['skuimage'];
        $attr9 = new stdClass();
        $attr9->attrName = "orderid";
        $attr9->attrValue = $vars['orderid'];
        $attrListArray = array($attr1, $attr2, $attr3, $attr4, $attr5, $attr6, $attr7, $attr8, $attr9);
        $attrList = new stdClass();
        $attrList->attribute = $attrListArray;
        $custObj = new stdClass();
        $custObj->customObjKeyList = $keyList;
        $custObj->customObjAttributeList = $attrList;
        $custObjList = new stdClass();
        $custObjList->customObj = array($custObj);
        $params = new stdClass();
        $params->operation = 'UPSERT';
        $params->objTypeName = 'shoppingcart';
        $params->customObjList = $custObjList;
        $soapClient = new SoapClient($endpoint ."?WSDL", $options);
        try {
          $leads = $soapClient->__soapCall('syncCustomObjects', array($params), $options, $authHdr);
        }
        catch(Exception $ex) {
          var_dump($ex);
        }
	}

	//update chosen user with their first name and last name in the sql database

	function updatechosen($ordervars)
	{
		$mysqli = $this->mysqli;
		$firstname = $ordervars['firstname'];
		$lastname = $ordervars['lastname'];
		$email = $ordervars['email'];
		$userid = $ordervars['userid'];

		$sqluser = "UPDATE `ecommerce`.`chosen` SET `firstname` = '$firstname', lastname='$lastname', 
            email='$email' WHERE `chosen`.`userid` = '$userid'";

		$insertresults = mysqli_query($mysqli, $sqluser);
		$this->updateuser($ordervars);
	}

	//update the user information with the first name and last name

	function updateuser($ordervars)
	{
		$soapvars = $this->soapconnect($this->endpoint, $this->userid, $this->secretkey, $this->namespace);
		$options = $soapvars['options'];
		$authHdr = $soapvars['authHdr'];
		$endpoint = $this->endpoint;

		//REQUEST TO UPDATE USER IN MARKETO

		$leadKey = new stdClass();
		$leadKey->Email = $ordervars['email'];

		// Lead attributes to update
		$attr1 = new stdClass();
		$attr1->attrName  = "FirstName";
		$attr1->attrValue = $ordervars['firstname'];
		$attr2= new stdClass();
		$attr2->attrName  = "LastName";
		$attr2->attrValue = $ordervars['lastname'];
		$attr3= new stdClass();
		$attr3->attrName  = "email+purchase";
		$attr3->attrValue = $ordervars['email'];
		$attrArray = array($attr1, $attr2, $attr3);
		$attrList = new stdClass();
		$attrList->attribute = $attrArray;
		$leadKey->leadAttributeList = $attrList;

		$leadRecord = new stdClass();
		$leadRecord->leadRecord = $leadKey;
		$leadRecord->returnLead = false;
		$params = array("paramsSyncLead" => $leadRecord);
		$soapClient = new SoapClient($endpoint ."?WSDL", $options);
		$result = $soapClient->__soapCall('syncLead', $params, $options, $authHdr);
		$this->updatepurchaseofsc($ordervars, $options, $authHdr, $endpoint);
	}

	//update the shopping cart items purchased to true

	function updatepurchaseofsc($ordervars, $options, $authHdr, $endpoint)
	{
		$userid = $ordervars['userid'];

		if(isset($_SESSION["products"]))
		{
		    $total = 0;
		    foreach ($_SESSION["products"] as $cart_itm)
		    {
		        $idofsc = $cart_itm['idofsc'];
		        $sqlcart = "UPDATE `cartorder` SET purchase ='1' WHERE `id` = '$idofsc'";
		        $updateresults = mysqli_query($mysqli, $sqlcart);

		        //REQUEST TO PUSH SHOPPING CART INTO MARKETO
		        $keyAttrib1 = new stdClass();
		        $keyAttrib1->attrName = "userid+shoppingcart";
		        $keyAttrib1->attrValue = "$userid";
		        $keyAttrib2 = new stdClass();
		        $keyAttrib2->attrName = "orderid+productid";
		        $keyAttrib2->attrValue = "$idofsc";
		        $keyAttrList = array($keyAttrib1, $keyAttrib2);
		        $keyList = new stdClass();
		        $keyList->attribute = $keyAttrList;
		        $attr1 = new stdClass();
		        $attr1->attrName = "purchased";
		        $attr1->attrValue = "true";
		        $attrListArray = array($attr1);
		        $attrList = new stdClass();
		        $attrList->attribute = $attrListArray;
		        $custObj = new stdClass();
		        $custObj->customObjKeyList = $keyList;
		        $custObj->customObjAttributeList = $attrList;
		        $custObjList = new stdClass();
		        $custObjList->customObj = array($custObj);
		        $params = new stdClass();
		        $params->operation = 'UPSERT';
		        $params->objTypeName = 'shoppingcart';
		        $params->customObjList = $custObjList;
		        $soapClient = new SoapClient($endpoint ."?WSDL", $options);
		        try {
		          $leads = $soapClient->__soapCall('syncCustomObjects', array($params), $options, $authHdr);
		        }
		        catch(Exception $ex) {
		          var_dump($ex);
		        }
		    }
		}
		$this->insertpurchase($ordervars, $options, $authHdr, $endpoint);
	}

	//insert the purchase totals into marketo

	function insertpurchase($ordervars, $options, $authHdr, $endpoint)
	{
		//REQUEST TO PUSH PURCHASE
        $keyAttrib1 = new stdClass();
        $keyAttrib1->attrName = "email+purchase";
        $keyAttrib1->attrValue = $ordervars['email'];
        $keyAttrib2 = new stdClass();
        $keyAttrib2->attrName = "purchaseid";
        $keyAttrib2->attrValue = $ordervars['orderid'];
        $keyAttrList = array($keyAttrib1, $keyAttrib2);
        $keyList = new stdClass();
        $keyList->attribute = $keyAttrList;
        $attr1 = new stdClass();
        $attr1->attrName = "subtotal";
        $attr1->attrValue = $ordervars['purchtot'];
        $attrListArray = array($attr1);
        $attrList = new stdClass();
        $attrList->attribute = $attrListArray;
        $custObj = new stdClass();
        $custObj->customObjKeyList = $keyList;
        $custObj->customObjAttributeList = $attrList;
        $custObjList = new stdClass();
        $custObjList->customObj = array($custObj);
        $params = new stdClass();
        $params->operation = 'UPSERT';
        $params->objTypeName = 'purchases';
        $params->customObjList = $custObjList;
        $soapClient = new SoapClient($endpoint ."?WSDL", $options);
        try {
          $leads = $soapClient->__soapCall('syncCustomObjects', array($params), $options, $authHdr);
        }
        catch(Exception $ex) {
          var_dump($ex);
        }
		$this->insertorder($ordervars, $options, $authHdr, $endpoint);
	}

	function insertorder($ordervars, $options, $authHdr, $endpoint)
	{
		$oppty = $this->insertopportunity($ordervars, $options, $authHdr, $endpoint);
		$opttyid = $oppty->result->mObjStatusList->mObjStatus->id;
		$lead = $this->searchlead($ordervars, $options, $authHdr, $endpoint);
		$leadid = $lead->result->leadRecordList->leadRecord->Id;
		$this->insertopportunitypersonrole($leadid, $opttyid, $ordervars, $options, $authHdr, $endpoint);
	}

	function insertopportunity($ordervars, $options, $authHdr, $endpoint)
	{
		$mObj = new stdClass();
		$mObj->type = 'Opportunity';
		$orderid = $ordervars['orderid'];
		$setorderid = strval($orderid);
		$attrib1 = new stdClass();
		$attrib1->name="Name";
		$attrib1->value= $setorderid;
		$attrib2 = new stdClass();
		$attrib2->name="Amount";
		$attrib2->value= $ordervars['purchtot'];

		$attribs = array($attrib1, $attrib2);

		$attribList = new stdClass();
		$attribList->attrib = $attribs;

		$mObjAssociationList = new stdClass();
		$mObjAssociationList->mObjAssociation = $mObjAssociation;

		$mObj->mObjAssociationList = $mObjAssociationList;
		$mObj->attribList = $attribList;
		$params = new stdClass();
		$params->mObjectList = array($mObj);
		$params->operation="INSERT";
			
		$soapClient = new SoapClient($endpoint ."?WSDL", $options);
		try {
		  $oppty = $soapClient->__soapCall('syncMObjects', array($params), $options, $authHdr);
		}
		catch(Exception $ex) {
		  var_dump($ex);
		}
		return $oppty;
	}

	function searchlead($ordervars, $options, $authHdr, $endpoint)
	{
		$leadKey = array("keyType" => "EMAIL", "keyValue" => $ordervars['email']);
		$leadKeyParams = array("leadKey" => $leadKey);
		$params = array("paramsGetLead" => $leadKeyParams);
		$soapClient = new SoapClient($endpoint ."?WSDL", $options);
		try {
		$lead = $soapClient->__soapCall('getLead', $params, $options, $authHdr);
		}
		catch(Exception $ex) {
		var_dump($ex);
		}
		return $lead;
	}

	function insertopportunitypersonrole($leadid, $opttyid, $ordervars, $options, $authHdr, $endpoint)
	{
		$params = new stdClass();

		$mObj = new stdClass();
		$mObj->type = 'OpportunityPersonRole';

		$attrib1 = new stdClass();
		$attrib1->name="OpportunityId";
		$attrib1->value= $opttyid;
			
		$attrib2 = new stdClass();
		$attrib2->name="PersonId";
		$attrib2->value= $leadid;
			
		$attrib3 = new stdClass();
		$attrib3->name="Role";
		$attrib3->value="Influencer/Champion";

		$attribs = array($attrib1, $attrib2, $attrib3);

		$attribList = new stdClass();
		$attribList->attrib = $attribs;

		$mObj->attribList = $attribList;
		$params->mObjectList = array($mObj);

		$params->operation="INSERT";
			
		$soapClient = new SoapClient($endpoint ."?WSDL", $options);
		try {
		  $leads = $soapClient->__soapCall('syncMObjects', array($params), $options, $authHdr);
		}
		catch(Exception $ex) {
		  var_dump($ex);
		}

		if ($debug) {
		  print "RAW request:\n" .$soapClient->__getLastRequest() ."\n";
		  print "RAW response:\n" .$soapClient->__getLastResponse() ."\n";
		}
	}
}
?>