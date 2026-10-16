CREATE OR REPLACE PROCEDURE spu_declaracion_archivo_portal(ve_operacion					VARCHAR2,
														ve_cod_declaracion			NUMBER   :=null,
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
													    ve_navegador				VARCHAR2 :=null
								                    )
as
BEGIN
	DECLARE vl_obs_revisado   VARCHAR2(100);
	BEGIN
		IF (ve_operacion = 'INSERT')then
			if(to_number(ve_error) <> 0)THEN
	    		vl_obs_revisado:= 'Archivo '||ve_name_original||' no subio correctamente';
	       
  				INSERT INTO DECLARACION_ARCHIVO (COD_DECLARACION_ARCHIVO,COD_DECLARACION,COD_TIPO_ARCHIVO,RUTA_ARCHIVO,NOM_ARCHIVO,TAMANO,
     								  f_error,f_name,f_type,f_tmp_name,navegador,ip,os,fecha_subida,COD_REVISADO_ARCHIVO, OBS_REVISADO )
							  VALUES(declaracion_archivo_sq.NEXTVAL,ve_cod_declaracion,ve_cod_tipo_archivo,ve_ruta_archivo,ve_nom_archivo,ve_tamano,
								  ve_error,ve_name_original,ve_type,ve_tmp_name,ve_navegador,ve_ip,ve_os,sysdate,3,vl_obs_revisado);
  	  		ELSE
      			INSERT INTO DECLARACION_ARCHIVO (COD_DECLARACION_ARCHIVO,COD_DECLARACION,COD_TIPO_ARCHIVO,RUTA_ARCHIVO,NOM_ARCHIVO,TAMANO,
     								  f_error,f_name,f_type,f_tmp_name,navegador,ip,os,fecha_subida)
							  VALUES(declaracion_archivo_sq.NEXTVAL,ve_cod_declaracion,ve_cod_tipo_archivo,ve_ruta_archivo,ve_nom_archivo,ve_tamano,
									  ve_error,ve_name_original,ve_type,ve_tmp_name,ve_navegador,ve_ip,ve_os,sysdate);
	    	END IF;
    	END IF;
	END;
	COMMIT;
 END spu_declaracion_archivo_portal;
