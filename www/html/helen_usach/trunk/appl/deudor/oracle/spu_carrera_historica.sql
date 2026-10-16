PROMPT CREATE OR REPLACE PROCEDURE spu_carrera_historica
CREATE OR REPLACE PROCEDURE spu_carrera_historica	(ve_operacion		IN VARCHAR2
													,ve_cod_matricula	IN NUMBER := NULL
													,ve_cod_deudor		IN NUMBER := NULL
													,ve_cod_universidad	IN NUMBER := NULL
													,ve_ano_matricula	IN NUMBER := NULL
													,ve_semestre		IN NUMBER := NULL
													,ve_cod_carrera		IN NUMBER := NULL)
AS
BEGIN
	DECLARE  vl_nom_carrera				VARCHAR2(100);
	BEGIN
		IF(ve_cod_carrera IS NOT NULL) THEN
			select	nom_carrera
			into	vl_nom_carrera
			from	carrera
			where	cod_carrera = ve_cod_carrera;
		ELSE
			vl_nom_carrera := NULL;
		END IF;
	
		IF (ve_operacion = 'INSERT') THEN
			INSERT INTO	MATRICULA
						(COD_MATRICULA
						,COD_DEUDOR
						,COD_UNIVERSIDAD
						,ANO_MATRICULA
						,SEMESTRE
						,COD_CARRERA
						,NOM_CARRERA)
			VALUES      (matricula_sq.NEXTVAL
						,ve_cod_deudor
						,ve_cod_universidad
						,ve_ano_matricula
						,ve_semestre
						,ve_cod_carrera
						,vl_nom_carrera);
			
		ELSIF(ve_operacion = 'UPDATE') THEN
			UPDATE	MATRICULA
			SET      COD_DEUDOR			= ve_cod_deudor
					,COD_UNIVERSIDAD	= ve_cod_universidad
					,ANO_MATRICULA		= ve_ano_matricula
					,SEMESTRE			= ve_semestre
					,COD_CARRERA		= ve_cod_carrera
					,NOM_CARRERA		= vl_nom_carrera
			WHERE   COD_MATRICULA		= ve_cod_matricula;
			
		ELSIF(ve_operacion = 'DELETE') THEN
	    	DELETE  MATRICULA
	   		WHERE   COD_MATRICULA	=	ve_cod_matricula;
	   		
	   	END IF;
	   	
   	COMMIT;
	END;
END spu_carrera_historica;
/