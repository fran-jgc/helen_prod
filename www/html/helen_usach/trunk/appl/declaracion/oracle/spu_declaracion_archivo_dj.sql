create or replace PROCEDURE spu_declaracion_archivo_dj(ve_operacion					VARCHAR2,
														ve_cod_declaracion			NUMBER   :=null,
												        ve_cod_declaracion_archivo  NUMBER   :=null,
												        ve_cod_revisado_archivo     NUMBER   :=null,
												        ve_obs_revisado    			VARCHAR2 :=null,
												        ve_cod_tipo_archivo     	NUMBER   :=null,
												        ve_ruta_archivo            	VARCHAR2 :=null,
													    ve_nom_archivo        		VARCHAR2 :=null,
													    ve_tamano     				NUMBER   :=null,
													    ve_tmp_name     			VARCHAR2 :=null,
													    ve_name_original     		VARCHAR2 :=null,
													    ve_type     				VARCHAR2 :=null,
													    ve_error     				VARCHAR2 :=null,
													    ve_ip						VARCHAR2 :=null,
													    ve_os						VARCHAR2 :=null,
													    ve_navegador				VARCHAR2 :=null,
									                    ve_cod_usuario				NUMBER   :=null
								                    )
	IS
	BEGIN
	DECLARE
	  vl_rut               NUMBER;
	  vl_cod_intento       NUMBER;
	  vl_con_observaciones NUMBER;
	  vl_sin_revisar       NUMBER;

	BEGIN

	IF (ve_operacion = 'UPDATE') THEN

	    UPDATE	DECLARACION_ARCHIVO
			SET	 cod_revisado_archivo = ve_cod_revisado_archivo,
			     obs_revisado 	= ve_obs_revisado
		WHERE  cod_declaracion_archivo = ve_cod_declaracion_archivo;

		SELECT cod_carga_intento
		  INTO vl_cod_intento
		  FROM declaracion_archivo
		 WHERE cod_declaracion_archivo = ve_cod_declaracion_archivo;

		IF vl_cod_intento IS NOT NULL THEN
			SELECT SUM(CASE WHEN cod_revisado_archivo = 3 THEN 1 ELSE 0 END),
			       SUM(CASE WHEN cod_revisado_archivo <> 2 THEN 1 ELSE 0 END)
			  INTO vl_con_observaciones, vl_sin_revisar
			  FROM declaracion_archivo
			 WHERE cod_carga_intento = vl_cod_intento;

			UPDATE declaracion_carga_intento
			   SET estado = CASE
				   WHEN vl_con_observaciones > 0 THEN 'RECHAZADO'
				   WHEN vl_sin_revisar > 0 THEN 'EN_REVISION'
				   ELSE 'APROBADO'
			   END
			 WHERE cod_carga_intento = vl_cod_intento;
		END IF;

	END IF;
	
	IF (ve_operacion = 'UPDATE_ENVIO') THEN
		--se cambia el estado a todos los archivos con observacion
	    UPDATE	DECLARACION_ARCHIVO
			SET	 cod_revisado_archivo = ve_cod_revisado_archivo
		WHERE  cod_declaracion = ve_cod_declaracion
		and cod_revisado_archivo = 3;

	END IF;
	
	IF (ve_operacion = 'INSERT') THEN

	    INSERT INTO DECLARACION_ARCHIVO (COD_DECLARACION_ARCHIVO,COD_DECLARACION,COD_TIPO_ARCHIVO,RUTA_ARCHIVO,NOM_ARCHIVO,TAMANO,
     								f_error,f_name,f_type,f_tmp_name,navegador,ip,os,fecha_subida, obs_revisado, cod_revisado_archivo, cod_usuario, origen)
							VALUES(declaracion_archivo_sq.NEXTVAL,ve_cod_declaracion,ve_cod_tipo_archivo,ve_ruta_archivo,ve_nom_archivo,ve_tamano,
								ve_error,ve_name_original,ve_type,ve_tmp_name,ve_navegador,ve_ip,ve_os,sysdate, ve_obs_revisado, ve_cod_revisado_archivo, ve_cod_usuario, 'HELEN');

	END IF;

	COMMIT;
	END;
 END spu_declaracion_archivo_dj;
