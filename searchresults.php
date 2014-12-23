<!--
Date: 12/22/2014
User: ayasavolian

- The search results page 
- will display the search results based on the topcontainer search query

-->
<?php
session_start();
setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+1000); 
require 'servercall.php';
//grabbing search results and querying
$search = $_POST["site-search"];
//set the session variables equal to new variables
$orderid = $_SESSION["orderid"];
$userid = $_SESSION["usercookieid"];
$email = $_SESSION["email"];
$searchpush = $search;
$sessprodexists = 0;
$current_url = base64_encode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//if returning from the updatecart screen this will make sure that search query is kept by storing the search
//string as a session variable "searchpush"
if($search == null)
{
    $searchpushed = $_SESSION["searchpush"];
    $sql = "SELECT family,sku,image,price,id,description FROM products WHERE family or sku LIKE '%$searchpushed%' ";
    $search = $searchpushed;
}
//if a product hasnt been selected yet and the updatecart page hasnt been viewed then it will just use the search
//string that was pulled from the home page
else{
    $sql = "SELECT family,sku,image,price,id,description FROM products WHERE family or sku LIKE '%$search%'";
}
$result = mysqli_query($mysqli, $sql);
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
                <!--
                This will be the output of the search query
                It will take the query made above that was stored in the variable "$result" and
                loop through it to show all of the products that have part of the query string in it
                -->
                <div class = "searched">
                    <?php
                        echo "your search: '$search'";
                    ?>
                </div>
                        <?php
                        //if the query results is greater than 0, then show the results
                        if (mysqli_num_rows($result) > 0) {
                        //loop through each row of the search results to show each product
                        while($row = mysqli_fetch_assoc($result)) {
                            if(isset($_SESSION["products"]))
                            {
                                //using sessproductexists is for the "Success"... this is to let
                                //the user know that choosing the specific product was identified and
                                //successfully added to the cart
                                $sessprodexists = 0;
                                foreach ($_SESSION["products"] as $cart_itm)
                                {
                                    //this is the statement to see if the item was added to the cart
                                    //which will later display as a "Success!" on what used to be "Add to Cart"
                                    //for the specific product
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
                                //show the current product record's image
                                echo '<img src="'.$row['image'].'"/>';
                                ?>
                        </div>
                        <div class = "resultscontainer">
                            <div class = "resultsheader">
                                    <?php
                                    //show the current product record's name
                                    echo $row['sku'];
                                    ?>
                            </div>
                            <div class = "resultswords">
                                    <?php
                                    //show the current product record's family
                                    echo $row['family']; ?> <br> <?php
                                    echo "$";
                                    //show the current product record's price
                                    echo $row['price']; ?>
                            </div>
                            <div class = "resultsdescription">
                                    <?php
                                    //show the current product record's description
                                    echo $row['description'];
                                    ?>
                            </div>
                        </div>
                    </div>
                    <!--
                    This is the form to add the current product to the cart
                    it will call the updatecart page when the user chooses to add the product to the cart
                    -->
                    <form class = "resultspagebuttoncontainer" method="post" action="updatecart.php">
                    <div class = "resultspagebuttoncontainer">
                        <!--
                        The quantity field so the user can add more than one quantity of the current product to the cart
                        -->
                        <input class = "quantityenter" value = "1" id = "searched" type="text" name="quantityentered">
                        <?php
                        //a check to see if the current product was already chosen and if it was then
                        //show Success! for where Add to Cart would be
                        if($sessprodexists)
                        {
                            echo '<button type = "submit" class = "resultsbuttonchosen">Success!</button>';
                        }
                        //if the current product record hasnt been added to cart just show Add to Cart
                        else{
                        ?>
                        <button type = "submit" class = "resultsbutton">
                            Add to Cart
                        </button>
                        <?php
                        }
                        //some hidden values we want to pass to the updatecart page...
                        //these include the id of the current product so we can query on it
                        //as well as the orderid, search parameters, and current_url
                        echo '<input type="hidden" name="product_code" value="'.$row['id'].'" />';
                        echo '<input type="hidden" name="orderid" value="'.$orderid.'" />';
                        echo '<input type="hidden" name="type" value="add" />';
                        echo '<input type="hidden" name="search" value="'.$search.'" />';
                        echo '<input type="hidden" name="return_url" value="'.$current_url.'" />';

                        ?>
                    </div>
                    </form>
                </div>
                        <?php
                        }
                        //if the search query had no results just reply with no results
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