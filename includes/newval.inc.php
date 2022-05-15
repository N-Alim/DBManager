<h1>Nouvelle entrée</h1>
<?php

if (!isset($_SESSION['login']))
{
    echo "<script>
    document.location.replace('http://localhost/DBManager');
    </script>";
}

else
{   
    $formCreator = new Form("post", "index.php?page=newval&db=" . $_GET['db'] . "&table=". $_GET['table'], $_SESSION["token"]);
    $formCreator->getFormValues();

    $connHand = new DBHandler($_GET["db"]);

    $resultat = $connHand->getTableColumnDetails();

    if (count($resultat) > 1)
    {
        $form = $formCreator->createFormFromSqlInputs($resultat);

        if (isset($_POST['envoi']))
        {
            if (hash_equals($_SESSION['token'], $_POST['token'])) 
            {
                $errorHand = new ErrorHandler;
    
                $formCreator->setValuesChecker($errorHand);
        
                $formCreator->checkValues();
        
                if ($formCreator->getErrorsCount() === 0)
                {
        
                    $columnValues = array();
        
                    foreach ($resultat as $columnDetails) 
                    {
                        if ($columnDetails->COLUMN_NAME !== "id")
                        {
                            $colName = $columnDetails->COLUMN_NAME;
                            $columnValues[$colName] = $formCreator->getValue($colName);
                        }
                    }
        
                    $connHand = new DBHandler($_GET["db"]);
        
                    $connHand->addRow($columnValues);
                    echo "<script>
                    document.location.replace('http://localhost/DBManager/index.php?page=table&db=" . $_GET['db'] . "&table=". $_GET['table'] . "' );
                    </script>";
                }
        
                else
                {
                    echo $formCreator->checkErrors();
                    echo $form;
                    echo "<div><a href='http://localhost/DBManager/index.php?page=table&db=" . $_GET['db'] . "&table=" . $_GET['table'] 
                    . "'>Retourner à la page précédente</a></div>";
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
            echo "<div><a href='http://localhost/DBManager/index.php?page=table&db=" . $_GET['db'] . "&table=" . $_GET['table'] 
            . "'>Retourner à la page précédente</a></div>";
        }
    }

    else 
    {
        echo "Veuillez ajouter une nouvelle colonne avant d'insérer des données";
        echo "<div><a href='http://localhost/DBManager/index.php?page=table&db=" . $_GET['db'] . "&table=" . $_GET['table'] 
        . "'>Retourner à la page précédente</a></div>";
    }
}