CREATE OR REPLACE PROCEDURE spi_project_matricula(ve_ano number)
AS
BEGIN
	declare vc_rut					      NUMBER;
			    vl_count				      NUMBER:= 0;
			    vl_rut					      NUMBER;
			    vl_dig_verif		      VARCHAR2(1);
			    vl_apellido_paterno	  VARCHAR2(100);
			    vl_apellido_materno	  VARCHAR2(100);
			    vl_nombre				      VARCHAR2(100);
			    K_PARAM_UNIVERSIDAD	  NUMBER:= 3;

	
	CURSOR C_PRO_MA_TEMP IS
	SELECT	RUT
	FROM	  MATRICULA M, DEUDOR DR
	WHERE	  M.COD_UNIVERSIDAD = f_get_parametro(K_PARAM_UNIVERSIDAD)
	AND		  M.COD_DEUDOR = DR.COD_DEUDOR
	AND		  M.ANO_MATRICULA = ve_ano;

  BEGIN
	vl_count := 0;
  
  FOR a IN C_PRO_MA_TEMP LOOP
		SELECT  COUNT(*)
    INTO    vl_count 
		FROM	  MATRICULA M, DEUDOR D
		WHERE	  M.COD_DEUDOR = D.COD_DEUDOR	
		and		  RUT = a.rut;

		IF (vl_count = 0) THEN
			SELECT	DISTINCT CM.RUT
					    ,DIG_VERIF
					    ,APELLIDO_PATERNO
					    ,APELLIDO_MATERNO
					    ,NOMBRE
      INTO    vl_rut 
              ,vl_dig_verif
              ,vl_apellido_paterno
              ,vl_apellido_materno
              ,vl_nombre
			FROM	  CARGA_MATRICULA CM, DEUDOR DR
			WHERE	  CM.RUT = DR.RUT
			AND		  DR.RUT = a.rut;
		
		ELSE 
			SELECT	DISTINCT RUT
					    ,DIG_VERIF
					    ,APELLIDO_PATERNO
					    ,APELLIDO_MATERNO
					    ,NOMBRE
      INTO    vl_rut
              ,vl_dig_verif
              ,vl_apellido_paterno
              ,vl_apellido_materno
              ,vl_nombre
			FROM	  MATRICULA M, DEUDOR DR
			WHERE	  M.COD_DEUDOR = DR.COD_DEUDOR
			AND		  RUT = a.rut;		
		END IF;  
		
		INSERT INTO TEMP_PROJECT_MATRICULA 
              (RUT
							,DIG_VERIF
							,APELLIDO_PATERNO
							,APELLIDO_MATERNO
							,NOMBRE)
		VALUES		(vl_rut
							,vl_dig_verif
							,vl_apellido_paterno
							,vl_apellido_materno
							,vl_nombre);
  END LOOP;
	
  END;
END;
/