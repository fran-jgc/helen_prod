<?php
ob_start(); // Evita errores de encabezado por salidas previas
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(dirname(__FILE__) . "/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR') . "appl.ini");

ini_set('memory_limit', '4000M');
ini_set('max_execution_time', 1200);
ini_set('mssql.connect_timeout', '400');
ini_set('mssql.timeout', '400');

class wo_archivo_texto extends w_base {
    function __construct() {
        parent::__construct('', '2521');
        $this->work_directory = getcwd();
    }

    function log($mensaje) {
        $hora = date("Y-m-d H:i:s");
        file_put_contents("log.txt", "[$hora] $mensaje\n", FILE_APPEND);
    }

    function print_txt() {
        $this->log("===== NUEVA EJECUCIÓN =====");
        $this->log("POST: " . print_r($_POST, true));

        if (!isset($_POST['COD_USUARIO']) || empty($_POST['COD_USUARIO'])) {
            $this->alert("Código de usuario no recibido.");
            $this->log("ERROR: COD_USUARIO no recibido");
            exit;
        }

        $cod_usuario = $_POST['COD_USUARIO'];
        $archivo = "DEUDORES_NOMINA_TGR_" . K_CLIENTE . ".txt";
        $ruta_dcto = "documentos/" . $archivo;

        if (!is_dir('documentos') || !is_writable('documentos')) {
            $this->alert("La carpeta 'documentos' no existe o no tiene permisos de escritura.");
            $this->log("ERROR: carpeta 'documentos' inaccesible");
            exit;
        }

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

        // Conexión real al procedimiento almacenado
        if (K_TIPO_BD == "mssql") {
            $sql = "EXEC spi_nomina_tgr $cod_usuario";
            $result = $db->build_results($sql);
            $this->log("Ejecutado: $sql");
        } elseif (K_TIPO_BD == "oci") {
            $db->EXECUTE_SP('spi_nomina_tgr', "$cod_usuario");
            $sql = "SELECT LINEA FROM TEMP_NOMINA_TGR ORDER BY LINEA";
            $result = $db->build_results($sql);
            $this->log("Ejecutado: EXECUTE_SP y luego SELECT");
        } else {
            $this->alert("Tipo de base de datos no soportado.");
            $this->log("ERROR: K_TIPO_BD no soportado");
            exit;
        }

        $this->log("Resultado desde BD:\n" . print_r($result, true));

        if (!$result || count($result) === 0) {
            $this->alert("No se obtuvieron resultados desde la base de datos.");
            $this->log("SP ejecutado, pero sin resultados.");
            exit;
        }

        if (!$handle = fopen($ruta_dcto, 'w')) {
            $this->alert("No se puede abrir el archivo para escritura: $archivo");
            $this->log("ERROR: fopen falló para $ruta_dcto");
            exit;
        }

        foreach ($result as $i => $fila) {
            $linea = $fila[0];
            if ($i < count($result) - 1) {
                $linea .= "\r\n";
            }

            if (fwrite($handle, $linea) === FALSE) {
                fclose($handle);
                $this->alert("No se puede escribir en el archivo: $archivo");
                $this->log("ERROR: fwrite falló en la línea $i");
                exit;
            }
        }

        fclose($handle);
        $this->log("Archivo creado correctamente en: $ruta_dcto");

        if (!file_exists($ruta_dcto)) {
            $this->alert("Archivo no fue creado en el servidor.");
            $this->log("ERROR: archivo no encontrado tras creación.");
            exit;
        }

        // Envío del archivo al navegador
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$archivo\"");
        readfile($ruta_dcto);

        $this->log("Archivo enviado al navegador.");
        ob_end_flush();
    }
}

$w = new wo_archivo_texto();
if (isset($_POST['b_file_xls'])) {
    $w->print_txt();
}
?>



