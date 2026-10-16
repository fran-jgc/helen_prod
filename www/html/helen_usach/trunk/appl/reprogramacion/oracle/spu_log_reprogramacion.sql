CREATE OR REPLACE PROCEDURE spu_log_reprogramacion (ve_cod_reprogramacion	NUMBER := NULL
												   ,ve_cod_usuario			NUMBER := NULL
					                               ,ve_nom_tabla				VARCHAR2 := NULL
					                               ,ve_key_tabla				VARCHAR2 := NULL
					                               ,ve_tipo_cambio			VARCHAR2 := NULL)
AS
BEGIN
	INSERT INTO LOG_REPROGRAMACION
			(COD_LOG_REPROGRAMACION
			,COD_REPROGRAMACION
			,COD_USUARIO
		    ,NOM_TABLA
		    ,KEY_TABLA
		    ,FECHA_CAMBIO
		    ,TIPO_CAMBIO)
	VALUES	(log_reprogramacion_sq.NEXTVAL
			,ve_cod_reprogramacion
			,ve_cod_usuario
		    ,ve_nom_tabla
		    ,ve_key_tabla
		    ,sysdate
		    ,ve_tipo_cambio);
END;