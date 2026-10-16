<?php
require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
require_once(session::get('K_ROOT_DIR')."appl.ini");	

class dw_ejecucion_anterior extends datawindow {
	function __construct() {
		if(K_TIPO_BD == "oci"){
			$sql = "select to_char(FECHA_EJECUCION_PROCESO, 'dd/mm/yyyy hh24:mi:ss') LAST_FECHA_EJECUCION_PROCESO
							,to_char(FECHA_TERMINO, 'dd/mm/yyyy hh24:mm:ss') LAST_FECHA_TERMINO
							,Trim(To_Char(trunc(mod((FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24, 24)), '9900')) || ':' ||
	 						 Trim(To_Char(trunc( mod( (FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24*60, 60)), '00')) || ':'  ||
	 						 Trim(To_Char(trunc( mod( (FECHA_TERMINO - FECHA_EJECUCION_PROCESO)*24*60*60, 60)), '00')) LAST_DURACION 						
							,CANCELA_PROCESO LAST_CANCELA_PROCESO
							,U.NOM_USUARIO LAST_NOM_USUARIO
							,decode(CANCELA_PROCESO, 'N', '', '    (Proceso cancelado por : ' || U2.NOM_USUARIO || ')') LAST_NOM_USUARIO_CANCELA
							,TOT_REGISTRO LAST_TOT_REGISTRO
							,ACTUAL_REGISTRO LAST_ACTUAL_REGISTRO
							,Decode(NVL(To_Char(FECHA_TERMINO, 'yyyy'), 'none'), 'none', 'none', '')  LAST_DISPLAY_EJECUCION
							,f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'E') LAST_CANT_ERROR
							,f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'W') LAST_CANT_WARNING
							,CASE WHEN (TOT_REGISTRO  = 0) THEN 100
					             WHEN (ACTUAL_REGISTRO >= TOT_REGISTRO) THEN 100
					             ELSE (ACTUAL_REGISTRO / TOT_REGISTRO) * 100
					         END ANTERIOR_AVANCE
					        ,E.ARCHIVO_RESULTADO
					from EJECUCION_PROCESO E, USUARIO U, USUARIO U2 
					where E.COD_EJECUCION_PROCESO = f_ep_ejecucion_anterior({KEY1})					
					  and U.COD_USUARIO = E.COD_USUARIO
					  and U2.COD_USUARIO (+)= E.COD_USUARIO_CANCELA";
		}
		else if(K_TIPO_BD == "mssql"){
			$sql ="select	convert(varchar(10), FECHA_EJECUCION_PROCESO, 103)+' '+ convert(varchar(2), datepart(hh, FECHA_EJECUCION_PROCESO))+':'+ convert(varchar(2), datepart(month, FECHA_EJECUCION_PROCESO))+':'+ convert(varchar(2), datepart(ss, FECHA_EJECUCION_PROCESO)) LAST_FECHA_EJECUCION_PROCESO
							,convert(varchar(10), FECHA_TERMINO, 103)+' '+ convert(varchar(2), datepart(hh, FECHA_TERMINO))+':'+ convert(varchar(2), datepart(month, FECHA_TERMINO))+':'+ convert(varchar(2), datepart(ss, FECHA_TERMINO)) LAST_FECHA_TERMINO	
							,dbo.f_aud_proceso(FECHA_EJECUCION_PROCESO, FECHA_TERMINO) LAST_DURACION
							,CANCELA_PROCESO LAST_CANCELA_PROCESO
							,U.NOM_USUARIO LAST_NOM_USUARIO
							,case CANCELA_PROCESO
								when 'N' then '' 
								else '    (Proceso cancelado por : ' + U2.NOM_USUARIO + ')' 
							end LAST_NOM_USUARIO_CANCELA
							,TOT_REGISTRO LAST_TOT_REGISTRO
							,ACTUAL_REGISTRO LAST_ACTUAL_REGISTRO
							,case datepart(year, FECHA_TERMINO)
								when null then 'none'
								else  ''
							end LAST_DISPLAY_EJECUCION		
							,dbo.f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'E') LAST_CANT_ERROR
							,dbo.f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'W') LAST_CANT_WARNING
							,CASE WHEN (TOT_REGISTRO  = 0) THEN 100
							 WHEN (ACTUAL_REGISTRO >= TOT_REGISTRO) THEN 100
							 ELSE (ACTUAL_REGISTRO / TOT_REGISTRO) * 100
							END ANTERIOR_AVANCE
							,E.ARCHIVO_RESULTADO
					from	EJECUCION_PROCESO E left outer join USUARIO U2 on E.COD_USUARIO_CANCELA = U2.COD_USUARIO, USUARIO U
					where	E.COD_EJECUCION_PROCESO = dbo.f_ep_ejecucion_anterior({KEY1})					
					and 	U.COD_USUARIO = E.COD_USUARIO";
		}
		parent::__construct($sql);
	}
	function retrieve_bd($sql) {
		parent::retrieve_bd($sql);
		if ($this->row_count()==0) {
			$this->insert_row();
			$this->set_item(0, 'LAST_CANT_ERROR', 0);
			$this->set_item(0, 'LAST_CANT_WARNING', 0);
		}
	}
}

