CREATE OR REPLACE PROCEDURE spu_deudor (ve_operacion			      VARCHAR2 
				                    , ve_cod_deudor		        NUMBER  
				                    , ve_rut				          NUMBER    := NULL
				                    , ve_dig_verif			      VARCHAR2  := NULL
				                    , ve_nombre			          VARCHAR2  := NULL
				                    , ve_apellido_paterno	    VARCHAR2  := NULL
				                    , ve_apellido_materno	    VARCHAR2  := NULL
				                    , ve_sexo				          VARCHAR2  := NULL
				                    , ve_cod_estado_civil	    NUMBER    := NULL
				                    , ve_fecha_nacim		      DATE      := NULL
				                    , ve_fecha_defuncion	    DATE      := NULL
				                    , ve_password			        VARCHAR2  := NULL
				                    , ve_cod_situacion_academica 	NUMBER 	  := NULL)
AS
BEGIN
	DECLARE vl_password   VARCHAR2(100);
          vl_password_old VARCHAR2(100);
          vl_cod_log_cambio NUMBER;

  BEGIN 
	  IF (ve_password is NULL)THEN  
		  vl_password := ve_rut;

	  ELSE
		  vl_password := ve_password;
	  END IF;

	  IF(ve_operacion = 'INSERT')THEN 
		  INSERT INTO deudor
				    (cod_deudor
            		,rut
				    ,dig_verif
				    ,nombre
				    ,apellido_paterno
				    ,apellido_materno
				    ,sexo
				    ,cod_estado_civil
				    ,fecha_nacim
				    ,fecha_defuncion
				    ,password
				    ,cod_situacion_academica)
		  VALUES (deudor_sq.NEXTVAL 
           			,ve_rut
				    ,ve_dig_verif
				    ,ve_nombre
				    ,ve_apellido_paterno
				    ,ve_apellido_materno
				    ,ve_sexo
				    ,ve_cod_estado_civil
				    ,ve_fecha_nacim
				    ,ve_fecha_defuncion
				    ,vl_password
				    ,ve_cod_situacion_academica);

	  ELSIF(ve_operacion = 'UPDATE')THEN 
		  UPDATE	DEUDOR 
		  SET		  rut					      = ve_rut
				    , dig_verif			    = ve_dig_verif
				    , nombre			      = ve_nombre
				    , apellido_paterno	= ve_apellido_paterno
				    , apellido_materno	= ve_apellido_materno
				    , sexo				      = ve_sexo
				    , cod_estado_civil	= ve_cod_estado_civil
				    , fecha_nacim		    = ve_fecha_nacim
				    , fecha_defuncion	  = ve_fecha_defuncion
				    , password			    = vl_password
				    , cod_situacion_academica = ve_cod_situacion_academica
		  WHERE cod_deudor          =	ve_cod_deudor;
	
	  ELSIF(ve_operacion = 'CHANGE_PASSWORD')THEN
    
			SELECT password
			INTO vl_password_old
			FROM DEUDOR 
			WHERE cod_deudor =	ve_cod_deudor;
			 
			UPDATE	DEUDOR 
			SET		  password			= vl_password
			WHERE   cod_deudor    =	ve_cod_deudor;
			
			--registra modificacion en log_cambio                                                                                                
			sp_log_cambio ('DEUDOR'
			  , ve_cod_deudor
			  , 1  --COD_USUARIO, se asigna admninistrador. Ya que el que realiza el cambio es un usuario web y no del sistema helen.
			  , 'U');
			
			--registra modificacion en detalle_cambio
			SELECT  log_cambio_sq.CURRVAL
			INTO vl_cod_log_cambio
			FROM dual;
			                                                                              
			sp_detalle_cambio (vl_cod_log_cambio, 'PASSWORD', vl_password_old, vl_password);  
	  END	IF;
  END;
END;
