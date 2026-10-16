PROMPT CREATE OR REPLACE PROCEDURE spu_deuda_cruzados
CREATE OR REPLACE PROCEDURE spu_deuda_cruzados	(ve_operacion			IN VARCHAR2
									,ve_cod_cruzado			IN NUMBER := NULL
									,ve_cod_deuda			IN NUMBER := NULL
									,ve_cod_universidad		IN NUMBER := NULL
									,ve_monto_actualizado	IN NUMBER := NULL
									,ve_porc				IN NUMBER := NULL
									,ve_fecha_consolidacion IN DATE := NULL)														
AS		
BEGIN
	DECLARE
	vl_nom_carrera			VARCHAR2(100);
	vl_cod_deudor_old		NUMBER;
	vl_cod_universidad_old	NUMBER;
	vl_ano_matricula_old	NUMBER;
	vl_semestre_old			NUMBER;
	vl_cod_carrera_old		NUMBER;
	vl_nom_carrera_old		VARCHAR2(100);
	vl_glosa_auditoria		VARCHAR2(100);
	
	BEGIN
		IF (ve_operacion = 'INSERT') THEN
		INSERT INTO	CRUZADO
					(COD_CRUZADO
					,COD_DEUDA	
					,COD_UNIVERSIDAD
					,MONTO_ACTUALIZADO
					,PORC
					,FECHA_CONSOLIDACION)
		VALUES      (cruzado_sq.NEXTVAL
					,ve_cod_deuda
					,ve_cod_universidad
					,ve_monto_actualizado
					,ve_porc
					,ve_fecha_consolidacion);
	
		ELSIF(ve_operacion = 'UPDATE') THEN
			UPDATE	CRUZADO
			SET      COD_UNIVERSIDAD	= ve_cod_universidad
					,MONTO_ACTUALIZADO	= ve_monto_actualizado
					,PORC				= ve_porc
					,FECHA_CONSOLIDACION = ve_fecha_consolidacion
			WHERE   COD_CRUZADO			= ve_cod_cruzado;
	
	    ELSIF(ve_operacion = 'DELETE') THEN
			DELETE  CRUZADO
	   		WHERE   COD_CRUZADO	= ve_cod_cruzado;
	   		
	   	END IF;
	   	
	   	COMMIT;
	END;
END;
/