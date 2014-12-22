<?php
	require 'servercall.php';
	$username = $_POST['username'];
	$password = $_POST['password'];
	$home = '/te2/home.php';
	$index = '/te2/index.php?login=false';
	$value = "true";

	$userexistsql = "SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password'";
	$userexistrows = mysqli_query($mysqli, $userexistsql);
    if(mysqli_num_rows($userexistrows) > 0)
    {
		session_start();
		setcookie("sesscookie", $value, time()+1000);  /* expire in 1 hour */
		header('location:'.$home);
	}
	else
	{
		header('location:'.$index);
	}
?>