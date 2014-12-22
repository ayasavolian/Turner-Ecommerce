<?php
session_start();
setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+1000); 
require 'servercall.php';
//grabbing search results and querying
$search = $_POST["site-search"];
$orderid = $_SESSION["orderid"];
$userid = $_SESSION["usercookieid"];
$email = $_SESSION["email"];
$searchpush = $search;
$sessprodexists = 0;
$current_url = base64_encode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
if($search == null)
{
    $searchpushed = $_SESSION["searchpush"];
    //$orderid = $_SESSION["orderidpush"];
    $sql = "SELECT family,sku,image,price,id,description FROM products WHERE family or sku LIKE '%$searchpushed%' ";
    $search = $searchpushed;
}
else{
    $sql = "SELECT family,sku,image,price,id,description FROM products WHERE family or sku LIKE '%$search%'";
}
$result = mysqli_query($mysqli, $sql);
$userid = rand(1, 99999999999);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script src="js/marketoscripts.js"></script>
<script type="text/javascript">
var email = <?php echo json_encode("$email"); ?>;
emailtest(email);
//passSession("<?php echo $email; ?>");
</script>
<script type="text/javascript">
document.write(unescape("%3Cscript src='//munchkin.marketo.net/munchkin.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script>Munchkin.init('314-QVX-610');</script>
<link rel="stylesheet" type="text/css" href="css/global.css">
</head>
<body>
    <div id = "nyanlightbox">
        <div id = "closeboxcontainer">
            <div id = "closebox" onclick="nyanlightboxoff()">
                X
            </div>
        </div>
        <div id = "nyancatcontainer">
            <iframe width="700" height="500" src="//www.youtube.com/embed/QH2-TGUlwu4" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
    <div id = "lightboxbackground">
    </div> 
    <?php
        include 'topcontainer.php';
    ?>
    <div class = "bodycontainer">
        <div class = "maincontent">
            <div class = "promocontainer">
                <div class = "searched">
                    <?php
                        echo "your search: '$search'";
                    ?>
                </div>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                        // output data of each row
                        while($row = mysqli_fetch_assoc($result)) {
                            if(isset($_SESSION["products"]))
                            {
                                $sessprodexists = 0;
                                foreach ($_SESSION["products"] as $cart_itm)
                                {
                                    if($cart_itm['sku'] == $row['sku'])
                                    {
                                        $sessprodexists = 1;
                                    }
                                }
                            }
                        ?>
                <div class = "oneresultscontainer">
                    <div class = "resultsheadercontainer">
                        <div class = "resultsimage">
                                <?php
                                echo '<img src="'.$row['image'].'"/>';
                                ?>
                        </div>
                        <div class = "resultscontainer">
                            <div class = "resultsheader">
                                    <?php
                                    echo $row['sku'];
                                    ?>
                            </div>
                            <div class = "resultswords">
                                    <?php
                                    echo $row['family']; ?> <br> <?php
                                    echo "$";
                                    echo $row['price']; ?>
                            </div>
                            <div class = "resultsdescription">
                                    <?php
                                    echo $row['description'];
                                    ?>
                            </div>
                        </div>
                    </div>
                    <form class = "resultspagebuttoncontainer" method="post" action="updatecart.php">
                    <div class = "resultspagebuttoncontainer">
                        <input class = "quantityenter" value = "1" id = "searched" type="text" name="quantityentered">
                        <?php
                        if($sessprodexists)
                        {
                            echo '<button type = "submit" class = "resultsbuttonchosen">Success!</button>';
                        }
                        else{
                        ?>
                        <button type = "submit" class = "resultsbutton">
                            Add to Cart
                        </button>
                        <?php
                        }
                        echo '<input type="hidden" name="product_code" value="'.$row['id'].'" />';
                        echo '<input type="hidden" name="orderid" value="'.$orderid.'" />';
                        echo '<input type="hidden" name="type" value="add" />';
                        echo '<input type="hidden" name="search" value="'.$search.'" />';
                        echo '<input type="hidden" name="return_url" value="'.$current_url.'" />';
                        //echo '<input type="hidden" name="userid" value="'.$userid.'" />';

                        ?>
                    </div>
                    </form>
                </div>
                        <?php
                        }
                        } else {
                            echo "0 results";
                        }
                        ?>
            </div>
            <div class = "promocontainer">
                <div class = "promoheadercontainer">
                    <div class = "relatedwords">
                        Most Relevant Products for You
                    </div>
                    <img id = "relatedprods" src = "images/relateditems.png">
                </div>
            </div>
        </div>
    <?php
        include 'footer.php';
    ?>
    </div>
</body>
</html>
?>