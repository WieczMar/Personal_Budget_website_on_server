<?php
	session_start(); 

    if ((!isset($_POST['addButton']))) 
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

            $userId = $_SESSION['userId'];
            $amount = $_POST['amount'];
            $date = $_POST['date'];
            $categoryId = $_POST['category'];
            $comment = $_POST['comment'];

            if($_POST['addButton'] == "income"){

                $isIncomeAddedCorrectly = $connection->query("INSERT INTO incomes VALUES (NULL, '$userId', '$categoryId', '$amount', '$date', '$comment')");
                if (!$isIncomeAddedCorrectly) throw new Exception($connection->error);

                $_SESSION['savingTransactionCompleted']="You have successfully added new income!";
                header('Location: add_income.php');
            }

            if($_POST['addButton'] == "expense"){

                $paymentMethodId = $_POST['paymentMethod'];

                $isExpenseAddedCorrectly = $connection->query("INSERT INTO expenses VALUES (NULL, '$userId', '$categoryId', '$paymentMethodId', '$amount', '$date', '$comment')");
                if (!$isExpenseAddedCorrectly) throw new Exception($connection->error);

                $_SESSION['savingTransactionCompleted']="You have successfully added new expense!";
                header('Location: add_expense.php');
            }

                       
            $connection->close();
        }
    }
    catch(Exception $exceptionError)
    {
	    echo '<div class="incorrect-validation-text">Server ERROR!</div>';
        echo '<div class="incorrect-validation-text">Detailed Information: '.$exceptionError.';</div>';
	}

?>