<?php
    if($_COOKIE['sesscookie'] == 'true')
    {
    session_start();
	require 'servercall.php';
    $orderid = rand(1, 99999999999);
    $cookieid = rand(1, 999999999999999999);
    $_SESSION["orderid"] = $orderid;
    $current_url = base64_encode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $sessid = session_id();
    $_SESSION["usercookieid"] = $sessid;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head lang="<?php echo $str_language; ?>" xml:lang="<?php echo $str_language; ?>">
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script type="text/javascript">
document.write(unescape("%3Cscript src='//munchkin.marketo.net/munchkin.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script>Munchkin.init('314-QVX-610');</script>
<script src="js/marketoscripts.js"></script>
<link rel="stylesheet" type="text/css" href="css/global.css">
</head>
<body>
    <?php
    $usersql = "SELECT * FROM chosen WHERE userid = '$sessid'";
    $usersqlresults = mysqli_query($mysqli, $usersql);
    //if theres not a lead already, insert them
    if(mysqli_num_rows($usersqlresults) == 0)
    {
    ?>

    <div id = "lightbox">
        <div id = "closeboxcontainer">
            <div id = "closebox" onclick="lightboxoff()">
                X
            </div>
        </div>
        <div id = "newslettercontainer">
            <div id = "newslettertitle">
                Sign up for <br>
                <b> Email Updates </b>
            </div>
            <form action = "/newslettersubmit.php" method="post">
            <input class = "newsletterbar" placeholder = "email address" id = "searched" type="text" name="newsletter">
            <input type="submit" class = "newslettersubmit" onclick = "lightboxoff()" value="Subscribe Now!" data-nav-tabindex="72" tabindex="1">
            <?php
            echo '<input type="hidden" name="return_url" value="'.$current_url.'" />';
            echo '<input type="hidden" name="sessid" value="'.$sessid.'" />';
            ?>
        </div>
    </div>
    <div id = "lightboxbackground">
    </div>
    <?php
    }  
    else
    {}
    require 'topcontainer.php';
    ?>
    <div class = "bodycontainer">
        <div class = "maincontent">
            <div class = "promocontainer" id = "promobanner">
                <div class = "promoheadercontainer">
                    <div class = "promowords">
                        <div class = "promoheader">
                            Love Magic? <br>
                            Don't Miss This Offer!
                        </div>
                        <div class = "promodescription">
                            Subscribe to Turner Ecommerce for the latest news on our promotions and automatically get a $100
                            gift card for Magic cards!
                        </div>
                        <div class = "subscribe">
                            <div class = "subscribebutton">
                                Subscribe
                            </div>
                        </div>
                    </div>
                    <div class = "promobanner">
                        <img id = "magicimage" src = "images/promobanner.png">
                    </div>
                </div>
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
<?php
}
else
{
    $index = '/index.php';
    header('location:index.php?login=false');
}
?>