<?php
    //Démarrer la session
    session_start();
    //Détruire la session 
    session_destroy();
    //Détruire tous les variable de session
    session_unset();
    //redirection  vers la page d'acceuil
    header('Location: ../index.php');
    exit();

?>