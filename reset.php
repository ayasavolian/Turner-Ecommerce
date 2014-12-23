<!--
Date: 12/22/2014
User: ayasavolian

- This action page is used to reset the instance of the ecommerce site

-->
<?php
session_start();
require 'servercall.php';
$index = "/home.php";
//reset the sesscookie cookie
setcookie('sesscookie', '', time() -42000);
        $_COOKIE["sesscookie"] = null;
        unset($_COOKIE["sesscookie"] );
        $_SESSION = array();
//reset the PHPSESSID cookie
setcookie('PHPSESSID', '', time() -42000);
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
        //destroy the session created
        session_destroy();
//return back to the index page
header('Location:'.$index);
?>