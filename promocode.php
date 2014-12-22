<?php
	session_start();
	require 'servercall.php';
	$promocode = $_POST['promocode'];
	$return_url = base64_decode($_POST["returnurl"]);
	if($promocode == '98q387325')
	{
		if(isset($_SESSION["products"]))
        {
            foreach ($_SESSION["products"] as $cart_itm)
            {
            	if($cart_itm['sku'] == "iPhone 6 Case")
            	{
            		echo "test";
            		$cart_itm['price'] = "25";
            	}
            }
        }
	}
	header('Location:'.$return_url);
?>