<?php
	session_start();
	
	if ((!isset($_SESSION['loggedIn']))||($_SESSION['loggedIn']==false))
	{
		header('Location: index.php');
		exit();  
	}

    require_once "connect.php"; // import from file data necessary to connect to database
	mysqli_report(MYSQLI_REPORT_STRICT);

    try 
	{
        $connection = new mysqli($host, $db_user, $db_password, $db_name);
        if ($connection->connect_errno!=0)
        {
            throw new Exception(mysqli_connect_errno());
        }
        else {

            if (!isset($_POST['selectedPeriod'])) $selectedPeriod = "Current month"; // first entry on balance page after logging
            else $selectedPeriod = $_POST['selectedPeriod'];

            function getPeriodOptions($selectedPeriod) // dynamically show selected option in dropdown selection
            {
                $periods = array('Current month', 'Previous month', 'Current year', 'Nonstandard');
                foreach($periods as $period){
                    if($period == $selectedPeriod) echo '<option value="'.$period.'" selected>'.$period.'</option>';
                    else echo '<option value="'.$period.'">'.$period.'</option>';
                }
            }

            // getting current date
            $currentMonth = date('m');
            $currentYear = date('Y');
            $previousMonth = sprintf("%02d", $currentMonth-1);

            switch ($selectedPeriod) {
                case 'Current month':
                    $startDate = $currentYear.'-'.$currentMonth.'-01';
                    $endDate = $currentYear.'-'.$currentMonth.'-31';
                    break;
                case 'Previous month':
                    $startDate = $currentYear.'-'.$previousMonth.'-01';
                    $endDate = $currentYear.'-'.$previousMonth.'-31';
                    break;
                case 'Current year':
                    $startDate = $currentYear.'-01-01';
                    $endDate = $currentYear.'-12-31';
                    break;
                case 'Nonstandard':
                    $startDate = $_POST['startDate'];
                    $endDate = $_POST['endDate'];
                    if($startDate > $endDate) $_SESSION["wrongDateRange"] = '<div class="incorrect-validation-text" style="text-align: center;">Selected wrong date range!</div>';
                    break;
            }

            // get incomes data from database
            $result = $connection->query("SELECT name AS categoryName, SUM(amount) AS categoryAmount FROM incomes, incomes_category_assigned_to_users 
                WHERE incomes.user_id='".$_SESSION['userId']."' AND incomes.income_category_assigned_to_user_id=incomes_category_assigned_to_users.id 
                AND incomes.date_of_income BETWEEN '$startDate' AND '$endDate' GROUP BY categoryName ORDER BY categoryAmount DESC");
            if (!$result) throw new Exception($connection->error);
            
            $incomesCount = $result->num_rows;
            if($incomesCount>0)
            {
                $rowsIncomes= $result->fetch_all(MYSQLI_ASSOC); 
                $result->free_result(); 

                $sumOfIncomes = number_format(array_sum(array_column($rowsIncomes, 'categoryAmount')), 2); // aggregate incomes amount with 2 digit precision
            }
            else { // null handling case
                $rowsIncomes = array(array('categoryName' => '-', 'categoryAmount' => '-'));
                $sumOfIncomes = number_format(0, 2);
            }	

            // get expenses data from database
            $result = $connection->query("SELECT name AS categoryName, SUM(amount) AS categoryAmount FROM expenses, expenses_category_assigned_to_users 
                WHERE expenses.user_id='".$_SESSION['userId']."' AND expenses.expense_category_assigned_to_user_id=expenses_category_assigned_to_users.id 
                AND expenses.date_of_expense BETWEEN '$startDate' AND '$endDate' GROUP BY categoryName ORDER BY categoryAmount DESC");
            if (!$result) throw new Exception($connection->error);
            
            $expensesCount = $result->num_rows;
            if($expensesCount>0)
            {
                $rowsExpenses = $result->fetch_all(MYSQLI_ASSOC); 
                $result->free_result();  

                $sumOfExpenses = number_format(array_sum(array_column($rowsExpenses, 'categoryAmount')), 2); // aggregate expenses amount with 2 digit precision
            }
            else { // null handling case
                $rowsExpenses = array(array('categoryName' => '-', 'categoryAmount' => '-'));
                $sumOfExpenses = number_format(0, 2);
            }
            
            $balance = number_format($sumOfIncomes - $sumOfExpenses, 2);

            $connection->close();
        }
    }
    catch(Exception $exceptionError)
    {
	    echo '<div class="incorrect-validation-text">Server ERROR!</div>';
        echo '<div class="incorrect-validation-text">Detailed Information: '.$exceptionError.';</div>';
	}

?>
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
    <title>View balance</title>
</head>

<body>
    <header class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-6 d-flex flex-row">
                <div class="display-6 text-center">Personal Budget</div>
                <div>
                    <img class="d-none d-md-block header-icon" src="imgs\header.png." alt="hand-icon" />
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
                    <li class="nav-item d-flex flex-row align-items-center">
                        <div class="test">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-house-door" viewBox="0 0 16 16">
                                <path
                                    d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z" />
                            </svg>
                        </div>
                        <a class="nav-link active" aria-current="page" href="main_menu.php">Main site</a>
                    </li>
                    <li class="nav-item d-flex flex-row align-items-center">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-currency-dollar" viewBox="0 0 16 16">
                                <path
                                    d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z" />
                            </svg>
                        </div>
                        <a class="nav-link active" aria-current="page" href="add_income.php">Add income</a>
                    </li>
                    <li class="nav-item d-flex flex-row align-items-center">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-cart2" viewBox="0 0 16 16">
                                <path
                                    d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z" />
                            </svg>
                        </div>
                        <a class="nav-link active" aria-current="page" href="add_expense.php">Add expense</a>
                    </li>
                    <li class="nav-item d-flex flex-row align-items-center bg-gradient current-website">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z" />
                            </svg>
                        </div>
                        <a class="nav-link active" aria-current="page" href="view_balance.php">View balance</a>
                    </li>
                    <li class="nav-item d-flex flex-row align-items-center">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-gear" viewBox="0 0 16 16">
                                <path
                                    d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
                                <path
                                    d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
                            </svg>
                        </div>
                        <a class="nav-link active" aria-current="page" href="#">Settings</a>
                    </li>
                    <li class="nav-item d-flex flex-row align-items-center">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-door-open" viewBox="0 0 16 16">
                                <path d="M8.5 10c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1z" />
                                <path
                                    d="M10.828.122A.5.5 0 0 1 11 .5V1h.5A1.5 1.5 0 0 1 13 2.5V15h1.5a.5.5 0 0 1 0 1h-13a.5.5 0 0 1 0-1H3V1.5a.5.5 0 0 1 .43-.495l7-1a.5.5 0 0 1 .398.117zM11.5 2H11v13h1V2.5a.5.5 0 0 0-.5-.5zM4 1.934V15h6V1.077l-6 .857z" />
                            </svg>
                        </div>
                        <a class="nav-link active" aria-current="page" href="logout.php">Sign out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-end align-items-center">
            <div class="col-6 col-md-4 col-lg-3">
                <div class="form-group input-data">
                    <form id="periodForm" method="post">
                        <label for="selectedPeriod" class="form-label">Select period</label>
                        <select class="form-select" name="selectedPeriod" onchange="js\view_balance.js" aria-label="period" id="selectedPeriod" >
                            <?php getPeriodOptions($selectedPeriod);?>
                        </select>
                        <?php 
                            if(isset($_SESSION["wrongDateRange"])){
                            echo $_SESSION["wrongDateRange"];
                            unset($_SESSION["wrongDateRange"]);
                            } 
                        ?>
                    </form>
                    
                </div>

            </div>
        </div>
        <div class="row justify-content-center align-items-center">
            <div class="col-11 col-md-12 panel">
                <div class="row justify-content-around align-items-start">
                    <div class="col-11 col-md-6 col-lg-5">
                        <div class="text-center table-title">Incomes</div>
                        <table class="table table-striped table-hover" id="incomesTable">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Total amount [PLN]</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($rowsIncomes as $income){
                                        echo '<tr><td>'.$income['categoryName'].'</td> <td>'.$income['categoryAmount'].'</td> </tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-11 col-md-6 col-lg-5">
                        <div class="text-center table-title">Expenses</div>
                        <table class="table table-striped table-hover" id="expensesTable">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Total amount [PLN]</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach($rowsExpenses as $expense){
                                        echo '<tr><td>'.$expense['categoryName'].'</td> <td>'.$expense['categoryAmount'].'</td> </tr>';
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-11 col-md-5 col-lg-4">
                        <div class="text-center table-title">Summary</div>
                        <table class="table table-striped table-hover" id="summaryTable">
                            <tr>
                                <th>Total incomes</th>
                                <td colspan="2"><?php echo $sumOfIncomes ?></td>
                            </tr>
                            <tr>
                                <th>Total expenses</th>
                                <td colspan="2"><?php echo $sumOfExpenses ?></td>
                            </tr>
                            <tr>
                                <th>Balance</th>
                                <td colspan="2"><?php echo $balance ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row justify-content-around"> 
                    <div class="col-11 col-md-6 col-lg-5">
                        <canvas id="incomesPieChart"></canvas>
                    </div>
                    <div class="col-11 col-md-6 col-lg-5">
                        <canvas id="expensesPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Select nonstandard period</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post">
            <div class="modal-body">
                <div class="form-group input-data">
                    <label for="date" class="form-label">Start date</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-calendar-check-fill" viewBox="0 0 16 16">
                                <path
                                    d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zm-5.146-5.146-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708.708z" />
                            </svg>
                        </span>
                        <input type="date" class="form-control" id="startDate" name="startDate" aria-describedby="basic-addon2">
                    </div>
                </div>
                <div class="form-group input-data">
                    <label for="date" class="form-label">End date</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-calendar-check-fill" viewBox="0 0 16 16">
                                <path
                                    d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zM16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zm-5.146-5.146-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 0 1 .708-.708L7.5 10.793l2.646-2.647a.5.5 0 0 1 .708.708z" />
                            </svg>
                        </span>
                        <input type="date" class="form-control" id="endDate" name="endDate" aria-describedby="basic-addon2">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="submit" class="btn btn-secondary cancel-button" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary button" name="selectedPeriod" value="Nonstandard">Set selected dates</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
    <script src="js\view_balance.js"></script>
</body>

</html>