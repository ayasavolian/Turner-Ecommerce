<?php
session_start(); //start session
require 'servercall.php';
include 'newitemclass.php';
include 'soapclass.php';
//empty cart by destroying current session
if(isset($_GET["emptycart"]) && $_GET["emptycart"]==1)
{
    $return_url = base64_decode($_GET["return_url"]); //return url
    session_destroy();
    header('Location:'.$return_url);
}

//add item in shopping cart
if(isset($_POST["type"]) && $_POST["type"]=='add')
{
    $id = $_POST["product_code"];
    $searchpush = $_POST["search"]; //product code
    $quantityentered = $_POST["quantityentered"];
    $orderid = $_POST["orderid"];
    $orderid = (string)$orderid;
    $_SESSION["searchpush"] = $searchpush;
    $_SESSION["orderidpush"] = $orderid;
    $userid = $_SESSION["usercookieid"];
    $return_url = base64_decode($_POST["return_url"]); //return url
    $item = new newitem($id, $quantityentered, $orderid, $userid, $mysqli);

//this is for the SOAP API insert

//HEADER FOR THE SOAP API 
      //$orderidshoppingcartid = "$orderid" + "$idofsc";

      //Values for the SOAP API
      //Can get these values from within Marketo Admin
      $debug = true;
     
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
if(isset($_GET["removep"]) && isset($_GET["return_url"]) && isset($_SESSION["products"]))
{
    $product_code   = $_GET["removep"]; //get the product code to remove
    $return_url     = base64_decode($_GET["return_url"]); //get return url

    newitem::removeitem($product_code, $return_url);
    
}