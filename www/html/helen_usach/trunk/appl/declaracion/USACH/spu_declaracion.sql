PROMPT CREATE OR REPLACE PROCEDURE spu_declaracion
CREATE OR REPLACE PROCEDURE spu_declaracion (ve_operacion                IN   VARCHAR2
											 , ve_cod_declaracion          IN   NUMBER
											 , ve_cod_estado_declaracion   IN   NUMBER    := NULL
											 , ve_cod_usuario              IN   NUMBER    := NULL
											 , ve_cod_deudor               IN   NUMBER    := NULL
											 , ve_cod_via_declaracion      IN   NUMBER    := NULL
											 , ve_cod_usuario_confirma     IN   NUMBER    := NULL
											 , ve_fecha_declaracion        IN   DATE      := NULL
											 , ve_ano                      IN   NUMBER    := NULL
											 , ve_con_hijo                 IN   VARCHAR2  := NULL
											 , ve_cod_afp                  IN   NUMBER    := NULL
											 , ve_cod_estado_civil         IN   NUMBER    := NULL
											 , ve_rut_conyuge              IN   NUMBER    := NULL
											 , ve_dig_verif_conyuge        IN   VARCHAR2  := NULL
											 , ve_nom_conyuge              IN   VARCHAR2  := NULL
											 , ve_conyuge_es_deudor        IN   VARCHAR2  := NULL
											 , ve_conyuge_declara          IN   VARCHAR2  := NULL
											 , ve_entrega_f22              IN   VARCHAR2  := NULL
                       						 , ve_cod_afp_conyuge          IN   NUMBER    := NULL
                       						 , ve_entrega_f22_conyuge      IN   VARCHAR2   := NULL
                       						 , ve_cert_sueldo			   IN   VARCHAR2  := NULL
									 		 , ve_cert_honorario           IN   VARCHAR2  := NULL
									 		 , ve_cert_afp				   IN   VARCHAR2  := NULL
									 		 , ve_declara_renta			   IN   VARCHAR2  := NULL
									 		 , ve_cert_notarial_ing        IN   VARCHAR2  := NULL
									 		 , ve_antec_conyuge			   IN   VARCHAR2  := NULL
									 		 , ve_cert_alum_regular        IN   VARCHAR2  := NULL
									 		 , ve_presenta_postgrado       IN   VARCHAR2  := NULL
											 )
