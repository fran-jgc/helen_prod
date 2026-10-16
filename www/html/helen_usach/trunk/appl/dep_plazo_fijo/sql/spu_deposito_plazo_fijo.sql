CREATE OR REPLACE PROCEDURE spu_deposito_plazo_fijo (ve_operacion					IN   VARCHAR2
												,ve_cod_dep_plazo_fijo        IN   NUMBER := NULL
												,ve_cod_usuario		          IN   NUMBER := NULL
												,ve_cod_estado_dep_plazo_fijo IN   NUMBER := NULL
												,ve_cod_moneda			      IN   NUMBER := NULL	--cuando ve_operacion = 'CAMBIO_ESTADO', aca viene el cod_usuario 
												,ve_fecha_emision             IN   DATE := NULL
												,ve_dias	                   IN   NUMBER := NULL
												,ve_fecha_vencto               IN   DATE := NULL
												,ve_tasa_interes_base          IN   NUMBER   := NULL
												,ve_tasa_interes_periodo       IN   NUMBER := NULL
												,ve_valor_inicial				IN   NUMBER := NULL
												,ve_intereses					IN   NUMBER := NULL
												,ve_valor_final					IN   NUMBER := NULL
												,ve_descripcion					IN   VARCHAR2:= NULL
												,ve_cod_cuenta_corriente		IN   NUMBER := NULL)
