<?php

class DBHandler 
{
    private string $serverName = "localhost";
    private string $userName = "root";
    private string $database;
    private string $userPassword = "";
    private object $connexion;

    public function __construct(string $dbName="db_manager")
    {
        $this->database = $dbName;
    }

    public function createDatabase($newDatabase)
    {     
        $this->connectToDB();
        try
        {            
            $sql = $this->connexion->prepare("CREATE DATABASE " . $newDatabase);
            $sql->execute();
                
            $this->connexion->beginTransaction();

            $sql2 = $this->connexion->prepare("
            INSERT INTO dbs(name, user_id)
            VALUES (?, ?)");
            $sql2->execute(array($newDatabase, $_SESSION["id"]));

            $this->connexion->commit();
        }

        catch (PDOException $e)
        {
            // $this->connexion->rollBack();     
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function dropDatabase($database)
    {     
        $this->connectToDB();
        try
        {            
            $sql = $this->connexion->prepare("DROP DATABASE " . $database);
            $sql->execute();

            $this->connexion->beginTransaction();

            $sql2 = $this->connexion->prepare("
            DELETE FROM dbs WHERE name=?");

            $sql2->execute(array($database));

            $this->connexion->commit();
        }

        catch (PDOException $e)
        {
            // $this->connexion->rollBack();     
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function getUserDatabasesAsTable()
    {
        $this->connectToDB();

        try
        {
            $sql = $this->connexion->prepare("SELECT * FROM dbs WHERE user_id = ?");
            $sql->execute(array($_SESSION["id"]));

            $resultat = $sql->fetchAll(PDO::FETCH_OBJ);

            $table = "<table class='table'><thead><tr>
                <th>Bases de données</th> 
                <th>Actions</th>
                    </tr>
                </thead>";

                // <th>Created_at</th>
                // <th>Modified_at</th>
            
            $tbody = "<tbody>";

            foreach ($resultat as $row) 
            {
                $tbody .= "<tr><td>$row->name</td>
                    <td>
                    <a href='http://localhost/DBManager/index.php?page=db&db=$row->name'>Afficher</a>
                    <a href='http://localhost/DBManager/index.php?page=dblist&delete=$row->name'>Supprimer</a>
                    </td></tr>";
            }

            return $table . $tbody . "</tbody></table>";
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function getDatatablesAsTable()
    {
        $this->connectToDB();

        try
        {
            $sql = $this->connexion->prepare("SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA=? ");
            $sql->execute(array($_GET["db"]));

            $resultat = $sql->fetchAll(PDO::FETCH_OBJ);

            $table = "<table class='table'><thead><tr>
                <th>Tables</th> 
                <th>Actions</th>
                    </tr>
                </thead>";
            
            $tbody = "<tbody>";

            foreach ($resultat as $row) 
            {
                $tbody .= "<tr><td>$row->TABLE_NAME</td>
                    <td>
                    <a href='http://localhost/DBManager/index.php?page=table&db=" . $_GET["db"] . "&table=$row->TABLE_NAME'>Afficher</a>
                    <a href='http://localhost/DBManager/index.php?page=db&db=" . $_GET['db'] ."&delete=$row->TABLE_NAME'>Supprimer</a>
                    </td></tr>";
            }

            return $table . $tbody . "</tbody></table>";
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function getColumnsAsTable()
    {
        $this->connectToDB();

        try
        {
            $sql = $this->connexion->prepare("SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?");
            $sql->execute(array($_GET["db"], $_GET["table"]));

            $columns = $sql->fetchAll(PDO::FETCH_OBJ);

            $sql2 = $this->connexion->prepare("SELECT *
            FROM " . $_GET["table"]);
            $sql2->execute();

            $values = $sql2->fetchAll(PDO::FETCH_ASSOC);

            $table = "<table class='table'><thead><tr>";

            foreach ($columns as $column) 
            {
                $table .= "<th>$column->COLUMN_NAME</th>";
            }

            $table .="<th>Action</th>
                    </tr>
                </thead>";
            
            $tbody = "<tbody>";

            foreach ($values as $value) 
            {
                $tbody .= "<tr>";

                foreach ($columns as $column) 
                {
                    $colName = $column->COLUMN_NAME;
                    $tbody .= "<th>$value[$colName]</th>";
                }

                $tbody .= "<td>
                    <a href='http://localhost/DBManager/index.php?page=table&db=" . $_GET['db'] . "&table=" 
                        . $_GET['table'] . "&delete=" . $value["id"] . "'>Supprimer</a>
                    </td></tr>";
            }

            return $table . $tbody . "</tbody></table>";
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function getDatabaseWithName(string $name)
    {
        $this->connectToDB();

        try
        {
            $sql = $this->connexion->prepare("SELECT * FROM dbs WHERE name=?");
            $sql->execute(array($name));

            $resultat = $sql->fetchAll(PDO::FETCH_OBJ);

            return $resultat;
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function connectToDB()
    {
        try
        {
            $this->connexion = new PDO("mysql:host=$this->serverName;dbname=$this->database", $this->userName, $this->userPassword);
            $this->connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e)
        {
            die("Erreur :  " . $e->getMessage());
        }
    }

    public function createTable(string $tableName)
    {
        $this->connectToDB();

        try
        {
            $request = "CREATE TABLE " . $tableName . " (id int(11) NOT NULL AUTO_INCREMENT,";

            $request .= "PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

            $this->connexion->beginTransaction();

            $this->connexion->exec($request);
        }

        catch (PDOException $e)
        {
            $this->connexion->rollBack();     
            die("Erreur : " . $e->getMessage());
        }    

    }

    public function dropTable(string $tableName)
    {
        $this->connectToDB();
        try
        {            
            $sql  = $this->connexion->prepare("
            DROP TABLE " . $tableName);

            $sql->execute();
        }

        catch (PDOException $e)
        {
            // $this->connexion->rollBack();     
            die("Erreur : " . $e->getMessage());
        }  
    }

    public function getDatatableWithName(string $tableName)
    {
        $this->connectToDB();

        try
        {
            $sql = $this->connexion->prepare("SELECT TABLE_NAME
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?");
            $sql->execute(array($_GET["db"], $tableName));

            $resultat = $sql->fetchAll(PDO::FETCH_OBJ);

            return $resultat;
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function addColumn(array $parameters)
    {
        $this->connectToDB();

        try
        {
            $request = "ALTER TABLE " . $_GET["table"] . " ADD "
            . $parameters["name"] . " " 
            . $parameters["type"] . "("
            . $parameters["size"] . ") "
            . (($parameters["isNullable"]) ? "" : "NOT NULL ");

            if (in_array($parameters["type"], ["varchar, text"]))
            {
                $request .=  (($parameters["hasDefaultValue"]) ? 
                    "DEFAULT " . (($parameters["defaultValue"] === null) ? 
                        ($parameters["isNullable"] ? "NULL" : "''") 
                    : "'" . $parameters["defaultValue"] . "'") 
                : "") . ";";
            }

            else if (in_array($parameters["type"], ["int, double"]))
            {
                $request .=  (($parameters["hasDefaultValue"]) ? 
                    "DEFAULT " . (($parameters["defaultValue"] === null) ? 
                        ($parameters["isNullable"] ? "NULL" : "0") 
                    : floatval($parameters["defaultValue"])) 
                : "") . ";";
            }

            // else 
            // {
            //     $sql->bindParam(':name', $parameters["name"]);
            //     $sql->bindParam(':type', $parameters["type"]);
            //     $sql->bindParam(':size', $parameters["size"], PDO::PARAM_INT);
            //     $sql->bindParam(':isNullable', ($parameters["isNullable"]) ? "" : "NOT NULL ");
            //     $sql->bindParam(':defaultValue', 
            //         (($parameters["hasDefaultValue"]) ? 
            //         "DEFAULT " . (($parameters["defaultValue"] === null) ? 
            //             ($parameters["isNullable"] ? "NULL" : "'0000-00-00 00:00:00'") 
            //         : (($parameters["defaultValue"] === "current") ? "current_timestamp()" : "'" . $parameters["defaultValue"] . "'"))
            //     : "");
            // }

            $this->connexion->exec($request);
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    

    }


    

    public function getColumnWithName($colName)
    {
        $this->connectToDB();

        try
        {
            $sql = $this->connexion->prepare("SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?");
            $sql->execute(array($_GET["db"], $_GET["table"], $colName));

            return $sql->fetchAll(PDO::FETCH_OBJ);
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function createUser(string $firstName, string $lastName, string $mail, string $mdp)
    {
        $this->connectToDB();

        try
        {
            $this->connexion->beginTransaction();

            $sql = $this->connexion->prepare("
            INSERT INTO users(first_name, last_name, mail, password)
            VALUES (?, ?, ?, ?)");
            $sql->execute(array($firstName, $lastName, $mail, $mdp));

            $this->connexion->commit();
        }

        catch (PDOException $e)
        {
            $this->connexion->rollBack();     
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function getUsersWithMail(string $mail)
    {
        $this->connectToDB();

        try
        {
            $sql = $this->connexion->prepare("SELECT * FROM users WHERE mail=?");
            $sql->execute(array($mail));

            $resultat = $sql->fetchAll(PDO::FETCH_OBJ);

            return $resultat;
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function dropValue($idRow)
    {
        $this->connectToDB();

        try
        {            
            $sql  = $this->connexion->prepare("DELETE FROM " . $_GET["table"] . " WHERE id = ?");
            $sql->execute(array($idRow));
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }  
    }

    public function getTableColumnDetails()
    {
        $this->connectToDB();

        try
        {
            $sql = $this->connexion->prepare("SELECT COLUMN_NAME, DATA_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?");
            $sql->execute(array($_GET["db"], $_GET["table"]));

            return $sql->fetchAll(PDO::FETCH_OBJ);
        }

        catch (PDOException $e)
        {
            die("Erreur : " . $e->getMessage());
        }    
    }

    public function addRow(array $parameters)
    {
        $this->connectToDB();

        try
        {
            $request = "INSERT INTO " . $_GET["table"] . "(";
            $endRequest = "(";
            $requestParams = array();

            foreach ($parameters as $colName => $value) 
            {
                $request .= $colName . ",";
                $endRequest .= "?,";
                array_push($requestParams, $value);
            }

            $this->connexion->beginTransaction();

            $sql = $this->connexion->prepare(substr($request, 0, -1) . ") VALUES " . substr($endRequest, 0, -1) . ");");
            $sql->execute($requestParams);

            $this->connexion->commit();
        }

        catch (PDOException $e)
        {
            $this->connexion->rollBack();     
            die("Erreur : " . $e->getMessage());
        }    
    }
}