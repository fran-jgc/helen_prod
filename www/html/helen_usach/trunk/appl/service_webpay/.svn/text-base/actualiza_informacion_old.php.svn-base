<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../deudor/class_sql_deudor.php");
require_once(dirname(__FILE__)."/../deudor/class_direccion_deudor.php");
include("funciones.php");

class dw_direccion_web extends dw_direccion_deudor {
	function dw_direccion_web($tipo_direccion) {
		parent::dw_direccion_deudor($tipo_direccion, 'actualiza_direccion');
		$this->controls['COD_PAIS_'.$this->nom_tipo_direccion]->set_onChange("load_drop_down_web(this);");
		$this->controls['COD_REGION_'.$this->nom_tipo_direccion]->set_onChange("load_drop_down_web(this);");
		$this->controls['COD_CIUDAD_'.$this->nom_tipo_direccion]->set_onChange("load_drop_down_web(this);");
	}
}

class w_actualiza_direccion{
	var $dws = array();
	var $cod_deudor;

	function w_actualiza_direccion(){
		valida_inicio();
		$this->cod_deudor = session::get('cod_deudor');
		
		$t = new Template_appl("actualiza_informacion.html");
		
		$this->crea_dws();

		// draw
		$this->dws['dw_direccionP']->habilitar($t, true);
		$this->dws['dw_direccionC']->habilitar($t, true);
		
		$t->setVar("VE_COD_DEUDOR", $this->cod_deudor);
		session::set('w_actualiza_direccion', $this);
		print $t->toString();
	}
	function crea_dws() {
		// Direccion PARTICULAR
		$this->dws['dw_direccionP'] = new dw_direccion_web('P');
		$this->dws['dw_direccionP']->retrieve($this->cod_deudor);
		if ($this->dws['dw_direccionP']->row_count() == 0){
			$this->dws['dw_direccionP']->insert_row();
		}
		
		// Direccion COMERCIAL
		$this->dws['dw_direccionC'] = new dw_direccion_web('C');
		$this->dws['dw_direccionC']->retrieve($this->cod_deudor);
		if ($this->dws['dw_direccionC']->row_count() == 0){
			$this->dws['dw_direccionC']->insert_row();
		}
	}
	function save_direccion() {
		/////////////////////////
		// VMC, 16-02-2011
		// por ennredos de autoload ¿? no reconoce los datos de dw_direccion_deudor, por eso se vuelve a crear el objeto como medida parche
		$this->crea_dws();
		$this->dws['dw_direccionP']->entrable = true;
		$this->dws['dw_direccionC']->entrable = true;
		/////////////////////////
		
		$this->dws['dw_direccionP']->get_values_from_POST();
		$this->dws['dw_direccionC']->get_values_from_POST();
		
		$dir_p = $_REQUEST['DIRECCION_PARTICULAR_0'];
		$email = $_REQUEST['EMAIL_PARTICULAR_0'];
		$cod_pais = $_REQUEST['COD_PAIS_PARTICULAR_0'];
		$cod_region = $_REQUEST['COD_REGION_PARTICULAR_0'];
		$cod_ciudad = $_REQUEST['COD_CIUDAD_PARTICULAR_0'];
		$cod_comuna = $_REQUEST['COD_COMUNA_PARTICULAR_0'];
		$telefono = $_REQUEST['TELEFONO_PARTICULAR_0'];
		$celular = $_REQUEST['CELULAR_PARTICULAR_0'];
		
		
		$telefono                 = ($telefono=='') ? "is null" : "= '".$telefono."'";
		$cod_comuna  			  = ($cod_comuna=='') ? "is null" : "= '".$cod_comuna."'";
		
		$sql_1 = /*"SELECT count(*) CANTIDAD
				FROM DIRECCION 
				WHERE COD_DEUDOR = $this->cod_deudor
				AND TIPO_DIRECCION = 'P'
				AND DIRECCION = '$dir_p'
				AND COD_COMUNA  $cod_comuna
				AND COD_CIUDAD = $cod_ciudad
				AND COD_PAIS = $cod_pais
				AND EMAIL = '$email'
				AND TELEFONO $telefono
				AND CELULAR = '$celular'
				AND COD_DIRECCION = (
					SELECT COD_DIRECCION FROM 
					      (SELECT COD_DIRECCION 
					      FROM DIRECCION
					      WHERE COD_DEUDOR = $this->cod_deudor 
					      AND TIPO_DIRECCION = 'P' 
					      ORDER BY FECHA_DIRECCION DESC)
					      WHERE rownum = 1)";
						  */
						  
						  "SELECT count(*) CANTIDAD
				FROM DIRECCION 
				WHERE COD_DEUDOR = $this->cod_deudor
				AND TIPO_DIRECCION = 0
				AND DIRECCION = 0
				AND COD_COMUNA = 0
				AND COD_CIUDAD = 0
				AND COD_PAIS = 0
				AND EMAIL = 0
				AND TELEFONO = 0
				AND CELULAR = 0
				AND COD_DIRECCION = (
					SELECT COD_DIRECCION FROM 
					      (SELECT COD_DIRECCION 
					      FROM DIRECCION
					      WHERE COD_DEUDOR = $this->cod_deudor 
					      AND TIPO_DIRECCION = 'P' 
					      ORDER BY FECHA_DIRECCION DESC)
					      WHERE rownum = 1)";
		
		
		$dir_c = $_REQUEST['DIRECCION_COMERCIAL_0'];
		$email = $_REQUEST['EMAIL_COMERCIAL_0'];
		$cod_pais = $_REQUEST['COD_PAIS_COMERCIAL_0'];
		$cod_region = $_REQUEST['COD_REGION_COMERCIAL_0'];
		$cod_ciudad = $_REQUEST['COD_CIUDAD_COMERCIAL_0'];
		$cod_comuna = $_REQUEST['COD_COMUNA_COMERCIAL_0'];
		$telefono = $_REQUEST['TELEFONO_COMERCIAL_0'];
		$celular = $_REQUEST['CELULAR_COMERCIAL_0'];
  		
				
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result	= $db->build_results($sql_1);
		$count_p 	= $result[0]['CANTIDAD'];	
		
		$flag_1 = 0;
		
		if($count_p == 0){
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$db->BEGIN_TRANSACTION();
			if ($this->dws['dw_direccionP']->update($db,$this->cod_deudor)){
				$db->COMMIT_TRANSACTION();	
				$flag_1 = 1;
			}
		}
		
			$sql_0 = "SELECT COUNT(*) CANTIDAD
		   			 FROM DIRECCION 
		   			 WHERE TIPO_DIRECCION = 'C'
		   			 AND COD_DEUDOR = $this->cod_deudor";

			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$result	= $db->build_results($sql_0);
			$count_tiene_c 	= $result[0]['CANTIDAD'];
		
			
		if($dir_c == '' && $email == '' && $cod_pais == '' && $cod_region == '' && $cod_ciudad == '' && $cod_comuna == '' && $cod_comuna == '' && $telefono == '' && $celular == '' && $count_tiene_c == 0){
			$count_c = 1;	
		}else{
			$count_c = 0;
		}
		
		if($count_c == 0){
		$dir_c 					= ($dir_c=='') ? "is null" : "= '".$dir_c."'";
		$email					= ($email=='') ? "is null" : "= '".$email."'";
		$cod_pais				= ($cod_pais=='') ? "is null" : "= ".$cod_pais."";
		$cod_region				= ($cod_region=='') ? "is null" : "= ".$cod_region."";
		$cod_ciudad				= ($cod_ciudad=='') ? "is null" : "= ".$cod_ciudad."";
		$cod_comuna				= ($cod_comuna=='') ? "is null" : "= ".$cod_comuna."";
		$celular				= ($celular=='') ? "is null" : "= '".$celular."'";
		$telefono				= ($telefono=='') ? "is null" : "= '".$telefono."'";
				 
		$sql_2 = "SELECT count(*) CANTIDAD
				FROM DIRECCION 
				WHERE COD_DEUDOR = $this->cod_deudor
				AND TIPO_DIRECCION = 'C'
				AND DIRECCION $dir_c
				AND COD_COMUNA $cod_comuna
				AND COD_CIUDAD  $cod_ciudad
				AND COD_PAIS  $cod_pais
				AND EMAIL $email
				AND TELEFONO $telefono
				AND CELULAR $celular ";
				
			if($count_tiene_c > 0)
			{
			$sql_2 .= "AND COD_DIRECCION = (
					SELECT COD_DIRECCION FROM 
					      (SELECT COD_DIRECCION 
					      FROM DIRECCION
					      WHERE COD_DEUDOR = $this->cod_deudor 
					      AND TIPO_DIRECCION = 'P' 
					      ORDER BY FECHA_DIRECCION DESC)
					      WHERE rownum = 1)";


					      	
			}
			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result	= $db->build_results($sql_2);
		$count_c 	= $result[0]['CANTIDAD'];
		}
		
