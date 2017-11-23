<?php
// cambiar host, usuario, contrasela y gcr por los campos apropiados
try {
    $dbnames = new PDO('mysql:host=localhost', 'usuario', 'contraseña');
    $dbnames->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbnames->exec('SET CHARACTER SET UTF8');
} catch (PDOException $e) {
    die('Error en línea '.$e->getMessage());
}
try {
    $db = new PDO('mysql:host=localhost; dbname=gcr', 'usuario', 'contraseña');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec('SET CHARACTER SET UTF8');
} catch (PDOException $e) {
    die('Error en línea '.$e->getMessage());
}

 ?>
