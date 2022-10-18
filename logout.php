<?php

	session_start();
	
	session_unset(); // delete ALL variables stored in session dictionary
	
	header('Location: index.php'); // go to login website

?>