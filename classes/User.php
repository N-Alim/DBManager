<?php

class User
{
    protected string $nom;
    protected string $prenom = "";
    protected string $mail = "";
    protected DBHandler $connexion;

    public function setNom(string $nom) : void
    {
        $this->nom = $nom;
    }

    public function getNom() : string|bool
    {
        return isset($this->nom) ? $this->nom : false;
    }

    public function setPrenom(string $prenom) : void
    {
        $this->prenom = $prenom;
    }

    public function getPrenom() : string|bool
    {
        return isset($this->prenom) ? $this->prenom : false;
    }

    public function setMail(string $mail) : bool
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL))
        {
            $this->mail = $mail;
            return true;
        }

        else
            return false;
    }

    public function getMail() : string|bool
    {
        return isset($this->mail) ? $this->mail : false;
    }

    public function getConnexion() : string|bool
    {
        return isset($this->connexion) ? $this->connexion : false;
    }

    public function setConnexion(DBHandler $connexion) : void
    {
        $this->connexion = $connexion;
    }

    public function inscription(string $mdp)
    {
        $resultat = $this->connexion->getUsersWithMail($this->mail);

        if (count($resultat) !== 0) 
        {
            echo "<p>Votre compte est déjà enregistrée dans la base de données</p>";
        }

        else 
        {
            $this->connexion->createUser($this->prenom, $this->nom, $this->mail, password_hash($mdp, PASSWORD_DEFAULT));
            echo "<script>
            document.location.replace('http://localhost/DBManager/index.php?page=login');
            </script>";
        }
    }
}
