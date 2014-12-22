<?php
session_start();
require 'servercall.php';
$promocode = $_GET['promocode'];

if($promocode == '98q387325')
{
    $userid = $_SESSION["usercookieid"];
    $orderid = rand(1, 99999999999);
    $cartprice = "25";
    $cartsku = "iPhone 6 Case";
    $id = "2";
    $quantityentered = "1";
    $originalprice = "50";
    $insertprodsql = "INSERT INTO `ecommerce`.`cartorder` (`id`, `userid`, `product`, `price`, `order`, `date`, `purchase`, `image`, `quantity`, `skuid`) 
        VALUES (NULL, '$userid', '$cartsku', '$cartprice', '$orderid', CURRENT_TIMESTAMP, '0', '', '$quantityentered', '$id')";
    $insertprodresult = mysqli_query($mysqli, $insertprodsql);
    $existingscsql = "SELECT * FROM `cartorder` WHERE `product` = '$cartsku' AND `order` = '$orderid'";
    $existingscsqlresults = mysqli_query($mysqli, $existingscsql);
    if(mysqli_num_rows($existingscsqlresults) > 0)
    {
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
/*
//search for lead in the table
    $usersql = "SELECT uniqueid,email FROM chosen WHERE userid = '$userid'";
    $usersqlresults = mysqli_query($mysqli, $usersql);
    if(mysqli_num_rows($usersqlresults) > 0)
    {
        while($row = mysqli_fetch_assoc($usersqlresults)) 
        {
          $email = $row['email'];
        }
    }
*/

    $usersql = "SELECT uniqueid,email FROM chosen WHERE userid = '$userid'";
    $usersqlresults = mysqli_query($mysqli, $usersql);
//if theres not a lead already, insert them
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
                    foreach ($_SESSION["products"] as $cart_itm)
                    {
                        $cartid = $cart_itm['idofsc'];
                        $sql = "SELECT price,product,quantity FROM `cartorder` WHERE `id` = '$cartid'";
                        $orders = mysqli_query($mysqli, $sql);
                        if (mysqli_num_rows($orders) > 0) {
                        // output data of each row
                            while($row = mysqli_fetch_assoc($orders)) {
                                $prod = $row['product'];
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
                                <div class = "oneresultscontainer">
                                    <div class = "checkoutheadercontainer">
                                        <div class = "resultsimage">
                                                <?php
                                                echo '<img src = "'.$prodimg.'"/>';
                                                //echo '<img src="data:image/jpeg;base64,'.base64_encode( $prodimg ).'"/>';
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
                    SSL is a secure protocol developed for sending information securely over the Internet. Many websites use SSL for secure areas of their sites, such as user account pages and online checkout. Usually, when you are asked to "log in" on a website, the resulting page is secured by SSL.<br><br>SSL encrypts the data being transmitted so that a third party cannot "eavesdrop" on the transmission and view the data being transmitted. Only the user's computer and the secure server are able to recognize the data. SSL keeps your name, address, and credit card information between you and merchant to which you are providing it. Without this kind of encryption, online shopping would be far too insecure to be practical. When you visit a Web address starting with "https," the "s" after the "http" indicates the website is secure. These websites often use SSL certificates to verify their authenticity.
                </div>
            </div>
        </div>
    <?php
        include 'footer.php';
    ?>
    </div>
</body>
</html>