class dw_ejecucion_actual extends datawindow {
	var $nom_w_proceso;
	
	function __construct($nom_w_proceso) {
		$this->nom_w_proceso = $nom_w_proceso; 
		
		if(K_TIPO_BD == "oci"){
		$sql = "select to_char(FECHA_EJECUCION_PROCESO, 'dd/mm/yyyy hh24:mi:ss') ACTUAL_FECHA_EJECUCION_PROCESO
						,to_char(FECHA_TERMINO, 'dd/mm/yyyy hh24:mi:ss') ACTUAL_FECHA_TERMINO
						,To_Char(trunc(mod((sysdate - FECHA_EJECUCION_PROCESO)*24, 24)), '9900') || ':' ||
 						 To_Char(trunc( mod( (sysdate - FECHA_EJECUCION_PROCESO)*24*60, 60)), '00') || ':'  ||
 						 To_Char(trunc( mod( (sysdate - FECHA_EJECUCION_PROCESO)*24*60*60, 60)), '00') ACTUAL_DURACION
						,CANCELA_PROCESO ACTUAL_CANCELA_PROCESO
						,U.NOM_USUARIO ACTUAL_NOM_USUARIO
						,U2.NOM_USUARIO ACTUAL_NOM_USUARIO_CANCELA
						,TOT_REGISTRO ACTUAL_TOT_REGISTRO
						,ACTUAL_REGISTRO ACTUAL_ACTUAL_REGISTRO
						,Decode(NVL(To_Char(FECHA_TERMINO, 'yyyy'), 'none'), 'none', 'none', '')  ACTUAL_DISPLAY_EJECUCION
						,f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'E') ACTUAL_CANT_ERROR
						,f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'W') ACTUAL_CANT_WARNING
						,CASE WHEN (TOT_REGISTRO  = 0) THEN 100
				             WHEN (ACTUAL_REGISTRO >= TOT_REGISTRO) THEN 100
				             ELSE (ACTUAL_REGISTRO / TOT_REGISTRO) * 100
				         END ACTUAL_AVANCE
				        ,COD_EJECUCION_PROCESO
				        ,E.ARCHIVO_RESULTADO
				from EJECUCION_PROCESO E, USUARIO U, USUARIO U2 
				where E.COD_EJECUCION_PROCESO = f_ep_ejecucion_actual({KEY1})					
				  and U.COD_USUARIO = E.COD_USUARIO
				  and U2.COD_USUARIO (+)= E.COD_USUARIO_CANCELA";
		}
		else if(K_TIPO_BD == "mssql"){
			$sql = "select convert(varchar(8),FECHA_EJECUCION_PROCESO, 103)+ ' ' + convert(varchar(2),datepart(hh,FECHA_EJECUCION_PROCESO)) + ':' + convert(varchar(2),datepart(mm,FECHA_EJECUCION_PROCESO)) + ':' +  convert(varchar(2),datepart(ss,FECHA_EJECUCION_PROCESO)) ACTUAL_FECHA_EJECUCION_PROCESO
						,case FECHA_TERMINO
							when null then null
							else convert(varchar(8), FECHA_TERMINO, 103)+ ' ' + convert(varchar(2),datepart(hh,FECHA_TERMINO)) + ':' + convert(varchar(2),datepart(mm,FECHA_TERMINO)) + ':' +  convert(varchar(2),datepart(ss,FECHA_TERMINO)) 
						 end ACTUAL_FECHA_TERMINO
						, dbo.f_aud_proceso(FECHA_EJECUCION_PROCESO, getdate()) ACTUAL_DURACION
						,CANCELA_PROCESO ACTUAL_CANCELA_PROCESO
						,U.NOM_USUARIO ACTUAL_NOM_USUARIO
						,U2.NOM_USUARIO ACTUAL_NOM_USUARIO_CANCELA
						,TOT_REGISTRO ACTUAL_TOT_REGISTRO
						,ACTUAL_REGISTRO ACTUAL_ACTUAL_REGISTRO
						,case convert(varchar(4), datepart(year, FECHA_TERMINO))
							when null then 'none'
							else ''
						end ACTUAL_DISPLAY_EJECUCION
						,dbo.f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'E') ACTUAL_CANT_ERROR
						,dbo.f_ep_cant_error(E.COD_EJECUCION_PROCESO, 'W') ACTUAL_CANT_WARNING
						,CASE WHEN (TOT_REGISTRO  = 0) THEN 100
				             WHEN (ACTUAL_REGISTRO >= TOT_REGISTRO) THEN 100
				             ELSE (ACTUAL_REGISTRO / TOT_REGISTRO) * 100
				         END ACTUAL_AVANCE
				        ,COD_EJECUCION_PROCESO
				        ,E.ARCHIVO_RESULTADO
				from EJECUCION_PROCESO E left outer join USUARIO U2 on E.COD_USUARIO_CANCELA = U2.COD_USUARIO , USUARIO U
				where E.COD_EJECUCION_PROCESO = dbo.f_ep_ejecucion_actual({KEY1})					
				  and U.COD_USUARIO = E.COD_USUARIO";
		}
		parent::__construct($sql);
		
		$this->add_control(new static_text('ACTUAL_DURACION'));
		$this->add_control(new static_text('ACTUAL_CANT_ERROR'));
		$this->add_control(new static_text('ACTUAL_CANT_WARNING'));
		$this->add_control(new static_text('ACTUAL_ACTUAL_REGISTRO'));
	}
	function retrieve_bd($sql) {
		parent::retrieve_bd($sql);
		if ($this->row_count()==0) {
			$this->insert_row();
			$this->set_item(0, 'ACTUAL_FECHA_TERMINO', '__NO_RUN__');	// Debe ser distointo de '' para que asuma que no esta running
			$this->set_item(0, 'ACTUAL_CANT_ERROR', 0);
			$this->set_item(0, 'ACTUAL_CANT_WARNING', 0);
			$this->set_item(0, 'ACTUAL_AVANCE', 0);
		}
	}
	function fill_record(&$temp, $record) {
		parent::fill_record($temp, $record);
		
		// runing ?
		if ($this->get_item(0, 'ACTUAL_FECHA_TERMINO')=='') {
			$temp->setVar("DISABLE_INIT", 'disabled="true"');
			$temp->setVar("DISABLE_CANCEL", '');
			print '<script type="text/javascript"> setTimeout("barra_fill_progress(\''.$this->nom_w_proceso.'\')",1000); </script>';
		}
		else {
			$temp->setVar("DISABLE_INIT", '');
			$temp->setVar("DISABLE_CANCEL", 'disabled="true"');			

		}		
	}
}

