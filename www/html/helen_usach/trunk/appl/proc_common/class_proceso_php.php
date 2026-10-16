<?php
require_once(dirname(__FILE__)."/../../appl.ini");

class proceso_php {
	
	static function connect() {
		if (K_TIPO_BD=="oci")
			$conn = oci_connect(K_USER, K_PASS, K_SERVER);
		else if (K_TIPO_BD=="mssql") {
			$conn = mssql_connect(K_SERVER, K_USER, K_PASS, true);
			mssql_select_db (K_BD, $conn); 
		}	
			
		return $conn;
	}
	static function f_cancela_proceso($conn, $cod_ejecucion_proceso) {
		if (K_TIPO_BD=="oci") {
			$sql = "select f_ep_cancela_proceso($cod_ejecucion_proceso) CANCELA from DUAL";
			$query_id = oci_parse($conn, $sql);
			oci_execute($query_id, OCI_DEFAULT);
			
			$row = oci_fetch_array($query_id, OCI_ASSOC+OCI_RETURN_NULLS);
		}
		else if (K_TIPO_BD=="mssql") {
			$sql = "select dbo.f_ep_cancela_proceso($cod_ejecucion_proceso) CANCELA";
			$res = mssql_query($sql, $conn);
			$row = mssql_fetch_array($res);
		}
		if ($row['CANCELA'] == 'S')
			return true;
			
		return false;
	}
	static function ejecucion_proceso($conn, $operacion, $cod_ejecucion_proceso, $value, $arch='') {
		$arch = $arch =='' ? 'null' : "'$arch'";
		if (K_TIPO_BD=="oci") {
			$sql = "BEGIN spu_ejecucion_proceso('$operacion', $cod_ejecucion_proceso, $value, null, null, $arch); END;";
			$query_id = oci_parse($conn, $sql);
			oci_execute($query_id, OCI_DEFAULT);
		}
		else if (K_TIPO_BD=="mssql") {
			$sql = "exec spu_ejecucion_proceso '$operacion', $cod_ejecucion_proceso, $value, null, null, $arch";
			$res = mssql_query($sql, $conn);
		}
	}	
}
?>