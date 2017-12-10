<?php
require_once 'vendor/autoload.php';
require_once 'conexion.php';
//inicializamos el framework
$app = new \Slim\Slim();

//cabeceras cors
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}
//iniciamos la sesión.
session_start();
/**
 * Función que se encarga de hacer login.
 * Si el login el correcto se graba en la sesión el parámetro autorizado a true
 * @var [type]
 */
$app->post('/login', function () use ($app, $db) {
    $_SESSION['autorizado'] = false;
    $json = $app->request->post('json');
    $data = json_decode($json, true);
    $sentencia = $db->prepare("SELECT * FROM usuarios WHERE (username= :username or email=:email)");
    $sentencia->bindParam(':email', $data['email']);
    $sentencia->bindParam(':username', $data['username']);
    $sentencia->execute();
    if ($sentencia->rowCount() == 1) {
        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);
        if (password_verify($data['password'], $usuario['password'])) {
            $_SESSION['autorizado'] = true;
            $usuario['password'] = "";
            $resultado = array(
      'status'=> 'success',
      'code' => 200,
      'data' => json_encode($usuario)
    );
        } else {
            $resultado = array(
        'status'=> 'error',
        'code' => 404,
        'message' => 'Contraseña incorrecta'
      );
        }
    } else {
        $resultado = array(
      'status'=> 'error',
      'code' => 404,
      'message' => 'No existe el usuario'
    );
    }
    echo json_encode($resultado);
});
/**
 * Función encargada de deslogar al usuario.
 * pasa el parámetro de sesión autorizado a false
 * @var [type]
 */
$app->post('/signout', function () use ($app) {
    $_SESSION['autorizado'] = false;
    $resultado = array(
'status'=> 'Desconectado',
'code' => 200,
'message' => 'Te has desconectado correctamente'
) ;
    echo json_encode($resultado);
});
/**
 * Función que comprueba si estamos logueados o no.
 * @var [type]
 */
$app->post('/status', function () use ($app) {
    $json = $app->request->post('json');
    $data = json_decode($json, true);
    if ($data['comprobar'] == "true") {
        $resultado = array(
          'status'=> 'Autorizado',
          'code' => 200,
          'data' => $_SESSION['autorizado']
          ) ;
    } else {
        $resultado = array(
          'status'=> 'No Autorizado',
          'code' => 404,
          'data' => $_SESSION['autorizado']
          ) ;
    }
    echo json_encode($resultado);
});
/**
 * Función que da de alta a un nuevo usuario.
 * @var [type]
 */
$app->post('/signup', function () use ($app, $db) {
    $_SESSION['autorizado'] = false;
    $json = $app->request->post('json');
    $data = json_decode($json, true);
    $sentencia = $db->prepare("SELECT * FROM usuarios WHERE username = :username OR email = :email");
    $sentencia->bindParam(':username', $data['username']);
    $sentencia->bindParam(':email', $data['email']);
    $sentencia->execute();
    if ($sentencia->rowCount() == 0) {
        $password = password_hash($data['password'], PASSWORD_DEFAULT, array('cost' => 12));
        $sentencia = $db->prepare("INSERT INTO usuarios VALUES(NULL, :username, :password, :name, :surname, :email)");
        $sentencia->bindParam(':username', $data['username']);
        $sentencia->bindParam(':password', $password);
        $sentencia->bindParam(':name', $data['name']);
        $sentencia->bindParam(':surname', $data['surname']);
        $sentencia->bindParam(':email', $data['email']);
        $sentencia->execute();
        $resultado = array(
      'status'=> 'error',
      'code' => 404,
      'message' => 'No se ha podido crear el usuario'
    ) ;
        if ($sentencia == 1) {
            $_SESSION['autorizado'] = true;
            $resultado = array(
        'status'=> 'success',
        'code' => 200,
        'message' => 'Usuario creado correctamente'
      );
        }
    } else {
        $resultado = array(
    'status'=> 'error',
    'code' => 403,
    'message' => 'El usuario ya existe'
  ) ;
    }
    echo json_encode($resultado);
});
/**
 * Función que actualiza la información de un usuario.
 * @var [type]
 */
$app->post('/update', function () use ($app, $db) {
    $_SESSION['autorizado'] = false;
    $json = $app->request->post('json');
    $data = json_decode($json, true);
    $sentencia = $db->prepare("SELECT * FROM usuarios WHERE id = :id");
    $sentencia->bindParam(':id', $data['id']);
    $sentencia->execute();
    if ($sentencia->rowCount() == 1) {
        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);
        if (password_verify($data['password'], $usuario['password'])) {
            $password = password_hash($data['passwordNew'], PASSWORD_DEFAULT, array('cost' => 12));
            $actualizacion = $db->prepare("UPDATE usuarios SET username = :username, password = :password, name = :name, surname = :surname, email = :email WHERE id = :id");
            $actualizacion->bindParam(':id', $data['id']);
            $actualizacion->bindParam(':username', $data['username']);
            $actualizacion->bindParam(':password', $password);
            $actualizacion->bindParam(':name', $data['name']);
            $actualizacion->bindParam(':surname', $data['surname']);
            $actualizacion->bindParam(':email', $data['email']);
            $actualizacion->execute();
            $resultado = array(
      'status'=> 'error',
      'code' => 404,
      'message' => 'No se ha actualizar el usuario'
    ) ;
            if ($actualizacion == 1) {
                $_SESSION['autorizado'] = true;
                $resultado = array(
        'status'=> 'success',
        'code' => 200,
        'message' => 'Usuario actualizado correctamente'
      );
            }
        }else{
          $resultado = array(
      'status'=> 'error',
      'code' => 405,
      'message' => 'Contraseña incorrecta'
    ) ;
        }
    } else {
        $resultado = array(
    'status'=> 'error',
    'code' => 403,
    'message' => 'El usuario no existe'
  ) ;
    }
    echo json_encode($resultado);
});
$app->run();
