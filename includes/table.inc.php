<h1>Base de données <?php echo $_GET['db']?> - Table <?php echo $_GET['table']?></h1>
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
    echo $connHand->dropValue($_GET['delete']);

    echo "<script>
    document.location.replace('http://localhost/DBManager/index.php?page=table&db=" . $_GET['db'] ."&table=" . $_GET['table'] ."' );
    </script>";
}

else
{
    $connHand = new DBHandler($_GET['db']);
    echo $connHand->getColumnsAsTable();
    echo "<div><a href='http://localhost/DBManager/index.php?page=newcol&db=" . $_GET['db'] . "&table=". $_GET['table'] 
        ."'>Ajouter une colonne</a></div>";
    echo "<div><a href='http://localhost/DBManager/index.php?page=newval&db=" . $_GET['db'] . "&table=". $_GET['table'] 
        ."'>Insérer une valeur</a></div>";
    echo "<div><a href='http://localhost/DBManager/index.php?page=db&db=" . $_GET['db'] . "'>Retourner à la page précédente</a></div>";

}