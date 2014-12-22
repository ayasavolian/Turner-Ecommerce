<?php
session_start();
require 'servercall.php';
$index = "/te2/home.php";

setcookie('sesscookie', '', time() -42000);
        $_COOKIE["sesscookie"] = null;
        unset($_COOKIE["sesscookie"] );
        $_SESSION = array();

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
        session_destroy();

header('Location:'.$index);
?>