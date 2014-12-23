<!--
Date: 12/22/2014
User: ayasavolian

- This page is referenced by index.php
- It will add the cookies if the credentials entered were correct

-->
<?php
	require 'servercall.php';
	//get the variables from the post on the index page
	$username = $_POST['username'];
	$password = $_POST['password'];
	//home page referenced if the credentials are correct
	$home = '/home.php';
	//return back to index page if credentials are false
	$index = '/index.php?login=false';
	$value = "true";
	//doing a sql search to see if the user exists with the correct credentials
	$userexistsql = "SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password'";
	$userexistrows = mysqli_query($mysqli, $userexistsql);
    if(mysqli_num_rows($userexistrows) > 0)
    {
    	//set the session and create the sesscookie cookie
		session_start();
		setcookie("sesscookie", $value, time()+1000);  /* expire in 1 hour */
		header('location:'.$home);
	}
	else
	{
		//return back to the home page
		header('location:'.$index);
	}
?>