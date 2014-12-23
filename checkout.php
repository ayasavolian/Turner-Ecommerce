<!--
Date: 12/22/2014
User: ayasavolian

- Page used to checkout the products added to the cart

-->
<?php
session_start();
require 'servercall.php';
//if theres a promotion used get its value
$promocode = $_GET['promocode'];
//if the promotion is one that is recognized and provided then reduce the price
//this uses dummy data and uses static values but actual promotions could be pulled
//referencing the promocode
if($promocode == '98q387325')
{
    $userid = $_SESSION["usercookieid"];
    $orderid = rand(1, 99999999999);
    $cartprice = "25";
    $cartsku = "iPhone 6 Case";
    $id = "2";
    $quantityentered = "1";
    $originalprice = "50";
    //add the product to the cart that was used for the promotion
    $insertprodsql = "INSERT INTO `ecommerce`.`cartorder` (`id`, `userid`, `product`, `price`, `order`, `date`, `purchase`, `image`, `quantity`, `skuid`) 
        VALUES (NULL, '$userid', '$cartsku', '$cartprice', '$orderid', CURRENT_TIMESTAMP, '0', '', '$quantityentered', '$id')";
    $insertprodresult = mysqli_query($mysqli, $insertprodsql);
    $existingscsql = "SELECT * FROM `cartorder` WHERE `product` = '$cartsku' AND `order` = '$orderid'";
    $existingscsqlresults = mysqli_query($mysqli, $existingscsql);
    if(mysqli_num_rows($existingscsqlresults) > 0)
    {
        //add it to the cart session
        while($existingscrow = mysqli_fetch_assoc($existingscsqlresults)) {
        $idofsc = $existingscrow['id'];
        $new_product = array(array('sku'=>$obj->sku, 'price'=>$obj->price, 'id'=>$id, 'idofsc'=>$idofsc, 'quantity'=>$quantityentered));
        $product = array(array('sku'=>$cartsku, 'price'=>$cartprice, 'id'=>$id, 'idofsc'=>$idofsc, 'quantity'=>'1'));
        $_SESSION["products"] = $product;
        }
    }
}

header('Location:'.$return_url);
//grabbing search results and querying
$orderid = $_POST["orderid"];
$userid = $_SESSION["usercookieid"];
$current_url = base64_encode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

    $usersql = "SELECT uniqueid,email FROM chosen WHERE userid = '$userid'";
    //get the details of the user so we can place it in the form
    $usersqlresults = mysqli_query($mysqli, $usersql);
    if(mysqli_num_rows($usersqlresults) > 0)
    {
        while($row = mysqli_fetch_assoc($usersqlresults)) 
        {
          $email = $row['email'];
        }
    }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script src="js/marketoscripts.js"></script>
