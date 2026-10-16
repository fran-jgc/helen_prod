PROMPT CREATE OR REPLACE PROCEDURE sp_ingreso_junta_pre
CREATE OR REPLACE PROCEDURE sp_ingreso_junta_pre(ve_fecha_desde	date,
												 ve_fecha_hasta	date)
AS
/*
CIERRE CAJA
*/
BEGIN
	DECLARE	
	
	K_INGRESO_EMITIDO		NUMBER :=1;
	K_INGRESO_CERRADO		NUMBER :=3;
	vl_cod_tipo_cajero		NUMBER;

	-- el comprobante debe ser por familia_deuda
	CURSOR C_INGRESO  IS
			SELECT	cod_ingreso
					,fecha_ingreso
			FROM	ingreso
			where	cod_estado_ingreso = K_INGRESO_EMITIDO
			order by fecha_ingreso asc;


	BEGIN
		
			FOR I IN C_INGRESO  LOOP
			
				update INGRESO
				SET COD_ESTADO_INGRESO = K_INGRESO_CERRADO
				   ,fecha_cierre = sysdate
			  	where COD_INGRESO = I.cod_ingreso;
			  
			  	commit;
			END LOOP;
		
	END;
END;
/