		$flag_2 = 0;
		
		if($count_c == 0){
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$db->BEGIN_TRANSACTION();
			if ($this->dws['dw_direccionC']->update($db,$this->cod_deudor)){
				$db->COMMIT_TRANSACTION();
				$flag_2 = 1;
			}
		}

		if(($flag_1 == 1 or $count_p != 0) && ($flag_2 == 1 or $count_c != 0)){
			header ('Location:'."error.php?error=2");
			return true;	 
		}
		
		$db->ROLLBACK_TRANSACTION();
		header ('Location:'."error.php?error=3");
		return false;
		//registra la Situacion de EGRESADO  o NO.
		/*
		$es_egesado = $_POST['SELECCION_OPCION'];
		$sp = 'spu_deudor';
		$param	= "'CHANGE_EGRESADO'
					,$this->cod_deudor
					, NULL
					, NULL
					, NULL
					, NULL
					, NULL
					, NULL
					, NULL
					, NULL
					, NULL
					, NULL
					,'$es_egesado'";
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);		
	    if ($db->EXECUTE_SP($sp, $param)){
	    	$db->BEGIN_TRANSACTION();
			if ($this->dws['dw_direccionP']->update($db,$this->cod_deudor)){
				if ($this->dws['dw_direccionC']->update($db,$this->cod_deudor)){
					$db->COMMIT_TRANSACTION();
					header ('Location:'."error.php?error=2");
					return true;
				}
			}
			$db->ROLLBACK_TRANSACTION();
			header ('Location:'."error.php?error=3");
			return false;
	    }*/
	}
}

if (isset($_POST['b_enviar'])){
	$w = session::get('w_actualiza_direccion');
	$w->save_direccion();
}
else{
	new w_actualiza_direccion();
}
?>