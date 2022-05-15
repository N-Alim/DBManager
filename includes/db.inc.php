<h1>Base de données <?php echo $_GET['db']?></h1>
<?php

if (!isset($_SESSION['login']))
{
    echo "<script>
    document.location.replace('http://localhost/DBManager');
    </script>";
}

else if (isset($_GET['delete']))
{
    $connHand = new DBHandler($_GET["db"]);
    echo $connHand->dropTable($_GET['delete']);

    echo "<script>
    document.location.replace('http://localhost/DBManager/index.php?page=db&db=" . $_GET['db'] ."' );
    </script>";
}

else
{
    $connHand = new DBHandler();
    echo $connHand->getDatatablesAsTable();

    $formCreator = new Form("post", "index.php?page=db&db=" . $_GET['db'], $_SESSION["token"]);
    $formCreator->getFormValues();
    $form = $formCreator->createFormFromCSV("./assets/frmFiles/newtable.csv");

    if (isset($_POST['envoi']))
    {
        if (hash_equals($_SESSION['token'], $_POST['token'])) 
        {
            $errorHand = new ErrorHandler;

            $formCreator->setValuesChecker($errorHand);
    
            $formCreator->checkValues();
    
            if ($formCreator->getErrorsCount() === 0)
            {
                $connHand = new DBHandler($_GET['db']);
    
                $resultat = $connHand->getDatatableWithName($formCreator->getValue("newtable"));
    
                if (count($resultat) !== 0) 
                {
                    echo "<p>Une table avec ce nom existe déjà</p>";
                }
        
                else 
                {
                    $connHand->createTable($formCreator->getValue("newtable"));
                    echo "<script>
                    document.location.replace('http://localhost/DBManager/index.php?page=db&db=" . $_GET['db'] ."' );
                    </script>";
                }    
            }
    
            else
            {
                echo $formCreator->checkErrors();
                echo $form;
                echo "<div><a href='http://localhost/DBManager/index.php?page=dblist'>Retourner à la page précédente</a></div>";
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
        echo "<div><a href='http://localhost/DBManager/index.php?page=dblist'>Retourner à la page précédente</a></div>";
    }
}