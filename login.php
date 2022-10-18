<?php
	session_start(); // function that allows this documents using global SESSION variable
	
	if ((!isset($_POST['email'])) || (!isset($_POST['password']))) // if user did not login (e.g. manually typed link to login.php insted of submitting login form in index.php), go back to index.php site
	{
		header('Location: index.php');
		exit();
	}

	require_once "connect.php"; // include once, show error in anycase
	
	$connection = @new mysqli($host, $db_user, $db_password, $db_name); // create connection to DB and store it in connection variable
	
	if ($connection->connect_errno!=0) // in case of connection fails
	{
		echo "Error: ".$connection->connect_errno; // show connection error number
	}
	else // if connection succeeds
	{
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		$email = htmlentities($email, ENT_QUOTES, "UTF-8"); // code sanitation - protecion from MySQL Injection, function htmlentities() that change all signs in variable to entities that browser do not use them as source code
		$password = htmlentities($password, ENT_QUOTES, "UTF-8"); // like above, example: <div> converts to &lt;div&gt;
	
		if ($result = @$connection->query( // result = send a query to our connected database
		sprintf("SELECT * FROM users WHERE email='%s' AND password='%s'", // %s means variable of type string, we are using sprintf just for better readability instead of concatanating with dots etc.
		mysqli_real_escape_string($connection,$email), // mysqli_real_escape_string is advanced function in php that also protect form MySQL Injection
		mysqli_real_escape_string($connection,$password))))
		{
			$usersCount = $result->num_rows; // set the number of users to usersCount variable from result of our query (how many rows we get in query, 1 user = 1 row) 
			if($usersCount>0) // if we found user with such login and password in database
			{
				$_SESSION['loggedIn'] = true; // session variable flag (dictionary) informing that user is now logged in. Reminder: Session variable (dictionary) is global and avaible for all other php pages
				unset($_SESSION['loginError']); // delete login error session variable if exists because we successfully logged in

				$row = $result->fetch_assoc(); // set data that we got from query to variable row that becomes dictionary and table columns (fields) are keys
				$_SESSION['id'] = $row['id']; // set necessary data to global session dictionary
				$_SESSION['username'] = $row['username']; // set necessary data to global session dictionary
				
				$result->free_result(); // Remember to clear results from query when you do not need it more! alternatives to free_result() is free() or close();
				header('Location: main_menu.php'); // go to main_menu.php site
				
			} else {
				
				$_SESSION['loginError'] = '<div class="text-center" style="color:red; padding-top:20px">Wrong email or password!</div>';
				header('Location: index.php');
				
			}
			
		}
		
		$connection->close(); // remember to close connection with database!
	}
?>