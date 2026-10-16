<?php
ini_set('display_errors', 'ON');


include("class_helen_api.php");


class Api {
  var $USER = "helen";
  var $PASS = "H6a36K^5VoCw";

  function autenticar() {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      http_response_code(401);
      throw new Exception("Falta usuario");
    }
    else if (!isset($_SERVER['PHP_AUTH_PW'])) {
      http_response_code(401);
      throw new Exception("Falta password");
    }
    else if ($_SERVER['PHP_AUTH_USER'] <> $this->USER) {
      http_response_code(401);
      throw new Exception("Usuario desconocido");
    }
    else if ($_SERVER['PHP_AUTH_PW'] <> $this->PASS) {
      http_response_code(401);
      throw new Exception("Password invalido");
    }
  }
  function process() {
    $dir = __FILE__;
    $dir = substr($dir, 1);
    $dir = explode("/", $dir);
    
    $route 	= $_SERVER['REQUEST_URI'];   
    $route = substr($route, 1);
    $route = explode("?", $route);
    $route = explode("/", $route[0]);
    $route = array_diff($route, $dir);
    $route = array_values($route);

    $method = $_SERVER['REQUEST_METHOD'];
    $arr_json = null;
    try {
      $this->autenticar();
      
      if ($method=="GET")
        $arr_json = $this->doGet($route);
      else {
        // esta api solo tiene get
        http_response_code(405);
        throw new Exception('Metodo desconocido');
      }
    }  
    catch (Exception $e) {
      $arr_json = array('error' => $e->getMessage());
      return;
    }
    finally {
      header("content-Type:application/json");
      print json_encode($arr_json);
    }  
  }

  function doGet($route) {
    $api = new Helen_api();
    $count_funcion = 0;

    foreach ($route as $key => $valor) {
      switch ($valor) {
        case 'ruts':
          $count_funcion = 1;
          if (count($route) - $key !=2)
            throw new Exception("'ruts' debe tener 1 parametro ruts:string (lista ruts separados por coma)");

          $ruts = urldecode($route[$key + 1]);
          $res = $api->ruts($ruts);
          return array('resultado' => $res);
          break;

        case 'deudores':
          $count_funcion = 1;
          if (count($route) - $key !=2 and count($route) - $key !=3)
            throw new Exception("'deudores' debe tener 2 parametros (ruts:string, tables:string='ALL'");

          $ruts = urldecode($route[$key + 1]);
          if (count($route)==2)
            $tables = "";
          else
            $tables = urldecode($route[$key + 2]);
          $res = $api->deudores($ruts, $tables);
          return array('deudores' => $res);
          break;

        case 'deudas':
          $count_funcion = 1;
          if (count($route)!=2 - $key and count($route) - $key !=3)
            throw new Exception("'deudas' debe tener 2 parametros (deudas:string, tables:string='ALL'");

          $ruts = urldecode($route[$key + 1]);
          if (count($route)==2)
            $tables = "";
          else
            $tables = urldecode($route[$key + 2]);
          $res = $api->deudas($ruts, $tables);
          return array('deudas' => $res);
          break;

      }
    }

    if($count_funcion == 0){
      http_response_code(405);
      throw new Exception('Function desconocida');
    }

  }


}


$api = new Api();

$api->process();


// http://192.168.2.92/desarrolladores/vmelo_git_92/helen_userena_seven/trunk/appl/helen_api/api.php/deudores/16791371,1111,4343,3434