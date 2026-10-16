CREATE OR REPLACE PROCEDURE spu_deudor_reprogramacion(ve_operacion		VARCHAR2
													,ve_cod_deudor_reprogramacion	NUMBER := NULL
													,ve_cod_reprogramacion		NUMBER := NULL
													,ve_rut				NUMBER := NULL
													,ve_dig_verif		VARCHAR2 := NULL
													,ve_nombre			VARCHAR2 := NULL
													,ve_apellido_paterno	VARCHAR2 := NULL
													,ve_apellido_materno	VARCHAR2 := NULL
													,ve_sexo		VARCHAR2 := NULL
													,ve_fecha_nacimiento	DATE := NULL
													,ve_codigo_area1		VARCHAR2 := NULL
													,ve_telefono1	VARCHAR2 := NULL
													,ve_codigo_area2		VARCHAR2 := NULL
													,ve_telefono2	VARCHAR2 := NULL
													,ve_email		VARCHAR2 := NULL
													,ve_direccion	VARCHAR2 := NULL
													,ve_comuna		VARCHAR2 := NULL
													,ve_ciudad		VARCHAR2 := NULL
													,ve_region		VARCHAR2 := NULL)
AS
BEGIN
	IF (ve_operacion = 'INSERT')THEN
			INSERT INTO DEUDOR_REPROGRAMACION
					(COD_DEUDOR_REPROGRAMACION
					,COD_REPROGRAMACION
					,RUT 
					,DIG_VERIF 
					,NOMBRE 
					,APELLIDO_PATERNO 
					,APELLIDO_MATERNO 
					,SEXO 
					,FECHA_NACIMIENTO 
					,CODIGO_AREA1 
					,TELEFONO1 
					,CODIGO_AREA2 
					,TELEFONO2 
					,EMAIL 
					,DIRECCION 
					,COMUNA 
					,CIUDAD
					,REGION)
			VALUES(	deudor_reprogramacion_sq.NEXTVAL
					,ve_cod_reprogramacion
					,ve_rut 
					,ve_dig_verif 
					,ve_nombre 
					,ve_apellido_paterno 
					,ve_apellido_materno 
					,ve_sexo
					,ve_fecha_nacimiento
					,ve_codigo_area1 
					,ve_telefono1 
					,ve_codigo_area2 
					,ve_telefono2 
					,ve_email 
					,ve_direccion 
					,ve_comuna 
					,ve_ciudad
					,ve_region);
	ELSIF (ve_operacion = 'UPDATE')THEN
		UPDATE DEUDOR_REPROGRAMACION
		SET		COD_REPROGRAMACION	= ve_cod_reprogramacion
				,RUT			=	ve_rut
				,DIG_VERIF		=	ve_dig_verif 
				,NOMBRE			=	ve_nombre
				,APELLIDO_PATERNO	=	ve_apellido_paterno 
				,APELLIDO_MATERNO	=	ve_apellido_materno
				,SEXO			=	ve_sexo
				,FECHA_NACIMIENTO	=	ve_fecha_nacimiento
				,CODIGO_AREA1		=	ve_codigo_area1
				,TELEFONO1		=	ve_telefono1 
				,CODIGO_AREA2	=	ve_codigo_area2 
				,TELEFONO2		=	ve_telefono2
				,EMAIL			=	ve_email
				,DIRECCION		=	ve_direccion 
				,COMUNA			=	ve_comuna
				,CIUDAD			=	ve_ciudad
				,REGION			=   ve_region
        WHERE COD_DEUDOR_REPROGRAMACION = ve_cod_deudor_reprogramacion;
	
	ELSIF (ve_operacion = 'DELETE')THEN
		DELETE FROM DEUDOR_REPROGRAMACION
        WHERE COD_DEUDOR_REPROGRAMACION = ve_cod_reprogramacion;
    END IF;
END;