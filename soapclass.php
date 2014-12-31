<!--
Date: 12/31/2014
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
		//get all of the arguments passed into the soap api for the instance of marketo
		$this->endpoint = $marketoSoapEndPoint;
		$this->userid = $marketoUserId;
		$this->secretkey = $marketoSecretKey;
		$this->namespace = $marketoNameSpace;
		$this->mysqli = $mysqli;
		//call the soap header
		$this->soapconnect($this->endpoint, $this->userid, $this->secretkey, $this->namespace);
	}


	//SOAP Header which will be referencecd into all methods through the
	//soapclientcall function

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

	/*
	EXPLANATION
	the soapclientcall method will be referenced into all methods that need to use the SOAP API
	the soapclientcall first uses the soapconnect method in order to call the SOAP Header
	and then using the arguments passed for the parameters and calltype it will go ahead and
	do the call that is being passed
	*/

	function soapclientcall($calltype, $params)
	{
		//first calling the soap header
		$soapvars = $this->soapconnect($this->endpoint, $this->userid, $this->secretkey, $this->namespace);
		//gathering the variables from the soap header necessary for the call
		$options = $soapvars['options'];
		$authHdr = $soapvars['authHdr'];
		$endpoint = $this->endpoint;
		//initiating a new soap call
		$soapClient = new SoapClient($endpoint ."?WSDL", $options);
		//passing the soap call and gathering the results to be used if needed back in the original method
		$result = $soapClient->__soapCall($calltype, $params, $options, $authHdr);
		return $result;
	}

	//All vars used in the new item class that we stored as a session variable
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

		//first gather all the variables that are necessary to push the user into Marketo
		//such as email, and userid
		$vars = $this->pushvars();
		/*
		EXPLANATION
		we will use this first method as an example for the rest that are following to push info into Marketo
		the first thing that is necessary is calling a new stdClass for each parameter that you want to pass
		in this example we'll be passing email as the key to identify the lead and then ecommuserid and userid+shoppingcart as field values

		the purpose for passing the userid+shoppingcart is to make sure we can associate any shopping cart items
		with the correct lead in the future methods
		*/
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
		/*
		after gathering all the variables we want to use in stdClasses and getting the field/value pairs, we then store
		all of these as an array in params, this will be the way we prepare all SOAP API calls.
		*/
		$params = array("paramsSyncLead" => $leadRecord);

		$result = $this->soapclientcall('syncLead', $params);
		//call the next method to push the shopping cart items chosen into marketo 
		$this->pushshoppingcart($vars);
	}

	/*
	push the shopping cart items into the shopping cart object
	similar to the pushuser method we first gather the keys to identify the lead we want to associate the
	shopping cart item with and then create the field/value pairs storing them in stdClasses and then eventually
	passing them into the params variable along with the type of object/call we want to make to the shopping cart object
	*/

	function pushshoppingcart($vars)
	{
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
        $params = array($params);
        $leads = $this->soapclientcall('syncCustomObjects', $params);
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
		//after updating them in the sql database we want to make sure we update them
		//in Marketo as well so we also call updateuser
		$this->updateuser($ordervars);
	}

	//update the user information with the first name and last name in Marketo

	function updateuser($ordervars)
	{

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
		$leads = $this->soapclientcall('syncLead', $params);
		$this->updatepurchaseofsc($ordervars);
	}

	//update the shopping cart items purchased to true

	function updatepurchaseofsc($ordervars)
	{
		$userid = $ordervars['userid'];
		//we check to make sure that the products exist for the session
		if(isset($_SESSION["products"]))
		{
		    $total = 0;
		    //we then loop through the products in the current session to add them
		    //into Marketo while at the same time updating them in the sql database
		    foreach ($_SESSION["products"] as $cart_itm)
		    {
		        $idofsc = $cart_itm['idofsc'];
		        $sqlcart = "UPDATE `cartorder` SET purchase ='1' WHERE `id` = '$idofsc'";
		        $updateresults = mysqli_query($mysqli, $sqlcart);

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
		        $params = array($params);
		        $leads = $this->soapclientcall('syncCustomObjects', $params);
		    }
		}
		$this->insertpurchase($ordervars);
	}

	/*
	EXPLANATION
	This is the last piece of the purchasing process
	We want to push the purchase into the "Purchase" object in Marketo as well
	as the "Orders" object. Specifically we want to push to the Orders object so we can
	do ROI reporting on all purchases made.  

	We first insert the purchase into Marketo after the user puts in their personal/credit card info
	and begin processing.
	*/

	function insertpurchase($ordervars)
	{
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
        $params = array($params);
      	$leads = $this->soapclientcall('syncCustomObjects', $params);
		$this->insertorder($ordervars);
	}

	/*
	Now that we've inserted the purchase into the Purchase object we want to also insert it into the Orders object.
	There are three parts to the insertorder method. We must first insert an Opportunity record (Order record) into Marketo
	then we will search for the lead associated with the opportunity, and then lastly we will create the opportunitypersonrole
	record that associates the opportunity to a lead. 
	*/

	function insertorder($ordervars)
	{
		//first call the insert opportunity method to create a new opportunity in Marketo
		$oppty = $this->insertopportunity($ordervars);
		//gather the id of the opportunity that was created so we can pass it to the opportunitypersonrole junction object
		$opttyid = $oppty->result->mObjStatusList->mObjStatus->id;
		//get the lead's id
		$lead = $this->searchlead($ordervars);
		//store the lead id so we can pass it to the opportunitypersonrole junction object
		$leadid = $lead->result->leadRecordList->leadRecord->Id;
		//create the opportunitypersonrole record and pass both ids collected
		$this->insertopportunitypersonrole($leadid, $opttyid, $ordervars);
	}

	function insertopportunity($ordervars)
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
        $params = array($params);
		$oppty = $this->soapclientcall('syncMObjects', $params);
		return $oppty;
	}

	function searchlead($ordervars)
	{
		$leadKey = array("keyType" => "EMAIL", "keyValue" => $ordervars['email']);
		$leadKeyParams = array("leadKey" => $leadKey);
		$params = array("paramsGetLead" => $leadKeyParams);
		$lead = $this->soapclientcall('getLead', $params);
		return $lead;
	}

	function insertopportunitypersonrole($leadid, $opttyid, $ordervars)
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
        $params = array($params);
		$oppty = $this->soapclientcall('syncMObjects', $params);			
	}
}
?>