<script type="text/javascript">
document.write(unescape("%3Cscript src='//munchkin.marketo.net/munchkin.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script>Munchkin.init('314-QVX-610');</script>
<link rel="stylesheet" type="text/css" href="css/global.css">
</head>
<body>
    <?php
        include 'topcontainer.php';
    ?>
    <div class = "bodycontainer">
        <div class = "maincontent">
            <div class = "promocontainer">
                <div class = "searched">
                    Checkout: <br><br> Step 1: Products Confirmation
                </div>
                <div class = "cartheader">
                    <div class = "cartdetails">
                        PRODUCT DETAILS
                    </div>
                    <div class = "cartquantity">
                        QUANTITY
                    </div>
                    <div class = "carttotalprice">
                        PRICE
                    </div>
                </div>
                <?php
                if(isset($_SESSION["products"]))
                {
                    $total = 0;
                    echo '<div class = "carttotalsize">';
                    //query through the current cart session
                    foreach ($_SESSION["products"] as $cart_itm)
                    {
                        $cartid = $cart_itm['idofsc'];
                        //query the products table for the product from the cart in order to grab some
                        //of its basic information such as price product and quantity
                        //first query the cart table in the sql database for the product currently looped for
                        $sql = "SELECT price,product,quantity FROM `cartorder` WHERE `id` = '$cartid'";
                        $orders = mysqli_query($mysqli, $sql);
                        if (mysqli_num_rows($orders) > 0) {
                        // output data of each row
                            while($row = mysqli_fetch_assoc($orders)) {
                                $prod = $row['product'];
                                //query for the product in the product table so we can use the image and description
                                //of the product
                                $sqlprod = "SELECT * FROM `products` WHERE `sku` = '$prod'";
                                $prodresult = mysqli_query($mysqli, $sqlprod);
                                if (mysqli_num_rows($prodresult) > 0) {
                                    while($prodrow = mysqli_fetch_assoc($prodresult)) {
                                        $prodimg = $prodrow['image'];
                                        $proddescript = $prodrow['description'];
                                    }
                                }
                                else{
                                }
                                ?>
                                <!--
                                show the different products chosen from the cart
                                such as the image, description, quantity, etc
                                -->
                                <div class = "oneresultscontainer">
                                    <div class = "checkoutheadercontainer">
                                        <div class = "resultsimage">
                                                <?php
                                                echo '<img src = "'.$prodimg.'"/>';
                                                ?>
                                        </div>
                                        <div class = "checkoutcontainer">
                                            <div class = "checkoutheader">
                                                    <?php
                                                    echo $row['product'];
                                                    ?>
                                            </div>
                                            <div class = "checkoutdescription">
                                                    <?php
                                                    echo $proddescript;
                                                    ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class = "cartquantitycontainer">
                                        <div class = "cartquantitydisplay">
                                            <?php
                                            echo $cart_itm['quantity'];
                                            ?>
                                        </div>
                                    </div>
                                    <div class = "cartpricecontainer">
                                        <div class = "cartpricedisplay">
                                            <?php
                                            //if the promo exists we want to show the original price of the product
                                            if($promocode == '98q387325')
                                            {
                                                echo '<div style = "color:#ff0000; text-decoration: line-through; margin-top:-55px;margin-bottom:14px;">$50</div>';
                                            }
                                            echo "$";
                                            echo $row['price'];
                                            $total = $total + $cart_itm['price']*$cart_itm['quantity'];
                                            ?>
                                        </div>
                                    </div>
                                    <!--
                                    allow the user to be able to delete the product from the cart
                                    -->
                                    <div id = "closeboxcheckout">
                                        <?php echo '<a href="updatecart.php?removep='.$cart_itm['id'].'&return_url='.$current_url.'" class = "checkoutremove">X</a>'; ?>
                                    </div>
                                </div>
                            <?php
                            }
                        } 
                        else {
                            echo "0 results";
                        }
                    }
                }
                else{}
                ?>
                <div class = "totalofpurchasecontainer">
                    <form class = "checkoutformcontainer" method="post" action="promocode.php">
                    <div class = "promocodecontainer">
                        <input class = "promocodeform" type="text" name="promocode" value = "<?php echo $promocode ?>" placeholder="Promotion Code" >
                        <?php
                        echo '<input type="hidden" name="returnurl" value="'.$current_url.'">';
                        ?>
                        <button type = "submit" class = "promocodebutton">
                            Submit
                        </button>
                    </div>
                    </form>
                    <div class = "totalofpurchase">
                        <font style = "color: #c2c2c2;font-size: 24px"> TOTAL: </font><br><br>
                        <?php echo "$"; echo $total; ?>
                    </div>
                </div>
                <!--
                Show the form to fill out the order and all the user to choose a pre-used card
                -->
                <div class = "searched">
                    Step 2: Order Information
                </div>
                <form class = "checkoutformcontainer" method="post" action="confirmedshipment.php">
                    <div class = "resultsbuttoncontainer">
                        <input class = "checkoutform" type="text" name="firstname" placeholder="first name" required>
                        <input class = "checkoutform" type="text" name="lastname" placeholder="last name" required>
                        <input class = "checkoutform" value =<?php echo "$email";?> type="text" name="email" placeholder="email" required>
                    </div>
                    <div class = "cardimage">
                        <img style = "height: 100px"  onclick="blackcardclicked()" id = "blackcard" src = "images/blackcard.png">
                    </div>
                    <div class = "resultsbuttoncontainer">
                        <input class = "checkoutform" id = "creditform" type="text" name="creditcard" placeholder="card number">
                        <input class = "checkoutform" id = "creditnum" type="text" name="creditnum" placeholder="CVV">
                        <input class = "checkoutform" id = "expirationcard" type="text" name="expirationcard" placeholder="expiration">
                        <?php
                        echo '<input type="hidden" name="orderid" value="'.$orderid.'">';
                        echo '<input type="hidden" name="purchtot" value="'.$total.'">';
                        ?>
                        <button type = "submit" class = "finalcheckoutbutton">
                            Checkout
                        </button>
                    </div>
                </form>
                <div class = "formdescription">
                    <font style = "color: #0064d2; font-size: 24px;"> Trusted Deliverability </font>
                    <img style = "width: 75px;" src = "images/lock.png"><br><br><br>
                    SSL is a secure protocol developed for sending information securely over the Internet. 
                    Many websites use SSL for secure areas of their sites, such as user account pages and online checkout. 
                    Usually, when you are asked to "log in" on a website, the resulting page is secured by SSL.<br><br>
                    SSL encrypts the data being transmitted so that a third party cannot "eavesdrop" on the transmission and 
                    view the data being transmitted. Only the user's computer and the secure server are able to recognize the data. 
                    SSL keeps your name, address, and credit card information between you and merchant to which you are providing it. 
                    Without this kind of encryption, online shopping would be far too insecure to be practical. When you visit a Web 
                    address starting with "https," the "s" after the "http" indicates the website is secure. These websites often use SSL 
                    certificates to verify their authenticity.
                </div>
            </div>
        </div>
    <?php
        include 'footer.php';
    ?>
    </div>
</body>
</html>