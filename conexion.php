<?php

    $host = 'localhost:3306';
    $user = 'root';
    $contra = '';
    $db = 'bd_website';

    $conexion = @mysqli_connect($host,$user,$contra,$db);


    if(!$conexion){
        echo "Error en la conexion";
    }

?>