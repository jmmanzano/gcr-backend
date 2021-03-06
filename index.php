<?php
require_once 'vendor/autoload.php';
require_once 'conexion.php';

//cabeceras cors
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}
// Iniciamos la sesión
session_start();

// if (true) {
    if ($_SESSION['autorizado']) {
    // inicializamos el framework
    $app = new \Slim\Slim();

    /**
     * Función que devuelve todas las bases de datos del servidor.
     * @var [type]
     */
    $app->get('/db', function () use ($app, $dbnames) {
        $sql = "SHOW DATABASES";
        $consulta = $dbnames->query($sql);
        $bases = array();
        while ($base = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $bases[] = $base;
        }
        $resultado = array(
     'status'=> 'success',
     'code' => 200,
     'data' => $bases
   ) ;
        echo json_encode($resultado);
    });
    /**
     * Función que lista todos los datos de todos los socios de la tabla socios.
     * @var [type]
     */
    $app->get('/socios', function () use ($app, $db) {
        $sql = "select * from socios order by numero";
        $consulta = $db->query($sql);
        $socios = array();
        while ($socio = $consulta->fetch(PDO::FETCH_ASSOC)) {
            $socios[] = $socio;
        }
        $resultado = array(
     'status'=> 'success',
     'code' => 200,
     'data' => $socios
   ) ;
        echo json_encode($resultado);
    });
    /**
     * Función que lista todos los nombres de los usuarios de la tabla socios
     * @var [type]
     */
    $app->get('/nombres', function () use ($app, $db) {
        $sql = "select numero, concat(nombre, ' ',IFNULL(apellido1, ''), ' ', IFNULL(apellido2, '')) as nombre, domiciliado from socios order by numero asc";
        $consulta = $db->query($sql);
        $socios = array();
        if ($consulta->rowCount() > 0) {
            while ($socio = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $socios[] = $socio;
            }
            $resultado = array(
          'status'=> 'success',
          'code' => 200,
          'data' => $socios
        ) ;
        } else {
            $resultado = array(
      'status'=> 'error',
      'code' => 404,
      'message' => 'No se han encontrados nombres'
    ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que actualiza los datos de un socio.
     * @var [type]
     */
    $app->post('/socio-actualiza', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $domiciliado = (int) $data['domiciliado'];
        $no_activo = (int) $data['noActivo'];
        $alta = $data['fAlta'];
        if ($alta == null) {
            $alta = "NULL";
        } else {
            $alta = '"'.$alta.'"';
        }
        $baja = $data['fBaja'];
        if ($baja == null) {
            $baja = "NULL";
        } else {
            $baja = '"'.$baja.'"';
        }
        $sql = "UPDATE socios SET nombre = \"{$data['nombre']}\", ".
                       " apellido1 =  \"{$data['apellido1']}\", ".
                       " apellido2 =  \"{$data['apellido2']}\", ".
                       " domiciliado =  {$domiciliado}, ".
                       " DNI =  \"{$data['dni']}\", ".
                       " direccion =  \"{$data['direccion']}\", ".
                       " cod_postal =  \"{$data['cPostal']}\", ".
                       " poblacion =  \"{$data['localidad']}\", ".
                       " cod_prov =  \"{$data['cProvincia']}\", ".
                       " alta =  {$alta}, ".
                       " no_activo =  {$no_activo}, ".
                       " baja =  {$baja}, ".
                       " propiedad =  {$data['propiedad']}, ".
                       " observaciones =  \"{$data['observaciones']}\", ".
                       " provincias_cod_prov =  \"{$data['cProvincia']}\" ".
                       " WHERE numero = {$data['numero']}";
        $salida = $db->exec($sql);
        $resultado = array(
      'status'=> 'error',
      'code' => 404,
      'message' => 'No se ha podido modificar el socio'
    ) ;
        if ($salida == 1) {
            $resultado = array(
        'status'=> 'success',
        'code' => 200,
        'message' => 'Socios modificado correctamente'
      ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que inserta un nuevo socio en la base de datos.
     * @var [type]
     */
    $app->post('/socio-inserta', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $domiciliado = (int) $data['domiciliado'];
        $no_activo = (int) $data['noActivo'];
        $alta = $data['fAlta'];
        if ($alta == null) {
            $alta = "NULL";
        } else {
            $alta = '"'.$alta.'"';
        }
        $baja = $data['fBaja'];
        if ($baja == null) {
            $baja = "NULL";
        } else {
            $baja = '"'.$baja.'"';
        }
        $sql = "INSERT INTO socios VALUES( {$data['numero']} , \"{$data['nombre']}\", ".
                       " \"{$data['apellido1']}\", ".
                       " \"{$data['apellido2']}\", ".
                       "   {$domiciliado}, ".
                       " \"{$data['dni']}\", ".
                       " \"{$data['direccion']}\", ".
                       " \"{$data['cPostal']}\", ".
                       " \"{$data['localidad']}\", ".
                       " \"{$data['cProvincia']}\", ".
                       " {$alta}, ".
                       " {$no_activo}, ".
                       " {$baja}, ".
                       " {$data['propiedad']}, ".
                       " \"{$data['observaciones']}\", ".
                       " \"{$data['cProvincia']}\" )";
        $salida = $db->exec($sql);
        $resultado = array(
      'status'=> 'error',
      'code' => 404,
      'message' => 'No se ha podido crear el socio'
    ) ;
        if ($salida == 1) {
            $resultado = array(
        'status'=> 'success',
        'code' => 200,
        'message' => 'Socios creado correctamente'
      ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que obtiene todos los datos de un socio por su número de socio
     * @var [type]
     */
    $app->get('/socio/:id', function ($id) use ($app, $db) {
        $sql = "select * from socios where numero = ".$id;
        $consulta = $db->query($sql);
        $socio;
        $resultado = array(
           'status'=> 'error',
           'code' => 404,
           'message' => 'Socio no disponible'
         ) ;
        if ($consulta->rowCount() == 1) {
            $socio = $consulta->fetch(PDO::FETCH_ASSOC);
            $resultado = array(
             'status'=> 'success',
             'code' => 200,
             'data' => json_encode($socio)
           ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que devuelve el valor del número de socio siguiente para
     * generar una nueva entrada
     * @var [type]
     */
    $app->get('/socio-sig', function () use ($app, $db) {
        $sql = "select max(numero) + 1 as siguiente  from socios ";
        $consulta = $db->query($sql);
        $socio;
        $resultado = array(
     'status'=> 'error',
     'code' => 404,
     'message' => 'Socio no disponible'
   ) ;
        if ($consulta->rowCount() == 1) {
            $socio = $consulta->fetch(PDO::FETCH_ASSOC);
            $resultado = array(
       'status'=> 'success',
       'code' => 200,
       'data' => json_encode($socio)
     ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que obtiene el nombre de un socio por su id.
     * @var [type]
     */
    $app->get('/socio_nombre/:id', function ($id) use ($app, $db) {
        $sql = "select concat(nombre, ' ',IFNULL(apellido1, ''), ' ', IFNULL(apellido2, '')) as nombre from socios where numero = ".$id;
        $consulta = $db->query($sql);
        $socio;
        $resultado = array(
     'status'=> 'error',
     'code' => 404,
     'message' => 'Socio no disponible'
   ) ;
        if ($consulta->rowCount() == 1) {
            $socio = $consulta->fetch(PDO::FETCH_ASSOC);
            $resultado = array(
       'status'=> 'success',
       'code' => 200,
       'data' => json_encode($socio)
     ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * función que obtiene los datos de una provincia por su id.
     * @var [type]
     */
    $app->get('/provincia/:id', function ($id) use ($app, $db) {
        $sql = "select * from provincias where cod_prov = '".$id."'";
        $consulta = $db->query($sql);
        $provincia;
        $resultado = array(
           'status'=> 'error',
           'code' => 404,
           'message' => 'Provincia no disponible'
         ) ;
        if ($consulta->rowCount() == 1) {
            $provincia = $consulta->fetch(PDO::FETCH_ASSOC);
            $resultado = array(
             'status'=> 'success',
             'code' => 200,
             'data' => json_encode($provincia)
           ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que devuelve un array de localidades en función de su código postal.
     * @var [type]
     */
    $app->get('/localidad/:id', function ($id) use ($app, $db) {
        $sql = "select * from poblaciones where cod_postal = ".$id;
        $consulta = $db->query($sql);
        $localidad;
        $resultado = array(
           'status'=> 'error',
           'code' => 404,
           'message' => 'Localidad no disponible'
         ) ;
        if ($consulta->rowCount() >= 1) {
            $localidades = array();
            while ($localidad = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $localidades[] = $localidad;
            }
            $resultado = array(
               'status'=> 'success',
               'code' => 200,
               'data' => json_encode($localidades)
             ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que obtiene las parcelas de un socio por su id.
     * @var [type]
     */
    $app->get('/socio_parcelas/:id', function ($id) use ($app, $db) {
        $sql = "select * from parcelas where socio = ".$id;
        $consulta = $db->query($sql);
        $parcelas;
        $resultado = array(
           'status'=> 'error',
           'code' => 404,
           'message' => 'Parcelas no disponibles'
         ) ;
        $consulta = $db->query($sql);

        if ($consulta->rowCount() >= 1) {
            $parcelas = array();
            while ($parcela = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $parcelas[] = $parcela;
            }
            $resultado = array(
             'status'=> 'success',
             'code' => 200,
             'data' => json_encode($parcelas)
           ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que obtiene los teléfonos de un socio por su id.
     * @var [type]
     */
    $app->get('/socio_telefonos/:id', function ($id) use ($app, $db) {
        $sql = "select * from telefonos where socio = ".$id;
        $consulta = $db->query($sql);
        $telefonos;
        $resultado = array(
           'status'=> 'error',
           'code' => 404,
           'message' => 'Telefonos no disponibles'
         ) ;
        $consulta = $db->query($sql);

        if ($consulta->rowCount() >= 1) {
            $telefonos = array();
            while ($telefono = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $telefonos[] = $telefono;
            }
            $resultado = array(
             'status'=> 'success',
             'code' => 200,
             'data' => json_encode($telefonos)
           ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que obtiene los teléfonos de los socios pasados por post.
     * @var [type]
     */
    $app->post('/socios_telefonos', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $cadena = "(";
        for ($i=0; $i < sizeof($data); $i++) {
            if ($i == 0) {
                $cadena .= $data[$i];
            } else {
                $cadena .= ",".$data[$i];
            }
        }
        $cadena .=")";
        $sql = "select * from telefonos where socio in ".$cadena;
        $consulta = $db->query($sql);
        $telefonos;
        $resultado = array(
     'status'=> 'error',
     'code' => 404,
     'message' => 'Telefonos no disponibles'
   ) ;
        $consulta = $db->query($sql);

        if ($consulta->rowCount() >= 1) {
            $telefonos = array();
            while ($telefono = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $telefonos[] = $telefono;
            }
            $resultado = array(
       'status'=> 'success',
       'code' => 200,
       'data' => json_encode($telefonos)
     ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que inserta un nuevo teléfono.
     * @var [type]
     */
    $app->post('/telefono', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $sql = "INSERT INTO telefonos VALUES(NULL, ".
    "{$data['socio']},".
    "{$data['telefono']},".
    "{$data['activo']},".
    "\"{$data['fecha_alta']}\",".
    "\"{$data['fecha_baja']}\"".")";

        $salida = $db->exec($sql);
        $resultado = array(
    'status'=> 'error',
    'code' => 404,
    'message' => 'Registro NO insertado'
  ) ;
        if ($salida == 1) {
            $resultado = array(
      'status'=> 'success',
      'code' => 200,
      'message' => 'Registro insertado correctamente'
    ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que actualiza los datos de un teléfono por su id.
     * @var [type]
     */
    $app->post('/telefono-actualiza/:id', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $sql = "UPDATE telefonos SET ".
    "socio = {$data['socio']},".
    "telefono = {$data['telefono']},".
    "activo = {$data['activo']},".
    "fecha_alta = \"{$data['fecha_alta']}\",".
    "fecha_baja = \"{$data['fecha_baja']}\""."
    WHERE id = ". $id;

        $salida = $db->exec($sql);
        $resultado = array(
    'status'=> 'error',
    'code' => 404,
    'message' => 'Registro NO insertado'
  ) ;
        if ($salida == 1) {
            $resultado = array(
      'status'=> 'success',
      'code' => 200,
      'message' => 'Registro actualizado correctamente'
    ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que borra un teléfono por su id.
     * @var [type]
     */
    $app->post('/telefono-borra/:id', function ($id) use ($app, $db) {
        $sql = "DELETE FROM telefonos WHERE id= ".$id;
        $salida = $db->exec($sql);
        $resultado = array(
    'status'=> 'error',
    'code' => 404,
    'message' => 'Registro No borrado'
  ) ;
        if ($salida == 1) {
            $resultado = array(
      'status'=> 'success',
      'code' => 200,
      'message' => 'Registro borrado correctamente'
    ) ;
        }
        echo json_encode($resultado);
    });

    /**
     * Función que inserta una petición en la tabla horas.
     * @var [type]
     */
    $app->post('/hora', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $sql = "INSERT INTO horas VALUES(NULL,".
  "{$data['numero']},".
  "{$data['horas']})";
        $salida = $db->exec($sql);
        $resultado = array(
    'status'=> 'error',
    'code' => 404,
    'message' => 'Registro NO insertado'
  ) ;
        if ($salida == 1) {
            $resultado = array(
      'status'=> 'success',
      'code' => 200,
      'message' => 'Registro insertado correctamente'
    ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que actualiza una petición por su id.
     * @var [type]
     */
    $app->post('/actualiza-hora/:id', function ($id) use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $sql = "UPDATE horas SET ".
                "numero = {$data['numero']}, ".
                "horas = {$data['horas']} ".
                "WHERE id= ".$id;

        $salida = $db->exec($sql);
        $resultado = array(
          'status'=> 'error',
          'code' => 404,
          'message' => 'Registro No actualizado'
        ) ;
        if ($salida == 1) {
            $resultado = array(
              'status'=> 'success',
              'code' => 200,
              'message' => 'Registro actualizado correctamente'
            ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que borra una petición por su id.
     * @var [type]
     */
    $app->post('/borra-hora/:id', function ($id) use ($app, $db) {
        $sql = "DELETE FROM horas WHERE id= ".$id;

        $salida = $db->exec($sql);
        $resultado = array(
    'status'=> 'error',
    'code' => 404,
    'message' => 'Registro No borrado'
  ) ;
        if ($salida == 1) {
            $resultado = array(
      'status'=> 'success',
      'code' => 200,
      'message' => 'Registro borrado correctamente'
    ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que recupera todas las peticiones.
     * @var [type]
     */
    $app->get('/horas', function () use ($app, $db) {
        $sql = "SELECT horas.id, socios.numero, concat(socios.nombre, ' ',IFNULL(socios.apellido1, ''), ' ', IFNULL(socios.apellido2, '')) AS nombre, horas.horas FROM socios, horas WHERE socios.numero = horas.numero ORDER BY horas.id DESC ";
        $consulta = $db->query($sql);
        $horas = array();
        if ($consulta->rowCount() > 0) {
            while ($hora = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $horas[] = $hora;
            }
            $resultado = array(
        'status'=> 'success',
        'code' => 200,
        'data' => $horas
      ) ;
        } else {
            $resultado = array(
        'status'=> 'error',
        'code' => 404,
        'message' => 'No se han encontrado riegos'
      ) ;
        }
        echo json_encode($resultado);
    });

    /**
     * Función que devuelve los nombres de los socios con recibos pendientes
     * @var [type]
     */
    $app->get('/socios-pendientes', function () use ($app, $db) {
        $sql = "SELECT numero, nombre, sum(euros) as pendiente FROM devoluciones where pagado = 0 group by numero";
        $consulta = $db->query($sql);
        $socios = array();
        if ($consulta->rowCount() > 0) {
            while ($socio = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $socios[] = $socio;
            }
            $resultado = array(
          'status'=> 'success',
          'code' => 200,
          'data' => $socios
        ) ;
        } else {
            $resultado = array(
          'status'=> 'error',
          'code' => 404,
          'message' => 'No hay recibos pendientes'
        ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que devuelve todos los recibos de un socio por su numero de socio.
     * @var [type]
     */
    $app->post('/socios-pendientes/:id', function ($id) use ($app, $db) {
        $sql = "SELECT id, numero, nombre, euros, concepto, pagado as pagado, fecha_pago, observaciones FROM devoluciones where numero =".$id;
        $consulta = $db->query($sql);
        $recibos = array();
        if ($consulta->rowCount() > 0) {
            while ($recibo = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $recibos[] = $recibo;
            }
            $resultado = array(
          'status'=> 'success',
          'code' => 200,
          'data' => $recibos
        ) ;
        } else {
            $resultado = array(
          'status'=> 'error',
          'code' => 404,
          'message' => 'No hay recibos pendientes para el socio '.$id
        ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que obtiene los datos de un recibo por su id.
     * @var [type]
     */
    $app->post('/recibo', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $sql = "SELECT id, numero, nombre, euros, concepto, pagado as pagado, fecha_pago, observaciones FROM devoluciones where id =".$data['id'];

        $consulta = $db->query($sql);
        $recibo = $consulta->fetch(PDO::FETCH_ASSOC);
        $resultado = array(
        'status'=> 'error',
        'code' => 404,
        'message' => 'No se ha encontrado el recibo'
     ) ;
        if ($consulta->rowCount() == 1) {
            $resultado = array(
        'status'=> 'success',
        'code' => 200,
        'data' => $recibo
      ) ;
        }
        echo json_encode($resultado);
    });

    /**
     * Función que obtiene la cantidad pendiente de pago de un socio por su numero de socio.
     * @var [type]
     */
    $app->get('/cantidad-pendiente/:id', function ($id) use ($app, $db) {
        $sql = "select sum(euros) as pendiente from devoluciones where numero = ".$id." and pagado = 0";
        $consulta = $db->query($sql);
        $cantidad;
        $resultado = array(
     'status'=> 'error',
     'code' => 404,
     'message' => 'Cantidad no disponible'
   ) ;
        if ($consulta->rowCount() == 1) {
            $cantidad = $consulta->fetch(PDO::FETCH_ASSOC);
            $resultado = array(
       'status'=> 'success',
       'code' => 200,
       'data' => json_encode($cantidad)
     ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que inserta un nuevo recibo impagado.
     * @var [type]
     */
    $app->post('/inserta-recibo', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $pagado = $data['pagado'];
        if ($pagado) {
            $pagado = 1;
        }
        if (!$pagado) {
            $pagado = 0;
        }
        $fecha = $data['fecha_pago'];
        if ($fecha == null) {
            $fecha = "NULL";
        } else {
            $fecha = '"'.$fecha.'"';
        }
        $sql = "INSERT INTO devoluciones (numero, nombre, euros, concepto, pagado) VALUES( ".
     "{$data["numero"]}, \"{$data["nombre"]}\", {$data["euros"]} , ".
     "\"{$data["concepto"]}\" ,	".
     "{$pagado} )";
        $salida = $db->exec($sql);
        $resultado = array(
     'status'=> 'error',
     'code' => 404,
     'message' => 'Registro No insertado'
   ) ;
        if ($salida == 1) {
            $resultado = array(
       'status'=> 'success',
       'code' => 200,
       'message' => 'Registro insertado correctamente'
     ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que actualiza la información de un recibo.
     * @var [type]
     */
    $app->post('/actualiza-recibo', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $pagado = $data['pagado'];
        if ($pagado) {
            $pagado = 1;
        }
        if (!$pagado) {
            $pagado = 0;
        }
        $fecha = $data['fecha_pago'];
        if ($fecha == null) {
            $fecha = "NULL";
        } else {
            $fecha = '"'.$fecha.'"';
        }
        $sql = "UPDATE devoluciones SET ".
    "euros = {$data["euros"]} , ".
    "concepto = \"{$data["concepto"]}\" ,	".
    "pagado = {$pagado} ,	".
    "fecha_pago = {$fecha} ,	".
    "observaciones = \"{$data["observaciones"]}\" ".
    "WHERE id = {$data["id"]}" ;
        $salida = $db->exec($sql);
        $resultado = array(
    'status'=> 'error',
    'code' => 404,
    'message' => 'Registro No actualizado'
  ) ;
        if ($salida == 1) {
            $resultado = array(
      'status'=> 'success',
      'code' => 200,
      'message' => 'Registro actualizado correctamente'
    ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que genera una nueva remesa.
     * Primero genera la entrada en la tabla remesas, y luego vuelca en la tabla
     * riegos los datos de la tabla horas, truncando esta.
     * @var [type]
     */
    $app->post('/genera-remesa', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $fecha = $data['fecha_fin'];
        $concepto = $data['concepto'];
        $sql = "insert into remesas (fecha_fin, concepto) values (\"".$fecha."\",\"".$concepto."\")";
        $salida = $db->exec($sql);
        $resultado = array(
          'status'=> 'error',
          'code' => 404,
          'message' => 'Remesa No generada'
        ) ;

        if ($salida == 1) {
            $sql = "select MAX(remesa) as siguiente from remesas";
            $remesa_num = $db->query($sql);
            $remesa = $remesa_num->fetch(PDO::FETCH_ASSOC);
            $insercion =  $db->exec("insert into riegos (numero, horas, remesa) select numero, horas, \"{$remesa['siguiente']}\" from horas");
            $remesa = $db->exec("truncate table horas");
            $resultado = array(
              'status'=> 'success',
              'code' => 200,
              'message' => 'Remesa generada correctamente'
        );
        }
        echo json_encode($resultado);
    });
    /**
     * Función que devuelve un array con la información de todas las remesas.
     * @var [type]
     */
    $app->post('/remesas', function () use ($app, $db) {
        $sql = "SELECT * FROM remesas ORDER BY remesa DESC";
        $consulta = $db->query($sql);
        $remesas = array();
        if ($consulta->rowCount() > 0) {
            while ($remesa = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $remesas[] = $remesa;
            }
            $resultado = array(
         'status'=> 'success',
         'code' => 200,
         'data' => $remesas
       ) ;
        } else {
            $resultado = array(
          'status'=> 'error',
          'code' => 404,
          'message' => 'No hay remesas para mostrar'
        ) ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que devuelve la información completa de una remesa determinada.
     * @var [type]
     */
    $app->post('/remesa', function () use ($app, $db) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $sql = "select socios.numero, concat(socios.nombre, ' ',IFNULL(socios.apellido1, ''), ' ', IFNULL(socios.apellido2, '')) as nombre, sum(riegos.horas) as horas from socios, riegos where socios.numero = riegos.numero and riegos.remesa = ".$data['remesa']." group by socios.numero";
        $consulta = $db->query($sql);
        $remesas = array();
        if ($consulta->rowCount()>0) {
            while ($remesa = $consulta->fetch(PDO::FETCH_ASSOC)) {
                $remesas[] = $remesa;
            }
            $resultado = array(
         'status'=> 'success',
         'code' => 200,
         'data' => $remesas
       ) ;
        } else {
            $resultado = array(
              'status'=> 'error',
              'code' => 404,
              'message' => 'No se han encontrado datos para la remesa '.$data['remesa']
            ) ;
        }
        echo json_encode($resultado);
    });

    /**
     * función que se encarga de enviar SMS a través del programa sms gateway
     * de android.
     * Lee los datos de configuración contenidos en config/config.json
     * y con curl hace una llamada al servidor de sms del teléfono.
     * @var [type]
     */
    $app->post('/sms', function () use ($app) {
        $configFile = file_get_contents("config/config.json");
        $config = json_decode($configFile, true);
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        $ch = curl_init();
        $url = 'http://'.$config['ipTelefono'].':'.$config['puertoTelefono'].'/sendsms?phone=+34'.$data['telefono'].'&text='.urlencode($config['nombre'].' '.$data['texto']);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $head = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $resultado = array(
    'status'=> 'error',
    'code' => 404,
    'message' => 'Mensaje No Enviado'
  ) ;
        if ($httpCode == 200) {
            $resultado = array(
          'status'=> 'success',
          'code' => 200,
          'message' => 'Mensaje enviado') ;
        }
        echo json_encode($resultado);
    });
    /**
     * Función que guarda el fichero de configuración config.json.
     * @var [type]
     */
    $app->post('/config', function () use ($app) {
        $json = $app->request->post('json');
        $data = json_decode($json, true);
        unlink('config/config.json');
        try {
            $fh = fopen("config/config.json", 'w');
            fwrite($fh, json_encode($data));
            fclose($fh);
            $resultado = array(
          'status'=> 'success',
          'code' => 200,
          'message' => 'Archivo guardado'
          ) ;
            echo json_encode($resultado);
        } catch (Exception $e) {
            $resultado = array(
          'status'=> 'error',
          'code' => 404,
          'message' => 'No se ha podido guardar el fichero de configuración'
          ) ;
            echo json_encode($resultado);
        }
    });
    /**
     * Función que devuelve los datos contenidos en el fichero de configuración.
     * @var [type]
     */
    $app->post('/getConfig', function () use ($app) {
        try {
            $cadena = file_get_contents("config/config.json");
            $resultado = array(
          'status'=> 'success',
          'code' => 200,
          'data' => $cadena
          ) ;
            echo json_encode($resultado);
        } catch (Exception $e) {
            $resultado = array(
          'status'=> 'error',
          'code' => 404,
          'message' => 'No se ha podido leer el fichero de configuración'
          ) ;
            echo json_encode($resultado);
        }
    });
    $app->run();
} else { //mensaje que debe de aparecer si no se está logueado.
    $resultado = array(
    'status'=> 'error',
    'code' => 401,
    'message' => 'No está logueado'
    ) ;
    echo json_encode($resultado);
}
