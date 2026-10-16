<?php
	require_once(dirname(__FILE__)."/../../../../commonlib_seven/trunk/php/auto_load.php");
	///////////////////////
	// php llamado con ajax en el javascript suspension.js, 
	// tomado de drop_down_dependiente.php
	////////////////////////

	
	$nom_tabla = $_REQUEST["nom_tabla"]; 
	$selectDestino = $_REQUEST["select"]; 
	$opcionSeleccionada = $_REQUEST['rut'];
	$ano=$_REQUEST['ano'];
	
	$len = strlen($selectDestino);
	$i = $len - 1;
	while (substr($selectDestino, $i, 1) != '_')
		$i--;
	$field = substr($selectDestino, 0, $i);
	$record = substr($selectDestino, $i + 1, $len - $i - 1);

	
	/****************************************************************************
	  SE ASUMIR� QUE UN DEUDOR SOLO TENDR� UNA DEUDA DE CADA TIPO - PC.SEPT.2009
	*****************************************************************************/

	
	//en el php que se llama con el ajax, deberia primero rescatar el sql original, 
	//cambiarlo por el nuevo hacer retrieve y despues volver al sql original - mail de VM 

	//el sql del ajax deber�a traer todas las deudas del deudor pero 
	//con cuota vigente del a�o (usar current date) y que sean CS o CSR - mail VM 
	
	
	$ano=date('Y');
	$sql_1=sql_suspension::get_sql('NOM_TIPO_DEUDA',$ano,$opcionSeleccionada);


	
	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	$result = $db->build_results($sql_1);

	$row_count = $db->count_rows();

	$wi = unserialize(session::get('wi_'.$nom_tabla));
		$control = $wi->find_control($field);
		if ($control) 
		{
			if ($opcionSeleccionada=='')$opcionSeleccionada = 0;

			$sql_original = $control->sql; 
			$control->sql = $sql_1;
			$control->retrieve();
			$control->sql = $sql_original; 
			$drop_down = $control->draw_entrable('', $record);
			
			print $row_count."|".$drop_down;
		}
		
	
	


?>