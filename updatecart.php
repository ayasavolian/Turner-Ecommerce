<!--
Date: 12/22/2014
User: ayasavolian

- This action page is used to add the product chosen to the cart, sql database, and to Marketo through the SOAP API

-->
<?php
session_start(); //start session
require 'servercall.php';
include 'newitemclass.php';
include 'soapclass.php';

//add item in shopping cart
if(isset($_POST["type"]) && $_POST["type"]=='add')
{
    //grab the hidden variables passed to the updatecart page
    $id = $_POST["product_code"];
    $searchpush = $_POST["search"]; 
    $quantityentered = $_POST["quantityentered"];
    $orderid = $_POST["orderid"];
    $orderid = (string)$orderid;
    //take the search parameters and store them as a session variable so we can grab them
    //again on the searchresults page when we return back to it
    $_SESSION["searchpush"] = $searchpush;
    $_SESSION["orderidpush"] = $orderid;
    //Get the sessid as the userid
    $userid = $_SESSION["usercookieid"];
    //get the url of the searchresults page
    $return_url = base64_decode($_POST["return_url"]);
    //create an instance of the newitem class and pass variables from the POST
    //as well as the mysqli criteria from our servercall.php referenced page
    $item = new newitem($id, $quantityentered, $orderid, $userid, $mysqli);

    //this is for the SOAP API insert
    $debug = true;

    //Values for the SOAP API
    //Can get these values from within Marketo Admin
   
    $marketoSoapEndPoint     = "*********"; 
    $marketoUserId           = "*********"; 
    $marketoSecretKey        = "*********"; 
    $marketoNameSpace        = "*********"; 
   
    $soapcall = new soap($marketoSoapEndPoint, $marketoUserId, $marketoSecretKey, $marketoNameSpace, $objecttype);
    $soapcall->pushuser();
    // Create Signature

    header('Location:'.$return_url);
}

//remove item from shopping cart
//called if the user chooses to remove the product from the shopping cart
if(isset($_GET["removep"]) && isset($_GET["return_url"]) && isset($_SESSION["products"]))
{
    //get the product code to remove
    $product_code   = $_GET["removep"];
    //get return url
    $return_url     = base64_decode($_GET["return_url"]);
    //call the static method removeitem from the instance
    newitem::removeitem($product_code, $return_url);
    
}