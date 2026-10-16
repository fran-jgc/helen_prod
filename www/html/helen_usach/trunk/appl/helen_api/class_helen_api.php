<?php

ini_set('display_errors', 1);
include(dirname(__FILE__)."/../../appl.ini");

if(K_CLIENTE == 'UACH')
	include(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
else
	include(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");

session::set('K_ROOT_DIR', K_ROOT_DIR);
session::set('K_CLIENTE', K_CLIENTE);

class Helen_api {    
	var $MAX = 100;
	
	function clean_result($a) {
		// El arreglo que se obtiene con db->buildresult viene con la data duplicada con key numeric y luego el mismo dato con key txt
		// esta funcion elimina las key numeric
		$result = [];
		foreach ($a as $row) {
			$r = [];
			foreach ($row as $k => $v) {
				if (! is_numeric($k))
					$r[$k] = $v;
			}
			$result[] = $r;
		}
		return $result;
	}

	function ruts($ruts) {
		/*
		$ruts : string con ruts separados por ","
				clasifica los ruts en "sin_deuda", "deuda_cobranza", "deuda_no_cobranza"
		*/
		$sin_deuda = [];
		$deuda_cobranza = [];
		$deuda_no_cobranza = [];
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$list_ruts = explode(",", $ruts);
		foreach($list_ruts as $r) {
			$r = trim($r);
			if (! is_numeric($r)) {
                http_response_code(404);
                return "El rut debe ser numeric (rut:$r)";	//rut invalido
            }
			$r = intval($r);

			// tiene deudas cobrables
			$sql = "select count(*) CANT
					from  deudor de, deuda da
					where de.rut = $r
					and da.cod_deudor = de.cod_deudor
					and da.cod_estado_deuda in (1,2)	--no exigible, exigible (deudas cobrables)
					";
			$result = $db->build_results($sql);
			$cant = $result[0]['CANT'];
			if ($cant > 0) {
				array_push($deuda_cobranza, $r);
				continue;
			}			

			// tiene deudas no cobrables
			$sql = "select count(*) CANT
							from  deudor de, deuda da
							where de.rut = $r
							and da.cod_deudor = de.cod_deudor
							and da.cod_estado_deuda not in (1,2)
					";
			$result = $db->build_results($sql);
			$cant = $result[0]['CANT'];
			if ($cant > 0) {
				array_push($deuda_no_cobranza, $r);
				continue;
			}			

			// rut no tiene deudas 
			array_push($sin_deuda, $r);
		}
			
		$sql = "select VALOR from parametro where cod_parametro = 3";
		$result = $db->build_results($sql);
		$cod_universidad = $result[0]['VALOR'];

		$resultado = new stdClass();
		$resultado->COD_UNIVERSIDAD = $cod_universidad;
		$resultado->sin_deuda = $sin_deuda;
		$resultado->deuda_cobranza = $deuda_cobranza;
		$resultado->deuda_no_cobranza = $deuda_no_cobranza;
		
        http_response_code(200);
		return $resultado;
	}

	function existe_tabla($tabla) {
		$tabla = strtoupper($tabla);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if (K_TIPO_BD=='oci')
			$sql = "SELECT COUNT(TABLE_NAME) as CANT
					FROM USER_TABLES 
					WHERE TABLE_NAME = '$tabla'";
		else //mssql
			$sql = "SELECT COUNT(TABLE_NAME) as CANT
					FROM INFORMATION_SCHEMA.TABLES 
					WHERE TABLE_TYPE='BASE TABLE' 
					AND TABLE_NAME = '$tabla'";

		$result = $db->build_results($sql);		
		return $result[0]['CANT'];
	}

	function get_tabla($t_name, $key, $nom_key="") {
		if (! is_numeric($key)) {
            http_response_code(400);
			return "La pk debe ser numeric (pk:$key)";	//key debe ser numeric
        }

		$t_name = strtoupper($t_name);
		if (! $this->existe_tabla($t_name)) {
            http_response_code(400);
			return "Tabla $t_name no existe";
        }
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		// lista de campos
		$db->query("select * from $t_name where 1=2");
		$fields = $db->get_fields();

		// arma sql con todos los campos NOM_TABLA asociados a $t_name consulta
		$sql_fields = "";
		$sql_from = "$t_name t";
		if ($nom_key != "")
			$sql_where = "t.$nom_key = $key";
		else
			$sql_where = "t.COD_$t_name = $key";
		$sql_left_join = "";
		$i = 1;
		foreach($fields as $f) {
			// las fechas las formatea yyyy-mm-dd hh:mm:ss
			if (strncmp($f->name, "FECHA",5)==0) {
				if (K_TIPO_BD=='oci')
					$sql_fields .= "to_char(t.$f->name, 'yyyy-mm-dd hh24:mi:ss') as $f->name,";
				else //mssql
					$sql_fields .= "convert(varchar, t.$f->name, 120) as $f->name,";
			}
			else
				$sql_fields .= "t.$f->name,";

			// verifica si es FK para ibncluir la tabla relacionada
			// se basa en que todas las FK comienzan con COD_
			$e = explode("_", $f->name);
			if (count($e) < 2)
				continue;
			if ($e[0] != "COD")
				continue;
				
			array_shift($e);
			$t = implode("_", $e);		
			if ($t==$t_name)
				continue;

			if (! $this->existe_tabla($t) || $t=='DEUDA' || $t=='SUB_BOLETA' || $t=='DOCUMENTO_PAGO' || $t=='ANALITICO_COMPROBANTE' || $t=='PLAN_PAGO' || $t=='ABONO' || $t=='DESCUENTO' || $t=='CONDONACION')
				continue;			

			if ($t=='DEUDOR')	// no existe NOM_DEUDOR y para todo efecto es mas util RUT
				$sql_fields .= "t$i.RUT,";
			else
				$sql_fields .= "t$i.NOM_$t,";
			
			$sql_left_join .= " left outer join $t t$i on t$i.$f->name = t.$f->name";
			$i++;
		}

        // agregar saldos si tabla es DEUDA o PLAN_PAGO
		if ($t_name=='DEUDA') {
			// agrega la moneda
			$sql_left_join .= " left outer join tipo_deuda on tipo_deuda.cod_tipo_deuda = t.cod_tipo_deuda ";
			$sql_left_join .= " left outer join moneda on moneda.cod_moneda = tipo_deuda.cod_moneda";
			$sql_fields .= "moneda.COD_MONEDA,";
			$sql_fields .= "moneda.SIMBOLO as NOM_MONEDA,";
			if (K_TIPO_BD=='oci')
				$sql_fields .= "f_deuda_saldo_componente(t.COD_DEUDA, 1, sysdate) SALDO_SVP,
								f_deuda_saldo_componente(t.COD_DEUDA, 2, sysdate) SALDO_MORA,
								f_deuda_saldo_componente(t.COD_DEUDA, 3, sysdate) SALDO_VIGENTE,
								f_deuda_saldo_componente(t.COD_DEUDA, 4, sysdate) SALDO_FUTURA,";
			else //sqlserver
				$sql_fields .= "dbo.f_deuda_saldo_componente(t.COD_DEUDA, 1, getdate()) SALDO_SVP,
								dbo.f_deuda_saldo_componente(t.COD_DEUDA, 2, getdate()) SALDO_MORA,
								dbo.f_deuda_saldo_componente(t.COD_DEUDA, 3, getdate()) SALDO_VIGENTE,
								dbo.f_deuda_saldo_componente(t.COD_DEUDA, 4, getdate()) SALDO_FUTURA,";
		}
		elseif ($t_name=='PLAN_PAGO') {
			if (K_TIPO_BD=='oci')
				$sql_fields .= "f_pp_saldo_capital(t.COD_PLAN_PAGO, sysdate) SALDO_CAPITAL,
								f_pp_saldo_interes_normal(t.COD_PLAN_PAGO, sysdate) SALDO_INTERES,
								f_pp_saldo_mora(t.COD_PLAN_PAGO, sysdate) SALDO_MORA,
								f_pp_saldo_multa(t.COD_PLAN_PAGO, sysdate) SALDO_MULTA,
								f_pp_saldo_plan_pago(t.COD_PLAN_PAGO, sysdate) SALDO_PLAN_PAGO,";
			else //sqlserver
				$sql_fields .= "dbo.f_pp_saldo_capital(t.COD_PLAN_PAGO, getdate()) SALDO_CAPITAL,
								dbo.f_pp_saldo_interes_normal(t.COD_PLAN_PAGO, getdate()) SALDO_INTERES,
								dbo.f_pp_saldo_mora(t.COD_PLAN_PAGO, getdate()) SALDO_MORA,
								dbo.f_pp_saldo_multa(t.COD_PLAN_PAGO, getdate()) SALDO_MULTA,
								dbo.f_pp_saldo_plan_pago(t.COD_PLAN_PAGO, getdate()) SALDO_PLAN_PAGO,";
		}

		if (substr($sql_fields, -1)==",")
			$sql_fields = substr($sql_fields, 0, strlen($sql_fields)-1);


		$sql = "select $sql_fields
				from $sql_from $sql_left_join
				where $sql_where";

		$result = $db->build_results($sql);	
		$result = $this->clean_result($result); // dejo solo los registros con key texto y elimino lo key numeric
		http_response_code(200);
        return $result;
	}
		
	function deuda($cod_deuda, $ve_tables) {
		if (! is_numeric($cod_deuda))
			return "cod_deuda debe ser numeric (cod_deuda:$cod_deuda)";	//deuda invalido

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		if ($ve_tables=='ALL')
			$ve_tables = 'DEUDA,PAGARE,CRUZADO,PLAN_PAGO,RESOLUCION_ADMINISTRATIVA,ABONO,DESCUENTO,ABONO_FICTICIO,DESCUENTO_FICTICIO,SUSPENSION,PRORROGA,DEUDA_CONDONACION';
			
		$ve_tables = strtoupper($ve_tables);
		$a_tables = explode(",", $ve_tables);
			
		// recorrer deudas
		$sql = "select COD_DEUDA, COD_ESTADO_DEUDA from deuda where cod_deuda = $cod_deuda";
		$da = $db->build_results($sql);
		$COD_ESTADO_DEUDA = $da[0]["COD_ESTADO_DEUDA"];
		if (array_search("DEUDA", $a_tables)!==false) {
			$deuda = $this->get_tabla("DEUDA", $cod_deuda);
		}
		else {
			$deuda = new stdClass();
			$deuda->COD_DEUDA = $cod_deuda;
			$deuda->COD_ESTADO_DEUDA = $COD_ESTADO_DEUDA;
		}

		$res_deuda = new stdClass();
		$res_deuda->DEUDA = $deuda;

		// todas las tablas que se quieran rescatar deben estar relacionadas con COD_DEUDA
		foreach($a_tables as $t_name) {
			if ($t_name=='DEUDA')
				continue;

			$t_dato = $this->get_tabla($t_name, $cod_deuda, "COD_DEUDA");
			$res_deuda->$t_name = $t_dato;

			if ($t_name=="DEUDA_CONDONACION") {
				$sql = "select COD_CONDONACION from DEUDA_CONDONACION where COD_DEUDA = $cod_deuda";
				$result = $db->build_results($sql);
				if (count($result)) {
					$cod_condonacion = $result[0]['COD_CONDONACION'];
					$condonacion = $this->get_tabla("CONDONACION", $cod_condonacion);
					$res_deuda->CONDONACION = json_decode($condonacion);
				}
			}
		}
				
        http_response_code(200);
		return $res_deuda;
	}	
	
	function deudas($cod_deudas, $tables) {
		$cod_deudas = explode(",", $cod_deudas);
		$count = count($cod_deudas);
		if ($count > $this->MAX) {
            http_response_code(413);
			return "'Demasiados deudas: $count, maximo $this->MAX";
        }
		
		$tables = str_replace(" ", "", $tables);
		$tables = strtoupper($tables);
		$deudas = [];
		foreach ($cod_deudas as $r) {
			$d =  $this->deuda($r, $tables);
			$status_code = http_response_code();
			if ($status_code == 200) 
				$deuda = $d;
			else {
				$deuda = new stdClass();
				$deuda->status_code = $status_code;
				$deuda->content = $d;

				$res_deuda = new stdClass();
				$res_deuda->DEUDA = $deuda;
				$deuda = $res_deuda;
			}
			$deudas[] = $deuda;
		}
			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select VALOR from parametro where cod_parametro = 3";
		$result = $db->build_results($sql);
		$cod_universidad = $result[0]['VALOR'];

		http_response_code(200);
		$resultado = new stdClass();
		$resultado->COD_UNIVERSIDAD = $cod_universidad;
		$resultado->deudas = $deudas;
		return $resultado;
	}

	function deudor($rut, $ve_tables) {
		if (! is_numeric($rut))
			return "Rut debe ser numeric (rut:$rut)";	//rut invalido

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select COD_DEUDOR from deudor where rut = $rut";
		$result = $db->build_results($sql);
		if (count($result) <> 1 ) {
            http_response_code(400);
			return "Deudor inexistente (rut:$rut)";// rut inexistente
        }
		$cod_deudor = $result[0]['COD_DEUDOR'];

		// armar resultado
		$resultado = new stdClass();
		
		if ($ve_tables=='ALL')
			$ve_tables = 'DEUDOR,DIRECCION,DEUDA,PAGARE,CRUZADO,PLAN_PAGO,RESOLUCION_ADMINISTRATIVA,ABONO,DESCUENTO,ABONO_FICTICIO,DESCUENTO_FICTICIO,SUSPENSION,PRORROGA,DEUDA_CONDONACION';
			
		$ve_tables = strtoupper($ve_tables);
		$a_tables = explode(",", $ve_tables);
		// deudor
		if (array_search("DEUDOR", $a_tables)!==false)
			$deudor = $this->get_tabla("DEUDOR", $cod_deudor);
		else {
			$deudor = new stdClass();
			$deudor->COD_DEUDOR = $cod_deudor;
		}
		$resultado->DEUDOR = $deudor;
			
		// direccion	(caso especial DIRECCION)
		if (array_search("DIRECCION", $a_tables)!==false) {
			if (K_TIPO_BD=='oci')
				$sql = "select f_get_direccion_deudor($cod_deudor, 'P') COD_DIRECCION from dual";
			else
				$sql = "select dbo.f_get_direccion_deudor($cod_deudor, 'P') COD_DIRECCION";
			$result = $db->build_results($sql);
			$cod_direccion = $result[0]['COD_DIRECCION'];
			$direccion = $this->get_tabla("DIRECCION", $cod_direccion);
			$resultado->DIRECCION = $direccion;
		}

		// recorrer deudas
		$deudas = [];
		$sql = "select COD_DEUDA from deuda where cod_deudor = $cod_deudor";
		$result = $db->build_results($sql);
		$cod_deudas = "";
		foreach ($result as $da)
			$cod_deudas .= $da["COD_DEUDA"].",";
		$cod_deudas = substr($cod_deudas, 0, -1);
		$tables = str_replace('DIRECCION,', '', str_replace('DEUDOR,', '', $ve_tables));
		$deudas = $this->deudas($cod_deudas, $tables);
		$resultado->deudas = $deudas->deudas;
        http_response_code(200);
		return $resultado;
	}	
		
	function deudores($ruts, $tables) {
		$ruts = explode(",", $ruts);
		$count = count($ruts);
		if ($count > $this->MAX) {
            http_response_code(413);
			return "'Demasiados ruts: $count, maximo $this->MAX";
        }
		
		$tables = str_replace(" ", "", $tables);
		$tables = strtoupper($tables);
		$deudores = [];
		foreach ($ruts as $r) {
			$d =  $this->deudor($r, $tables);
			$status_code = http_response_code();
			if ($status_code == 200) 
				$deudor = $d;
			else {
				$deudor = new stdClass();
				$deudor->status_code = $status_code;
				$deudor->content = $d;

				$res_deudor = new stdClass();
				$res_deudor->DEUDOR = $deudor;
				$deudor = $res_deudor;
			}
			$deudores[] = $deudor;
		}
			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select VALOR from parametro where cod_parametro = 3";
		$result = $db->build_results($sql);
		$cod_universidad = $result[0]['VALOR'];

		http_response_code(200);
		$resultado = new stdClass();
		$resultado->COD_UNIVERSIDAD = $cod_universidad;
		$resultado->deudores = $deudores;
		return $resultado;
	}
}