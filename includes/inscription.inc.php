<h1>Inscription</h1>
<?php

if (isset($_SESSION['login']))
{
    echo "<script>
    document.location.replace('http://localhost/DBManager/index.php?page=404');
    </script>";
}


else
{
    $formCreator = new Form("post", "index.php?page=inscription", "multipart/form-data", $_SESSION["token"]);
    $formCreator->getFormValues();
    $form = $formCreator->createFormFromCSV("./assets/frmFiles/inscription.csv");

    if (isset($_POST['envoi']))
    {
        if (hash_equals($_SESSION['token'], $_POST['token'])) 
        {
            $errorHand = new ErrorHandler;

            $formCreator->setValuesChecker($errorHand);
    
            $formCreator->checkValues();
    
    
            if ($formCreator->getErrorsCount() === 0) 
            {
                $user = new User();
                $user->setNom($formCreator->getValue("nom"));
                $user->setPrenom($formCreator->getValue("prenom"));
                $user->setMail($formCreator->getValue("mail"));
    
                $connHand = new DBHandler();
                
                $user->setConnexion($connHand);
    
                $user->inscription($formCreator->getValue("mdp"));
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