class w_proceso extends w_base {
	// en $nom_tabla queda el nombre de la carpeta del proceso
	var $cod_proceso;
	var $dws = array();
	var $bd_php = 'bd';		// bd: indica proceso que se ejecuta en BD, 'php' indica proceso php
	
	static function f_viene_del_menu($modulo) {
		if (!session::is_set($modulo)) {
			return true;
		}
		return false;
	}
	function __construct($nom_tabla, $cod_item_menu, $cod_proceso, $bd_php='bd') {
	    parent::__construct($nom_tabla, $cod_item_menu);
		$this->cod_proceso = $cod_proceso;
		$this->bd_php = $bd_php;
		$this->nom_template = "wp_".$this->nom_tabla.".htm";
		 
		$this->dws['dw_ejecucion_anterior'] = new dw_ejecucion_anterior();
		$this->dws['dw_ejecucion_actual'] = new dw_ejecucion_actual("wp_".$this->nom_tabla);
		
		$this->load_dws();

		$this->save_SESSION();
		$this->redraw();		
	}
	function load_dws() {
		$this->dws['dw_ejecucion_anterior']->retrieve($this->cod_proceso);
		$this->dws['dw_ejecucion_actual']->retrieve($this->cod_proceso);		
	}
	function redraw_dws(&$temp) {
		$this->dws['dw_ejecucion_anterior']->habilitar($temp, false);
		$this->dws['dw_ejecucion_actual']->habilitar($temp, false);		
	}
	function redraw() {
		$temp = new Template_appl($this->nom_template);

		$this->make_menu($temp);

		$temp->setVar("WP_RUTA_MENU", $this->ruta_menu);
		$temp->setVar("WP_FECHA_ACTUAL", 'Fecha Actual: '.$this->current_date());
		$temp->setVar("WP_NOM_USUARIO", $this->nom_usuario);
		
		$this->habilita_boton($temp, 'back', true);
		$this->redraw_dws($temp);
		
		print $temp->toString();		
	}
	function habilita_boton(&$temp, $boton, $habilita) {
		if ($habilita)
			$temp->setVar("WP_".strtoupper($boton), '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib_seven/trunk/images/b_'.$boton.'.jpg" type="image" '.
																							'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
																							'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
																							'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib_seven/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
																							'/>');
		else
			$temp->setVar("WP_".strtoupper($boton), '<img src="../../../../commonlib_seven/trunk/images/b_'.$boton.'_d.jpg">');
	}
	function save_SESSION() {
	    session::set("wp_".$this->nom_tabla, serialize($this));
		session::set($this->nom_tabla, $this->nom_tabla);
	}
	function make_cmd_sp($cod_ejecucion_proceso) {
		// Debe retornar el sp y sus parametros para ser ejecutado
		return 'Debe retornar el sp y sus parametros para ser ejecutado';
	}
	function make_cmd_php($cod_ejecucion_proceso) {
		// Debe retornar el php y sus parametros para ser ejecutado
		return 'Debe retornar el php y sus parametros para ser ejecutado';
	}
	function ejecutar_proceso() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		if ($db->EXECUTE_SP('spu_ejecucion_proceso', "'NEW', null, $this->cod_proceso, $this->cod_usuario")) {
			$cod_ejecucion_proceso = $db->GET_IDENTITY('ejecucion_proceso');
			$this->dws['dw_ejecucion_actual']->retrieve($this->cod_proceso);
			$this->save_SESSION();
			if ($this->bd_php == 'bd') {
				$proceso = $this->make_cmd_sp($cod_ejecucion_proceso); 

				// Ejecuta el proceso con shell
				$posicion = strpos ($proceso,'(');
				if ($posicion===false)
					$posicion = strpos ($proceso,' ');
				$nom_archivo = trim(substr($proceso,0,$posicion));
				$archivo_shell = str_replace("\\", "/", dirname(__FILE__)).'/temp/'.$nom_archivo.'.sh';
				if (file_exists($archivo_shell)){
					unlink($archivo_shell);	 
				}
				$ar = fopen($archivo_shell, 'w');
				chmod($archivo_shell, 0777);
				$linea = "exec ".$proceso.";\n exit;"; //esto es para cerrar el sqlplus en xp.
				//$linea = "exec ".$proceso.";";
				fwrite($ar, $linea);

				fclose($ar);
				
				if (K_TIPO_BD=="oci")	{
					$linea = K_SQLPLUS." ".K_USER."/".K_PASS."@".K_SERVER_BD." @".$archivo_shell." &";
					//print $linea;
					//return;
					shell_exec($linea);
				}
				else if (K_TIPO_BD=="mssql")	{
					$db->query("exec ".$proceso);
					
					/* //PARA EJECUTAR MSSQL DESDE LINUX o WINDOWS
					$linea = "sqlcmd -S 201.238.210.133 -U sa -P ctrl*integra*ctrl -d HELEN_UACH -i".$archivo_shell ;
					shell_exec($linea);
					*/		
				}

				
			}
			else if ($this->bd_php == 'php') {
				$proceso = $this->make_cmd_php($cod_ejecucion_proceso); 	

				// 	Ejecuta en una nueva ventana
				print " <script>
							popup_window = window.open('".$proceso."')
							popup_window.close ();
					</script>";
			}
		}
		$this->redraw();
		print '<script type="text/javascript"> barra_start(\'wp_'.$this->nom_tabla.'\'); </script>';
	}
	function cancelar_proceso() {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$cod_ejecucion_proceso = $this->dws['dw_ejecucion_actual']->get_item(0, 'COD_EJECUCION_PROCESO');
		if ($db->EXECUTE_SP('spu_ejecucion_proceso', "'CANCEL', $cod_ejecucion_proceso, null, $this->cod_usuario")) {
			$this->load_dws();
			$this->save_SESSION();
		}
		$this->redraw();
	}
	function procesa_event() {
		if(isset($_POST['b_back_x']))
			$this->presentacion();
		elseif(isset($_POST['b_init']))
			$this->ejecutar_proceso();
		elseif(isset($_POST['b_cancel']))
			$this->cancelar_proceso();
	}
}
?>
