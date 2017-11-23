<?php
// if (true) { // descomentar para desarrollo local con el frontal
if ($_SESSION['autorizado']) { // descomentar para produccion
    $data = json_decode($json, true);
    unlink('config.json');
    try {
      $fh = fopen("config.json", 'w');
      fwrite($fh, $data);
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


}else{
  $resultado = array(
    'status'=> 'error',
    'code' => 404,
    'message' => 'No está autentificado'
    ) ;
  echo json_encode($resultado);
}
