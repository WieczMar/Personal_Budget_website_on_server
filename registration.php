<?php
	session_start();
	
  if (isset($_POST['email'])) // check if for was submited = all variables name, email, password were saved (even if empty) to POST container, so checking one variable is enough
	{
    $isValidationCorrect=true; // validation flag 
    // name validation
		$name = $_POST['name'];	
		if ((strlen($name)<3) || (strlen($name)>30)) // check the name length
		{
			$isValidationCorrect=false;
			$_SESSION['incorrectName']="Name has to be at least 3 and up to 30 sign length!";
		}
    if (ctype_alnum($name)==false) // check if argument has only alphanumeric signs
		{
			$isValidationCorrect=false;
			$_SESSION['incorrectName']="Name has to be without distinctive marks";
		}

    //email validation
		$email = $_POST['email']; 
		$filteredEmail = filter_var($email, FILTER_SANITIZE_EMAIL); // remove forbidden signs if such exist and leave rest
		if ((filter_var($filteredEmail, FILTER_VALIDATE_EMAIL)==false) || ($filteredEmail!=$email)) 
		{
			$isValidationCorrect=false;
			$_SESSION['incorrectEmail']="Type correct email address!";
		}

    //password validation
		$password = $_POST['password'];
		if ((strlen($password)<8) || (strlen($password)>30))
		{
			$isValidationCorrect=false;
			$_SESSION['incorrectPassword']="Password has to be at least 8 and up to 30 sign length!";
		}

		$hashedPassword = password_hash($password, PASSWORD_DEFAULT); // encrypt password (hash), PASSWORD_DEFAULT means use the best method currently known

    //Google recaptcha validation
    $secretKey = "6Lfa1ZgiAAAAALP1oykI5JFBlOqiv8zT0_GsJiNP";
    $reCaptchaResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);
    $reCaptchaResponse = json_decode($reCaptchaResponse);
		if ($reCaptchaResponse->success==false)
		{
			$isValidationCorrect=false;
			$_SESSION['incorrectReCaptcha']="reCaptcha ERROR!";
		}	

    //Remember typed data
		$_SESSION['name'] = $name;
		$_SESSION['email'] = $email;
		$_SESSION['password'] = $password;

    require_once "connect.php"; // import from file data necessary to connect to database
		mysqli_report(MYSQLI_REPORT_STRICT);

    try 
		{
			$connection = new mysqli($host, $db_user, $db_password, $db_name);
			if ($connection->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
        //Check if email already exists
				$result = $connection->query("SELECT id FROM users WHERE email='$email'");
				if (!$result) throw new Exception($connection->error);
				
				$emailsCount = $result->num_rows;
				if($emailsCount>0)
				{
					$isValidationCorrect=false;
					$_SESSION['incorrectEmail']="An account with such email already exists!";
				}	

        if ($isValidationCorrect==true)
				{
          $isUserAddedCorrectly = $connection->query("INSERT INTO users VALUES (NULL, '$name', '$hashedPassword', '$email')");
          if (!$isUserAddedCorrectly) throw new Exception($connection->error);

          $result = $connection->query("SELECT id FROM users WHERE email='$email'");
				  if (!$result) throw new Exception($connection->error);

          $row = $result->fetch_assoc();
          $userId = $row['id'];       
          $result->free_result(); 

          $isIncomeCategoriesAddedCorrectly = $connection->query("INSERT INTO incomes_category_assigned_to_users (user_id, name) SELECT '$userId', name FROM incomes_category_default");
          if (!$isIncomeCategoriesAddedCorrectly) throw new Exception($connection->error);

          $isExpenseCategoriesAddedCorrectly = $connection->query("INSERT INTO expenses_category_assigned_to_users (user_id, name) SELECT '$userId', name FROM expenses_category_default");
          if (!$isExpenseCategoriesAddedCorrectly) throw new Exception($connection->error);

          $isPaymentMethodsAddedCorrectly = $connection->query("INSERT INTO payment_methods_assigned_to_users (user_id, name) SELECT '$userId', name FROM payment_methods_default");
          if (!$isPaymentMethodsAddedCorrectly) throw new Exception($connection->error);

					$_SESSION['registrationCompleted']="You have successfully signed up!";
					
				}

        $connection->close();
      }
    }
    catch(Exception $exceptionError)
		{
			echo '<div class="incorrect-validation-text">Server ERROR!</div>';
      echo '<div class="incorrect-validation-text">Detailed Information: '.$exceptionError.';</div>';
		}
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Google fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
  <!-- main CSS -->
  <link rel="stylesheet" href="app.css" />
  <title>Registration</title>
  <script src="https://www.google.com/recaptcha/enterprise.js?render=6Lfa1ZgiAAAAABajZDkPplmwyNZQJHM0X8tx0A4R"></script>
</head>