AS
/******************************************************************************
   NOMBRE:       spu_documento_pago

   REVISION:
   Ver        Date         Author            Description
   ---------  ----------   ----------------  ------------------------------------
   1.0        22/10/2009   Cristian S?nchez   Inserta o Actualiza un deposito
   2.0		    08/02/2010   Cristian S?nchez	  Las fechas se pasan a tipo DATE

   NOTAS:

******************************************************************************/
BEGIN
    DECLARE vl_fecha_dep_plazo_fijo DATE;
    		vl_origen_comprobante NUMBER; 	
    		vl_cod_comp_solidario NUMBER;	
    		vl_cod_estado_anterior NUMBER;
	        vl_nom_comprobante VARCHAR2(1000);
	        KL_FAMILIA_SOLIDARIO NUMBER := 1;
	        vl_cod_cuenta_bco   NUMBER;
	        KL_ESTADO_EMITIDA	NUMBER := 1;
	        KL_ESTADO_CONFIRMADA NUMBER := 2;
	        KL_ESTADO_ANULADA NUMBER := 3;
	        K_ESTADO_COMPROBANTE_ANULADO      NUMBER := 3;

    BEGIN 
		IF (ve_operacion = 'INSERT') THEN
			INSERT INTO DEP_PLAZO_FIJO
                     (	COD_DEP_PLAZO_FIJO
                     	,COD_USUARIO
					    ,COD_ESTADO_DEP_PLAZO_FIJO  
					    ,COD_MONEDA                 
					    ,FECHA_DEP_PLAZO_FIJO       
					    ,FECHA_EMISION              
					    ,DIAS                       
					    ,FECHA_VENCTO               
					    ,TASA_INTERES_BASE          
					    ,TASA_INTERES_PERIODO       
					    ,VALOR_INICIAL              
					    ,INTERESES                  
					    ,VALOR_FINAL                
					    ,DESCRIPCION
					    ,COD_CUENTA_CORRIENTE)              
              VALUES (dep_plazo_fijo_sq.NEXTVAL
                     ,ve_cod_usuario
                     ,ve_cod_estado_dep_plazo_fijo
                     ,ve_cod_moneda
                     ,SYSDATE
                     ,ve_fecha_emision
                     ,ve_dias
                     ,ve_fecha_vencto
                     ,ve_tasa_interes_base
                     ,ve_tasa_interes_periodo
                     ,ve_valor_inicial
                     ,ve_intereses
                     ,ve_valor_final
                     ,ve_descripcion
                     ,ve_cod_cuenta_corriente);

		ELSIF(ve_operacion = 'UPDATE') THEN 				
				
				SELECT FECHA_DEP_PLAZO_FIJO
					,COD_ESTADO_DEP_PLAZO_FIJO
				INTO vl_fecha_dep_plazo_fijo 
					,vl_cod_estado_anterior
				FROM DEP_PLAZO_FIJO
				WHERE COD_DEP_PLAZO_FIJO = ve_cod_dep_plazo_fijo; 
				
				
			IF (vl_cod_estado_anterior = KL_ESTADO_EMITIDA
			    AND ve_cod_estado_dep_plazo_fijo = KL_ESTADO_CONFIRMADA) THEN
				
				vl_origen_comprobante := 48;
				
				--crea comprobante para fondo solidario
                vl_nom_comprobante := 'Comprobante Deposito Plazo Fijo, ' || TO_CHAR(vl_fecha_dep_plazo_fijo,'dd/mm/yyyy');
                spu_comprobante ('INSERT', NULL, vl_fecha_dep_plazo_fijo, ve_cod_usuario, vl_nom_comprobante, KL_FAMILIA_SOLIDARIO, 1, 1, vl_origen_comprobante, ve_cod_usuario, vl_fecha_dep_plazo_fijo);
                      
                SELECT  comprobante_sq.CURRVAL
                INTO    vl_cod_comp_solidario
                FROM    dual;
                 
             	SELECT 	COD_CUENTA_CONTABLE						
				INTO vl_cod_cuenta_bco
				FROM CUENTA_CORRIENTE
				WHERE COD_CUENTA_CORRIENTE = ve_cod_cuenta_corriente;
					           
				--HABER
                spu_item_comprobante ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  0, ve_valor_inicial);
                spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, NULL, vl_cod_cuenta_bco, 0, ve_valor_inicial);

                --DEBE
                vl_cod_cuenta_bco:= 11413;
                spu_item_comprobante ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  ve_valor_final, 0);
                spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, NULL, vl_cod_cuenta_bco, ve_valor_final, 0);

				--HABER
				vl_cod_cuenta_bco := 32101; --BANCO DE CHILE
                spu_item_comprobante ('INSERT', NULL, vl_cod_comp_solidario, vl_cod_cuenta_bco,  0, ve_intereses);
                spu_analitico ('INSERT', NULL, vl_cod_comp_solidario, NULL, vl_cod_cuenta_bco, 0, ve_intereses);
			
			END IF;
			IF (vl_cod_estado_anterior = KL_ESTADO_CONFIRMADA
			    AND ve_cod_estado_dep_plazo_fijo = KL_ESTADO_ANULADA) THEN	
				    SELECT 	COD_COMPROBANTE						
					INTO vl_cod_comp_solidario
					FROM DEP_PLAZO_FIJO
					WHERE COD_DEP_PLAZO_FIJO = ve_cod_dep_plazo_fijo;

					  UPDATE  COMPROBANTE
				      SET    COD_ESTADO_COMPROBANTE = K_ESTADO_COMPROBANTE_ANULADO
			            	, FECHA_ESTADO           = SYSDATE 
				       WHERE COD_COMPROBANTE        = vl_cod_comp_solidario;
			 END IF;   		
			UPDATE	DEP_PLAZO_FIJO
			SET		COD_MONEDA = ve_cod_moneda
					,FECHA_EMISION = ve_fecha_emision
					,DIAS			= ve_dias
					,FECHA_VENCTO	= ve_fecha_vencto
					,TASA_INTERES_BASE = ve_tasa_interes_base
					,TASA_INTERES_PERIODO = ve_tasa_interes_periodo
					,VALOR_INICIAL	 = ve_valor_inicial
					,INTERESES = ve_intereses
					,VALOR_FINAL = ve_valor_final
					,DESCRIPCION = ve_descripcion
					,COD_ESTADO_DEP_PLAZO_FIJO = ve_cod_estado_dep_plazo_fijo
					,COD_COMPROBANTE = vl_cod_comp_solidario
					,COD_CUENTA_CORRIENTE = ve_cod_cuenta_corriente
			WHERE	COD_DEP_PLAZO_FIJO = ve_cod_dep_plazo_fijo;
				
      END IF;
    END;
END;
/