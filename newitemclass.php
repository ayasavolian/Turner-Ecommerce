<?php
class newitem
{
	//declare variables used in object
	public $quantity;
	public $orderid;
	public $id;
	public $userid;
	public $mysqli;

	//getproduct function variables

	function __construct($product_code, $quantityentered, $orderid, $userid, $mysqli)
	{
		$this->userid = $userid;
		$this->quantity = $quantityentered;
		$this->orderid = $orderid;
		$this->id = $product_code;
		$this->mysqli = $mysqli;
		//call function to gather the product chosen's information
		$this->getproduct($this->id, $this->mysqli);
	}


	//MySql query - get details of item from db using product id in products table

	function getproduct($id, $mysqli)
	{
		//query the sql database for the product chosen
    	$sql = "SELECT * FROM products WHERE id = '$id'";
	    $queryresults = mysqli_query($mysqli, $sql);
    	if(mysqli_num_rows($queryresults) > 0)
    	{
    		//cycle through the query results
        	while($skurow = mysqli_fetch_assoc($queryresults)) 
        	{
    		    $skudescription = $skurow['description'];
			    $skuimage = $skurow['image'];
			    $skuprice = $skurow['price'];
			    $sku = $skurow['sku'];
        	}
    	}
    	//call a function to add the product to the shopping cart table
    	$this->addtocart($skudescription, $skuimage, $skuprice, $sku, $mysqli);
	}

	function addtocart($skudescription, $skuimage, $skuprice, $sku, $mysqli)
	{
		//get constructor variables
		$orderid = $this->orderid;
		$quantityentered = $this->quantity;
		$id = $this->id;
		$userid = $this->userid;
		//See if the product already exists in the database

	    $existingscsql = "SELECT * FROM `cartorder` WHERE `product` = '$sku' AND `order` = '$orderid'";
	    $existingscsqlresults = mysqli_query($mysqli, $existingscsql);
	    if(mysqli_num_rows($existingscsqlresults) > 0)
	    {
	        while($existingscrow = mysqli_fetch_assoc($existingscsqlresults)) {
	            $currquant = $existingscrow['quantity'];
	            $currquant = $currquant + $quantityentered;
	            $updatesc = "UPDATE `ecommerce`.`cartorder` SET quantity = '$currquant' 
	                        WHERE `cartorder`.`product` = '$sku' AND `cartorder`.`order` = '$orderid'";
	            $updatedsc = mysqli_query($mysqli, $updatesc);
	        }
	    }

	    //if not add a new one

	    else
	    {   
	        $sql = "INSERT INTO `ecommerce`.`cartorder` (`id`, `userid`, `product`, `price`, `order`, `date`, `purchase`, `image`, `quantity`, `skuid`) 
	                VALUES (NULL, '$userid', '$sku', '$skuprice', '$orderid', CURRENT_TIMESTAMP, '0', '', '$quantityentered', '$id')";
	        $insertresults = mysqli_query($mysqli, $sql);
	    }
	    $attributevars = array("skuprice"=>$skuprice, "quantityentered"=>$quantityentered, "skuid"=>$id, "sku"=>$sku, "skudescription"=>$skudescription, "skuimage"=>$skuimage, "orderid"=>$orderid);
	    $this->addtosession($this->sessionquery($sku, $mysqli, $attributevars));
	}

	//add the product to the current session

	function sessionquery($sku, $mysqli, &$attributevars)
	{
		$orderid = $this->orderid;
		$userid = $this->userid;	
		$quantityentered = $this->quantity;
		$scsql = "SELECT * FROM `cartorder` WHERE `product` = '$sku' AND `order` = '$orderid'";
	    $scsqlresults = mysqli_query($mysqli, $scsql);

		//checking if the row exists

	    if(mysqli_num_rows($scsqlresults) > 0)
	    {
	        while($scrow = mysqli_fetch_assoc($scsqlresults)) 
	        {
		        $idofsc = $scrow['id'];
		        $purchaseofsc = $scrow['purchase'];
		        $dateofsc = $scrow['date'];
		        $quantityofsc = $scrow['quantity'];
	        }
	    }

		//search for lead in the table

	    $usersql = "SELECT uniqueid,email FROM chosen WHERE userid = '$userid'";
	    $usersqlresults = mysqli_query($mysqli, $usersql);

		//if theres not a lead already, insert them
	        
	    while($row = mysqli_fetch_assoc($usersqlresults)) 
        {
          $email = $row['email'];
        }
        $attributevars2 = array("idofsc"=>$idofsc, "purchaseofsc"=>$purchaseofsc, "dateofsc"=>$dateofsc, "quantityofsc"=>$quantityentered, "email"=>$email, "userid"=>$userid);
		$completevars = array_merge($attributevars, $attributevars2);
		$_SESSION["completevars"] = array($completevars);

		return $completevars;
	}

	function addtosession(&$completevars)
	{
        //prepare array for the session variable
        $new_product = array(array('sku'=>$completevars['sku'], 'price'=>$completevars['skuprice'], 'id'=>$completevars['skuid'], 'idofsc'=>$completevars['idofsc'], 'quantity'=>$completevars['quantityofsc']));
        if(isset($_SESSION["products"])) //if we have the session
        {
            $found = false; //set found item to false
            
            foreach ($_SESSION["products"] as $cart_itm) //loop through session array
            {
                if($cart_itm['id'] == $completevars['skuid']){ //the item exist in array
                    $cart_itm['quantity'] = $cart_itm['quantity'] + $completevars['quantityofsc'];
                    $product[] = array('sku'=>$cart_itm['sku'], 'id'=>$cart_itm['id'], 'price'=>$cart_itm['price'], 'idofsc'=>$cart_itm['idofsc'],'quantity'=>$cart_itm['quantity']);
                    $found = true;
                }else{
                    //item doesn't exist in the list, just retrive old info and prepare array for session var
                    $product[] = array('sku'=>$cart_itm['sku'], 'id'=>$cart_itm['id'], 'price'=>$cart_itm['price'], 'idofsc'=>$cart_itm['idofsc'], 'quantity'=>$cart_itm['quantity']);
                }
            }
            
            if($found == false) //we didn't find item in array
            {
                //add new user item in array
                $_SESSION["products"] = array_merge($product, $new_product);
            }else{
                //found user item in array list, and increased the quantity
                $_SESSION["products"] = $product;
            }
        }else{
            $_SESSION["products"] = $new_product;
        }
    }

    public static function removeitem($product_code, $return_url)
    {
	    foreach ($_SESSION["products"] as $cart_itm) //loop through session array var
	    {
	        if($cart_itm["id"]!=$product_code){ //item doesn't exist in the list
	            $product[] = array('sku'=>$cart_itm["sku"], 'id'=>$cart_itm["id"], 'price'=>$cart_itm["price"], 'idofsc'=>$cart_itm['idofsc'],'quantity'=>$cart_itm['quantity']);
	        }
	        
	        //create a new product list for cart
	        $_SESSION["products"] = $product;
	    }
    
	    //redirect back to original page
	    header('Location:'.$return_url);
    }
}
?>