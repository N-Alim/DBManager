<h1>Connexion</h1>
<?php

if (isset($_SESSION['login']))
{
    echo "<script>
    document.location.replace('http://localhost/DBManager/index.php?page=404');
    </script>";
}

else
{
    $formCreator = new Form("post", "index.php?page=login", $_SESSION["token"]);
    $formCreator->getFormValues();
    $form = $formCreator->createFormFromCSV("./assets/frmFiles/login.csv");

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
    
                $resultat = $connHand->getUsersWithMail($formCreator->getValue("mail"));
    
                if (count($resultat) === 0)
                {
                    echo "Pas de résultat avec votre login/mot de passe";
                }
    
                else
                {
                    $mdpRequete = $resultat[0]->password;
                    if (password_verify($formCreator->getValue("mdp"), $mdpRequete))
                    {
                        $_SESSION['login'] = true;
                        $_SESSION['id'] = $resultat[0]->id;
                        $_SESSION['nom'] = $resultat[0]->last_name;
                        $_SESSION['prenom'] = $resultat[0]->first_name;
                        echo "<script>
                        document.location.replace('http://localhost/DBManager/');
                        </script>";
                    }
    
                    else
                    {
                        echo "Bien tenté, mais non";
                    }
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