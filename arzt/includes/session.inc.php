<?php

	session_start();
	if (isset($_SESSION['userId']))
	{
		session_regenerate_id();
	}
	else
	{
		header("location:/index.php");
		exit(0);
	}