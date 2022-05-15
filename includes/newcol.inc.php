<h1>Nouvelle colonne</h1>
<?php

if (!isset($_SESSION['login']))
{
    echo "<script>
    document.location.replace('http://localhost/DBManager');
    </script>";
}

else
{   
    $formCreator = new Form("post", "index.php?page=newcol&db=" . $_GET['db'] . "&table=". $_GET['table'], $_SESSION["token"]);
    $formCreator->getFormValues();
    $form = $formCreator->createFormFromCSV("./assets/frmFiles/newcol.csv");

    if (isset($_POST['envoi']))
    {
        if (hash_equals($_SESSION['token'], $_POST['token'])) 
        {
            $errorHand = new ErrorHandler;

            $formCreator->setValuesChecker($errorHand);
    
            $formCreator->checkValues();
    
            if ($formCreator->getErrorsCount() === 0)
            {
                $connHand = new DBHandler($_GET["db"]);
    
                $resultat = $connHand->getColumnWithName($formCreator->getValue("name"));
    
                if (count($resultat) !== 0) 
                {
                    echo "<p>Une colonne avec ce nom existe déjà</p>";
                }
        
                else 
                {
                    $connHand->addColumn(
                    [
                        "name" => $formCreator->getValue("name"),
                        "type" => $formCreator->getValue("type"),
                        "size" => $formCreator->getValue("size"),
                        "isNullable" => (($formCreator->getValue("isNullable") === null) ? true : false),
                        "hasDefaultValue" => (($formCreator->getValue("hasDefaultValue")  === null) ? true : false),
                        "defaultvalue" => $formCreator->getValue("defaultValue"),
                    ]);
                    echo "<script>
                    document.location.replace('http://localhost/DBManager/index.php?page=table&db=" . $_GET['db'] . "&table=". $_GET['table'] . "' );
                    </script>";
                }
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

    if (isset($_POST['envoi']))
    {

    }

    else
    {
        echo $form;
        echo "<div><a href='http://localhost/DBManager/index.php?page=table&db=" . $_GET['db'] . "&table=" . $_GET['table'] 
        . "'>Retourner à la page précédente</a></div>";
    }
}