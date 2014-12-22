<?php
session_start();
$email = $_POST['newsletter'];
$sessid = $_POST['sessid'];
$_SESSION['PHPSESSID'];

$sqluser = "INSERT INTO `ecommerce`.`chosen` (`currdate`, `userid`, `firstname`, `lastname`, `email`, `street`, `city`, `state`, `zip`, `country`) 
        VALUES (CURRENT_TIMESTAMP, '$sessid', '', '', '$email', '', '', '', '', '')";
        $insertresults = mysqli_query($mysqli, $sqluser);

header('Location:'.$return_url);
session_destroy();
?>