AS
/******************************************************************************
   NAME:       spu_declaracion
   PURPOSE:    Crea, Actualiza declaraciones de ingreso

   REVISIONS:
   Ver        Date        Author              Description
   ---------  ----------  ---------------     ------------------------------------
   1.0        03/12/2009  Sergio Castro

   NOTES:
    VM - SC 100710 Si se anula una DJ no se vuelve a recalcular la cuota
******************************************************************************/
BEGIN
	DECLARE
		K_EST_DECL_INGRESO              NUMBER := 1;
		K_EST_DECL_APROBADA             NUMBER := 2;
		K_EST_DECL_ANULADA              NUMBER := 3;
		K_INGRESA_DECLARACION           NUMBER := 6;
		K_MODIFICA_DECLARACION          NUMBER := 7;
		K_ORIGEN_PAGO_CSR               NUMBER := 9;
		K_CONCEP_CASTIGO_VENCIDA        NUMBER := 4;
		K_CONCEP_LARGO_PLAZO            NUMBER := 3;
		K_CONCEP_COND_DJ_DEUDAS_CSR     NUMBER := 15;
		K_DEUDA_CONBRANZA_NORMAL        NUMBER := 2;
		vl_cod_usuario_confirma         NUMBER;
		vl_fecha_confirma               DATE;
		vl_cod_estado_decl_anterior     NUMBER;
		vl_cod_estado_declaracion       NUMBER;
		vl_fecha_confirma_anterior      DATE;
		vl_count_plan_pago              NUMBER;
		vl_cod_tipo_deuda               VARCHAR2(10);
		vl_cod_plan_pago                NUMBER;
		vl_rut_deudor                   NUMBER;
		vl_cod_deuda                    NUMBER;
		vl_cta_castigo_cs               NUMBER;
		vl_cta_castigo_csr              NUMBER;
		vl_cta_lp                       NUMBER;
		vl_cta_condonacion_dj           NUMBER;
		vl_cuota_original               NUMBER;
		vl_cuota_nueva                  NUMBER := 0;
		vl_dif                          NUMBER;
		vl_nom_comprobante              comprobante.nom_comprobante%TYPE;
		vl_cod_comprobante              NUMBER;
    vl_cod_comprobante_new          NUMBER;
		K_FONDO_SOLIDARIO               NUMBER:= 1;
		K_ESTADO_COMPROBANTE_PENDIENTE  NUMBER:= 1;
		K_TIPO_COMP_TRASPASO            NUMBER:= 3;
		vl_monto_capital_despues        NUMBER;
		vl_max_ano_declaracion          NUMBER;
		vl_ano_sigue					          NUMBER := ve_ano + 1;
		vl_fecha_actual					        DATE;
		vl_cod_usuario					        NUMBER;
		vl_cod_comprobante1				      NUMBER;
		vl_cod_comprobante2				      NUMBER;
		vl_cod_comprobante1_new     		NUMBER := NULL;
		vl_cod_comprobante2_new		      NUMBER := NULL;
    vl_ano	                        NUMBER;
    
	  CURSOR C_DECLARACION IS
	  SELECT	 DA.COD_TIPO_DEUDA
		  ,DA.COD_DEUDA
		  ,DE.ANO +1 ANO_SIGUE
		  ,cod_usuario
	  FROM	DECLARACION DE, DEUDA DA
	  WHERE	DE.COD_DECLARACION = ve_cod_declaracion
		    AND	DE.COD_DEUDOR = DA.COD_DEUDOR
		    AND	DA.COD_TIPO_DEUDA IN ('CS','CSR')
		    AND	DA.COD_ESTADO_DEUDA = K_DEUDA_CONBRANZA_NORMAL;
                 
	  CURSOR C_DECLARACION2 (vc_ano number) IS
	  SELECT   pp.cod_plan_pago
						  ,pp.cod_deuda
						  ,cod_tipo_deuda
		FROM    plan_pago pp
				,deuda d
				,deudor dd
		WHERE   d.cod_deuda = pp.cod_deuda
				AND d.cod_deudor = dd.cod_deudor
				AND pp.ano_cobro = vc_ano + 1
				AND rut = vl_rut_deudor
				AND cod_tipo_deuda IN ('CS','CSR')
		    	AND	D.COD_ESTADO_DEUDA = K_DEUDA_CONBRANZA_NORMAL;

	BEGIN

		IF (ve_cod_estado_declaracion = K_EST_DECL_APROBADA)
		THEN
			vl_cod_usuario_confirma              := ve_cod_usuario_confirma;
			vl_fecha_confirma                    := SYSDATE;
		ELSE
			vl_cod_usuario_confirma              := NULL;
			vl_fecha_confirma                    := NULL;
		END IF;

		IF (ve_operacion = 'INSERT')
		THEN
			-- se coloca primero el spu_bitacora_deudor ya que en caso contrario el cod_declaracion lo asocia por el cod_bitacora_deudor
			-- se basa en el orden de los factores
			spu_bitacora_deudor('Ingresa declaracion ano: ' || TO_CHAR (ve_ano)
	                            ,K_INGRESA_DECLARACION
	                            ,ve_cod_deudor
	                            ,ve_cod_usuario
	                             );

			--INSERTAR EN DECLARACION
			INSERT INTO declaracion
			             (cod_declaracion
			            , cod_estado_declaracion
			            , fecha_registro
			            , cod_usuario
			            , cod_deudor
			            , cod_via_declaracion
			            , fecha_confirma
			            , cod_usuario_confirma
			            , fecha_declaracion
			            , ano
			            , con_hijo
			            , cod_afp
			            , cod_estado_civil
			            , rut_conyuge
			            , dig_verif_conyuge
			            , nom_conyuge
			            , conyuge_es_deudor
			            , conyuge_declara
			            , entrega_f22
                  		, cod_afp_conyuge
                  		, entrega_f22_conyuge
                  		, cert_sueldo
                        , cert_honorario
                    	, cert_afp
                    	, declara_renta
                    	, cert_notarial_ing
                    	, antec_conyuge
                    	, cert_alum_regular
                    	, presenta_postgrado
			             )
			      VALUES (declaracion_sq.NEXTVAL
			            , ve_cod_estado_declaracion
			            , SYSDATE
			            , ve_cod_usuario
			            , ve_cod_deudor
			            , ve_cod_via_declaracion
			            , vl_fecha_confirma
			            , vl_cod_usuario_confirma
			            , ve_fecha_declaracion
			            , ve_ano
			            , ve_con_hijo
			            , ve_cod_afp
			            , ve_cod_estado_civil
			            , ve_rut_conyuge
			            , ve_dig_verif_conyuge
			            , ve_nom_conyuge
			            , ve_conyuge_es_deudor
			            , ve_conyuge_declara
			            , ve_entrega_f22
                  		, ve_cod_afp_conyuge
                  		, ve_entrega_f22_conyuge
                  		, ve_cert_sueldo
                    	, ve_cert_honorario
                    	, ve_cert_afp
                    	, ve_declara_renta
                    	, ve_cert_notarial_ing
                    	, ve_antec_conyuge
                    	, ve_cert_alum_regular
                    	, ve_presenta_postgrado
			             );
			/*consultar con la declaracion actual con la mas nueva de la BD*/
			SELECT  Max(ANO)
			INTO    vl_max_ano_declaracion
			FROM    declaracion
			WHERE   cod_deudor = ve_cod_deudor;

			IF (vl_max_ano_declaracion = ve_ano) THEN
				UPDATE deudor
				SET    cod_estado_civil = ve_cod_estado_civil
				WHERE  cod_deudor = ve_cod_deudor;
			END IF;
		ELSIF (ve_operacion = 'UPDATE') THEN
			SELECT  cod_estado_declaracion
			        ,fecha_confirma
			INTO    vl_cod_estado_decl_anterior
			        ,vl_fecha_confirma_anterior
			FROM    declaracion
			WHERE   cod_declaracion = ve_cod_declaracion;

			IF (vl_cod_estado_decl_anterior = K_EST_DECL_INGRESO
			    AND ve_cod_estado_declaracion = K_EST_DECL_APROBADA) THEN
				vl_cod_estado_declaracion            := K_EST_DECL_APROBADA;
				vl_fecha_confirma                    := SYSDATE;
			ELSIF (vl_cod_estado_decl_anterior = K_EST_DECL_APROBADA
			        AND ve_cod_estado_declaracion = K_EST_DECL_ANULADA) THEN
				vl_cod_estado_declaracion            := ve_cod_estado_declaracion;
				vl_fecha_confirma                    := NULL;
			ELSIF (vl_cod_estado_decl_anterior = K_EST_DECL_INGRESO
					AND ve_cod_estado_declaracion = K_EST_DECL_ANULADA)
			THEN
				vl_cod_estado_declaracion            := K_EST_DECL_ANULADA;
				vl_fecha_confirma                    := NULL;
			ELSE
			    vl_cod_estado_declaracion            := vl_cod_estado_decl_anterior;
			    vl_fecha_confirma                    := vl_fecha_confirma_anterior;
			END IF;

			UPDATE declaracion
			SET cod_estado_declaracion 	= vl_cod_estado_declaracion
				, cod_deudor 			= ve_cod_deudor
				, cod_via_declaracion 	= ve_cod_via_declaracion
				, fecha_confirma 		= vl_fecha_confirma
				, cod_usuario_confirma 	= vl_cod_usuario_confirma
				, fecha_declaracion 	= ve_fecha_declaracion
				, ano 					= ve_ano
				, con_hijo 				= ve_con_hijo
				, cod_afp 				= ve_cod_afp
				, cod_estado_civil 		= ve_cod_estado_civil
				, rut_conyuge 			= ve_rut_conyuge
				, dig_verif_conyuge 	= ve_dig_verif_conyuge
				, nom_conyuge 			= ve_nom_conyuge
				, conyuge_es_deudor 	= ve_conyuge_es_deudor
				, conyuge_declara 		= ve_conyuge_declara
				, entrega_f22 			= ve_entrega_f22
        		, cod_afp_conyuge 		= ve_cod_afp_conyuge
        		, entrega_f22_conyuge 	= ve_entrega_f22_conyuge
        		, cert_sueldo			= ve_cert_sueldo
                , cert_honorario		= ve_cert_honorario
                , cert_afp				= ve_cert_afp
                , declara_renta			= ve_declara_renta
                , cert_notarial_ing		= ve_cert_notarial_ing
                , antec_conyuge			= ve_antec_conyuge
                , cert_alum_regular		= ve_cert_alum_regular
                , presenta_postgrado	= ve_presenta_postgrado
			WHERE  cod_declaracion = ve_cod_declaracion;

			spu_bitacora_deudor ('Modifica declaracion ano: ' || TO_CHAR (ve_ano)
								, K_MODIFICA_DECLARACION
								, ve_cod_deudor
								, ve_cod_usuario
								 );
		ELSIF (ve_operacion = 'CALCULO_CUOTA') THEN
			FOR a IN C_DECLARACION LOOP
								
				IF (a.cod_tipo_deuda = 'CS') THEN
          vl_cod_comprobante1_new := NULL;
				  vl_cod_comprobante2_new := NULL;

					sp_deuda_cuota_cs (a.cod_deuda, a.ano_sigue, 'N', null, vl_cod_comprobante1_new, vl_cod_comprobante2_new, a.cod_usuario); 
					sp_comp_simplifica (vl_cod_comprobante1_new);
					sp_comp_simplifica (vl_cod_comprobante2_new);
      	END IF;
				IF(a.cod_tipo_deuda = 'CSR') THEN
          vl_cod_comprobante_new:= NULL;

					sp_deuda_cuota_csr (a.cod_deuda, a.ano_sigue, a.cod_usuario, vl_cod_comprobante_new, 'N'); 
					sp_comp_simplifica (vl_cod_comprobante_new);
				END IF;
			END LOOP;
						
		ELSIF (ve_operacion = 'RE_CALCULO_CUOTA') THEN
			/*
			Si existe la cuota para el año ve_ano + 1 (CS o CSR) => se reclacula la cuota
			Si existe    vl_cod_tipo_deuda queda en NULL; y no hace nada
			*/
			vl_cod_tipo_deuda := NULL;

			SELECT  d.rut
					    ,de.ano
					    ,de.cod_usuario
			INTO	vl_rut_deudor
					,vl_ano
					,vl_cod_usuario
			FROM    deudor d, declaracion de
			WHERE   de.cod_declaracion = ve_cod_declaracion
			  and	d.cod_deudor = de.cod_deudor;

			vl_ano_sigue := vl_ano + 1;

			SELECT  Count(*)
			INTO	vl_count_plan_pago
			FROM    plan_pago pp
					,deuda d
					,deudor dd
			WHERE   d.cod_deuda = pp.cod_deuda
			AND		d.cod_deudor = dd.cod_deudor
			AND		pp.ano_cobro = vl_ano + 1
			AND		rut = vl_rut_deudor
			AND		cod_tipo_deuda IN ('CS','CSR')
		    AND	D.COD_ESTADO_DEUDA = K_DEUDA_CONBRANZA_NORMAL;

			IF (vl_count_plan_pago > 0) THEN
			  FOR a IN C_DECLARACION2 (vl_ano) LOOP
			
				  IF (a.cod_tipo_deuda = 'CS') THEN
					  --REALIZA COPIA DE LOS PLANES DE PAGO ACTUALES
            vl_cod_comprobante1_new:= NULL;
				    vl_cod_comprobante2_new:= NULL;

					  sp_deuda_plan_pago_h (a.cod_deuda
									  , 'DECLARACION'
									  , ve_cod_declaracion
									    );

					  sp_deuda_cuota_cs (a.cod_deuda
								    , vl_ano_sigue
								    , 'S'
								    , NULL
								    , vl_cod_comprobante1_new
								    , vl_cod_comprobante2_new
								    , vl_cod_usuario
								    ); 

					  sp_comp_simplifica (vl_cod_comprobante1_new);
					  sp_comp_simplifica (vl_cod_comprobante2_new);
     		  END IF;
     						
				  IF(a.cod_tipo_deuda = 'CSR') THEN
					  --REALIZA COPIA DE LOS PLANES DE PAGO ACTUALES
            vl_cod_comprobante_new:=NULL;

					  sp_deuda_plan_pago_h (a.cod_deuda
									  , 'DECLARACION'
									  , ve_cod_declaracion
									    );
									  
					  -- RECALCULA PLAN PAGO
					  sp_deuda_cuota_csr (a.cod_deuda
									  , vl_ano_sigue
									  , vl_cod_usuario
									  , vl_cod_comprobante_new
									  , 'S'
									  );
					  sp_comp_simplifica (vl_cod_comprobante_new);			
				  END IF;
				END LOOP;		
			END IF; ---IF (vl_count_plan_pago > 0)				
		END IF;
	END;
END;