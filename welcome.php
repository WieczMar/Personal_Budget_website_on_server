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
  <title>Welcome site</title>
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

  <nav id="mainNavbar" class="navbar navbar-expand-md navbar-dark bg-gradient">
    <div class="container">
      <a class="navbar-brand btn disabled" href="#">MENU</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navLinks"
        aria-controls="navLinks" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navLinks">
        <ul class="navbar-nav">
          <li class="nav-item d-flex flex-row align-items-center bg-gradient current-website">
            <div class="test">
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
      <div class="col-sm-10 col-md-6">
        <div class="text-center panel-title">Welcome to Personal Budget website!</div>
        <div class="welcome-text"> The Personal Budget is a web application that helps you to manage your daily budget and enhance it to the next level. 
          It allows you to add your incomes or expenses and view comprehend analysis of your finances in selected period. 
          If you already have an account just sign in but if you don't have it yet then sign up and try it for yourself!
        </div>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-around">
          <a class="btn btn-primary col-sm-3 button" href="index.php" role="button">Sign in</a>
          <a class="btn btn-primary col-sm-3 signup-button"href="registration.php" role="button">Sign up</a>
        </div>
      </div> 
      <div class="col-sm-10 col-md-6 ">
        <img class="main-site-picture d-none d-md-block " src="imgs\main-site.jpg" alt="Main site picture" />
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>