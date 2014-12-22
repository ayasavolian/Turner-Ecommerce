<?php
session_start();

require 'servercall.php';
require 'soapclass.php';
//grabbing search results and querying
$orderid = $_SESSION["orderid"];
$firstname = $_POST["firstname"];
$lastname = $_POST["lastname"];
$email = $_POST["email"];
$purchtot = $_POST["purchtot"];
$userid = $_SESSION["usercookieid"];

$ordervars = array("firstname"=>$firstname, "lastname"=>$lastname, "purchtot"=>$purchtot, "userid"=>$userid, "email"=>$email, "orderid"=>$orderid);

$current_url = base64_encode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

$debug = true;

$marketoSoapEndPoint     = "https://314-QVX-610.mktoapi.com/soap/mktows/2_7"; 
$marketoUserId           = "mktodemoaccount2271_953382685460FF2A57DAE8";
$marketoSecretKey        = "699028073596399455446600FFFF22BC5577EE3BCC64";
$marketoNameSpace        = "http://www.marketo.com/mktows/";

$shipsoap = new soap($marketoSoapEndPoint, $marketoUserId, $marketoSecretKey, $marketoNameSpace, $mysqli);
$shipsoap->updatechosen($ordervars);

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
                <div class = "confirmwords">
                    Order Complete! Congratulations on your items. They are displayed below. An email confirmation will be sent to you shortly.
                </div>
                <div class = "confirmcheck">
                    <img style = "height: 200px"src = "images/check.png">
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
                                            <div class = "resultsheader">
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
                                            echo "$";
                                            echo $row['price'];
                                            $total = $total + $row['price']*$row['quantity'];
                                            ?>
                                        </div>
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
            </div>
        </div>
    <?php
        //this will destroy the session after the return purchase...needs to check for the return purchase first
        setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time() -42000);
        $_COOKIE["PHPSESSID"] = null;
        unset($_COOKIE["PHPSESSID"] );
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        include 'footer.php';
    ?>
    </div>
</body>
</html>