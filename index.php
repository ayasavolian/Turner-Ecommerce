<!--
Date: 12/22/2014
User: ayasavolian

- The index page to log in

-->
<?php
	require 'servercall.php';
    //if they failed to login get the failure
    $login = $_GET['login'];
    //get the current URL
    $current_url = base64_encode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    //if we have already set the PHPSESSID and the sesscookie cookies then they already
    //have an instance of TE working so we don't need them to log in
    if($_COOKIE['PHPSESSID'] == true && $_COOKIE['sesscookie'] == "true")
    {
        $index = "/te2/home.php";
        header('Location:'.$index);
    }
    else
    {
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
    <div class = "logincontainer">
        <div class = "loginpage">
            <div class = "loginbody">
                <div class = "login">
                    <div class = "logintitle">
                        <img style = "height: 40px" src = "images/turnerecommerce.png">
                    </div>
                    <div class = "logincreds">
                        <!--
                        Login credentials go here...
                        Once done go to a credentials action page to add the cookies
                        if accurate credentials
                        -->
                        <form action = "/logincheck.php" method="post">
                            <?php
                                if($login == 'false')
                                {
                                    echo '<div style = "padding-left: 20px; padding-bottom: 20px; font-size: 14px; color: #ff0000"> The login attempt failed. Please try again.</div>';
                                }
                            ?>
                            <div class = "logindetail">
                                Username
                            </div>
                            <input class = "loginfield" placeholder = "username" type="text" name="username">
                            <div style = "padding-top: 20px;" class = "logindetail">
                                Password
                            </div>
                            <input class = "loginfield" placeholder = "password" type="password" name="password">
                            <div style = "height: 25px">
                            </div>
                            <input type="submit" class = "loginsubmit" value="login" data-nav-tabindex="72" tabindex="1">
                        </form>
                    <div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}
?>