<body>
  <header class="container-fluid">
    <div class="row justify-content-center align-items-center">
      <div class="col-6 d-flex flex-row">
        <div class="display-6 text-center">Personal Budget</div>
        <div>
          <img class="d-none d-md-block header-icon" src="imgs\header.png" alt="hand-icon" />
        </div>
      </div>
      <div class="col-4 text-center">Become the Master of Your Money</div>
    </div>
  </header>

  <nav id="mainNavbar" class="navbar navbar-expand-sm navbar-dark bg-gradient">
    <div class="container">
      <a class="navbar-brand btn disabled" href="#">MENU</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLinks"
        aria-controls="navLinks" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navLinks">
        <ul class="navbar-nav">
          <li class="nav-item d-flex flex-row align-items-center">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-house-door" viewBox="0 0 16 16">
                <path
                  d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z" />
              </svg>
            </div>
            <a class="nav-link active" aria-current="page" href="welcome.php">About site</a>
          </li>
          <li class="nav-item d-flex flex-row align-items-center">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                  d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z" />
                <path fill-rule="evenodd"
                  d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
              </svg>
            </div>
            <a class="nav-link active" aria-current="page" href="index.php">Sign in</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="row justify-content-center align-items-center">
      <div class="col-10 col-md-8 col-lg-6 col-xl-5 panel">
        <?php
          if (isset($_SESSION['registrationCompleted']))
          {
            echo '<div class="d-flex justify-content-center" style="color:green; padding-top:20px">'.$_SESSION['registrationCompleted'].'</div>';
            unset($_SESSION['registrationCompleted']);
            unset($_SESSION['name']);
            unset($_SESSION['email']);
            unset($_SESSION['password']);
          }
        ?>
        <div class="text-center panel-title">Create your account</div>
        <form id="registrationForm" method="post">
          <div class="form-group input-data">
            <label for="name" class="form-label">First Name</label>
            <div class="input-group">
              <span class="input-group-text" id="basic-addon1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-person-fill" viewBox="0 0 16 16">
                  <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                </svg>
              </span>
              <input type="text" class="form-control" id="name" placeholder="name" name="name" aria-describedby="basic-addon1" value="<?php
                if (isset($_SESSION['name']))
                {
                  echo $_SESSION['name'];
                  unset($_SESSION['name']);
                }
              ?>">
            </div>
            <?php
			        if (isset($_SESSION['incorrectName']))
			        {
			        	echo '<div class="incorrect-validation-text">'.$_SESSION['incorrectName'].'</div>';
			        	unset($_SESSION['incorrectName']);
			        }
		        ?>
          </div>
          <div class="form-group input-data">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text" id="basic-addon2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                  class="bi bi-envelope-fill" viewBox="0 0 16 16">
                  <path
                    d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757Zm3.436-.586L16 11.801V4.697l-5.803 3.546Z" />
                </svg>
              </span>
              <input type="text" class="form-control" id="email" name="email" placeholder="email" aria-describedby="basic-addon2" value="<?php
                if (isset($_SESSION['email']))
                {
                  echo $_SESSION['email'];
                  unset($_SESSION['email']);
                }
              ?>">
            </div>
            <?php
			        if (isset($_SESSION['incorrectEmail']))
			        {
			        	echo '<div class="incorrect-validation-text">'.$_SESSION['incorrectEmail'].'</div>';
			        	unset($_SESSION['incorrectEmail']);
			        }
		        ?>
          </div>
          <div class="form-group input-data">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text" id="basic-addon3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock"
                  viewBox="0 0 16 16">
                  <path
                    d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z" />
                </svg>
              </span>
              <input type="password" class="form-control password-input" id="password" name="password" placeholder="password" aria-describedby="basic-addon3" value="<?php
                if (isset($_SESSION['password']))
                {
                  echo $_SESSION['password'];
                  unset($_SESSION['password']);
                }
              ?>">
                  <i class="bi bi-eye-slash" id="togglePassword"></i>
            </div>
            <?php
			        if (isset($_SESSION['incorrectPassword']))
			        {
			        	echo '<div class="incorrect-validation-text">'.$_SESSION['incorrectPassword'].'</div>';
			        	unset($_SESSION['incorrectPassword']);
			        }
		        ?>
          </div>
          <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary button g-recaptcha" data-sitekey="6Lfa1ZgiAAAAABajZDkPplmwyNZQJHM0X8tx0A4R"
                    data-callback='onSubmit' data-action='submit'>Sign up</button>
          </div>
          <?php
			        if (isset($_SESSION['incorrectReCaptcha']))
			        {
			        	echo '<div class="d-flex justify-content-center incorrect-validation-text">'.$_SESSION['incorrectReCaptcha'].'</div>';
			        	unset($_SESSION['incorrectReCaptcha']);
			        }
		        ?>
        </form>
        <div class="already-registered">
          <span>Have already an account? </span>
          <a class="link" href="login.php">Sign in</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
  <script src="js\registration.js"></script>
  <script>
   function onSubmit(token) {
     document.getElementById("registrationForm").submit();
   }
</script>
</body>

</html>