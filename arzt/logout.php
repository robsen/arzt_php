<?php

	session_start();
	$_SESSION['userId'] = null;
	session_destroy();
	header('location:index.php');
	exit(0);