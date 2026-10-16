PROMPT CREATE OR REPLACE PROCEDURE spu_condonacion
CREATE OR REPLACE PROCEDURE spu_condonacion (ve_operacion               varchar2
									                          , ve_cod_condonacion			  number
									                          , ve_cod_usuario				    number
									                          , ve_cod_tipo_condonacion		number
									                          , ve_cod_deudor				      number
									                          , ve_obs						        varchar2
									                          , ve_cod_estado_condonacion	number
									                          , ve_cod_usuario_confirma		number
									                          , ve_tipo_doc					      varchar2:= null
									                          , ve_nom_receptor				    VARCHAR2:= null
									                          , ve_fecha_solicitud_tramite	VARCHAR2:= null
									                          )
AS
BEGIN
	DECLARE kl_cond_solicitada				      NUMBER:= 1;
			    kl_cond_aprobada                NUMBER:= 2;
			    kl_tipo_condonacion_defuncion   NUMBER:= 2;
			    vl_cod_estado_condonacion       NUMBER;
			    vl_folio_condonacion			NUMBER;
			    vl_cadena_folio					VARCHAR2(100):='0000';
			    vl_largo_nuevo_folio			NUMBER;
			    vl_cadena_nueva					VARCHAR2(100);

	BEGIN
	  IF (ve_operacion = 'INSERT')THEN
      --INSERTAR EN CONDONACION
			INSERT INTO condonacion
				      ( cod_condonacion
            		  , fecha_registro
				      , cod_usuario
				      , cod_tipo_condonacion
				      , cod_deudor
				      , obs
				      , cod_estado_condonacion
				      , fecha_solicitud_tramite
				      )
			  VALUES (condonacion_sq.NEXTVAL 
          			, sysdate
				    , ve_cod_usuario
				    , ve_cod_tipo_condonacion
				    , ve_cod_deudor
				    , ve_obs
				    , ve_cod_estado_condonacion
				    , TO_DATE(ve_fecha_solicitud_tramite, 'dd/mm/yyyy')
				    );
		ELSIF (ve_operacion = 'UPDATE') THEN
			SELECT  cod_estado_condonacion
      INTO    vl_cod_estado_condonacion
			FROM    condonacion
			WHERE   cod_condonacion = ve_cod_condonacion;

			UPDATE condonacion
			SET		cod_tipo_condonacion    = ve_cod_tipo_condonacion
					, cod_deudor              = ve_cod_deudor
					, obs                     = ve_obs
					, cod_estado_condonacion  = ve_cod_estado_condonacion
					, fecha_solicitud_tramite = TO_DATE(ve_fecha_solicitud_tramite, 'dd/mm/yyyy')
			WHERE	cod_condonacion         = ve_cod_condonacion;

			IF (vl_cod_estado_condonacion = kl_cond_solicitada
             AND ve_cod_estado_condonacion = kl_cond_aprobada)THEN
             
             SELECT  To_Number(Max(folio_condonacion))+1
             INTO vl_folio_condonacion
             FROM condonacion;
             			

        vl_largo_nuevo_folio :=  LENGTH(vl_folio_condonacion);
        
        IF(vl_largo_nuevo_folio < 4)then 
				  vl_cadena_nueva := substr(vl_cadena_folio,1,(4-vl_largo_nuevo_folio));
          vl_cadena_nueva := vl_cadena_nueva||to_char(vl_folio_condonacion);
        ELSE
          vl_cadena_nueva := to_char(vl_folio_condonacion);
        END IF;
        
             
				UPDATE  condonacion
				SET     cod_usuario_confirma  = ve_cod_usuario_confirma
					    , fecha_confirma        = sysdate
					    , folio_condonacion 	= vl_cadena_nueva
				WHERE  cod_condonacion        = ve_cod_condonacion;

				IF (ve_cod_tipo_condonacion = kl_tipo_condonacion_defuncion)THEN
				  UPDATE  deudor
					SET     fecha_defuncion = sysdate
					WHERE   cod_deudor = ve_cod_deudor;
				END IF;
			END IF;
		ELSIF (ve_operacion = 'DELETE')THEN
			DELETE  condonacion
			WHERE   cod_condonacion = ve_cod_condonacion;
		ELSIF (ve_operacion = 'PRINT_RECORD')THEN
		  INSERT INTO envio_notificacion
					      ( cod_envio_notificacion 
                , fecha_registro
					      , cod_usuario
					      , tipo_doc
					      , cod_doc
					      , nom_receptor)
			VALUES  (  envio_notificacion_sq.NEXTVAL
                , sysdate
					      , ve_cod_usuario
					      , ve_tipo_doc
					      , ve_cod_condonacion
					      , ve_nom_receptor
					      );
		END IF;
	END;
END;
