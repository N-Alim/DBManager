<h1>Bases de données</h1>
<?php


if (!isset($_SESSION['login']))
{
    echo "<script>
    document.location.replace('http://localhost/DBManager');
    </script>";
}

else if (isset($_GET['delete']))
{
    $connHand = new DBHandler();
    echo $connHand->dropDatabase($_GET['delete']);

    echo "<script>
    document.location.replace('http://localhost/DBManager/index.php?page=dblist');
    </script>";
}

else
{
    $connHand = new DBHandler();
    echo $connHand->getUserDatabasesAsTable();

    $formCreator = new Form("post", "index.php?page=dblist", $_SESSION["token"]);
    $formCreator->getFormValues();
    $form = $formCreator->createFormFromCSV("./assets/frmFiles/newdb.csv");

    if (isset($_POST['envoi']))
    {
        if (hash_equals($_SESSION['token'], $_POST['token'])) 
        {
            $errorHand = new ErrorHandler;

            $formCreator->setValuesChecker($errorHand);

            $formCreator->checkValues();

            if ($formCreator->getErrorsCount() === 0)
            {
                $connHand = new DBHandler();
    
                $resultat = $connHand->getDatabaseWithName($formCreator->getValue("newdb"));
    
                if (count($resultat) !== 0) 
                {
                    echo "<p>Une base de données avec ce nom existe déjà</p>";
                }
        
                else 
                {
                    $connHand->createDatabase($formCreator->getValue("newdb"));
                    echo "<script>
                    document.location.replace('http://localhost/DBManager/index.php?page=dblist');
                    </script>";
                }
            }
    
            else
            {
                echo $formCreator->checkErrors();
                echo $form;
            }
        } 

        else 
        {
            echo "Vous ne venez pas de mon site";
        }
    }

    else
    {
        echo $form;
    }
}