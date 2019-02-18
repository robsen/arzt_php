<?php

	session_start();
	if (isset($_SESSION['userId']))
	{
		session_regenerate_id();
	}
	else
	{
		header("location:${_SERVER['DOCUMENT_ROOT']}/index.php");
		exit(0);
	}