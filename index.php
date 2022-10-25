<?php
	session_start();
	
	if ((isset($_SESSION['loggedIn']))&&($_SESSION['loggedIn']==true))
	{
		header('Location: main_menu.php');
		exit(); // exit file and go to file indicated above in header. Without exit() the file would firstly execute to the end and then go to header file 
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
  <!-- main CSS -->
  <link rel="stylesheet" href="app.css" />
  <title>Login</title>
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
                class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                <path
                  d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
              </svg>
            </div>
            <a class="nav-link active" aria-current="page" href="registration.php">Sign up</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="row justify-content-center align-items-center">
      <div class="col-10 col-md-8 col-lg-6 col-xl-5 panel">
        <div class="text-center panel-title">Sign in to your account</div>
        <?php 
          if (isset($_SESSION['loginError']))
          {
            echo $_SESSION['loginError'];
            unset($_SESSION['loginError']);
          }
          ?>
        <form action="login.php" method="post">
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
              <input type="text" class="form-control" id="email" name="email" placeholder="email" aria-describedby="basic-addon2">
            </div>
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
              <input type="password" class="form-control" id="password" name="password" placeholder="password"
                aria-describedby="basic-addon3">
            </div>
          </div>
          <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary button">Sign in</button>
          </div>
        </form>
        <div class="already-registered">
          <span>Do not have an account? </span>
          <a class="link" href="registration.php">Sign up</a>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>