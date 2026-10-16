CREATE OR REPLACE PROCEDURE spu_documento_pago (ve_operacion					IN   VARCHAR2
												,ve_cod_documento_pago          IN   NUMBER := NULL
												,ve_cod_estado_documento_pago   IN   NUMBER := NULL
												,ve_cod_tipo_documento_pago     IN   NUMBER := NULL	--cuando ve_operacion = 'CAMBIO_ESTADO', aca viene el cod_usuario 
												,ve_nro_documento_pago          IN   NUMBER := NULL
												,ve_cod_banco                   IN   NUMBER := NULL
												,ve_cod_plaza                   IN   NUMBER := NULL
												,ve_fecha_documento_pago        IN   DATE   := NULL
												,ve_monto                       IN   NUMBER := NULL
												,ve_cod_deudor					IN   NUMBER := NULL
												,ve_cod_familia_deuda			IN   NUMBER := NULL
												,ve_rut_girador					IN   NUMBER := NULL
												,ve_dig_verif_girador			IN   VARCHAR2:= NULL)
AS
/******************************************************************************
   NOMBRE:       spu_documento_pago

   REVISION:
   Ver        Date         Author            Description
   ---------  ----------   ----------------  ------------------------------------
   1.0        22/10/2009   Cristian Sánchez   Inserta o Actualiza un deposito
   2.0		    08/02/2010   Cristian Sánchez	  Las fechas se pasan a tipo DATE

   NOTAS:

******************************************************************************/
BEGIN
  DECLARE	vl_cod_estado_actual	NUMBER;
			KL_ESTADO_PROTESTADO	NUMBER:=3;
			vl_current_date			DATE:=SYSDATE;
			vl_cod_usuario			NUMBER:=ve_cod_tipo_documento_pago; --en este parametro viene el código del usuario,
																		--necesario para crear el comprobante
			vl_glosa_comprobante	VARCHAR2(100);
			vl_cod_familia_deuda	NUMBER;
			K_COMP_PENDIENTE		NUMBER:=1;
			K_TIPO_COMP_TRASPASO	NUMBER:=3;
			K_ORIGEN_COMP			NUMBER;
			vl_cod_comprobante		NUMBER;
			vl_monto				NUMBER;
			vl_cod_cuenta_protesto	NUMBER;
			vl_cod_cuenta_ch_fecha	NUMBER;
			vl_cod_cuenta_cobranza	NUMBER;
			KL_ESTADO_COBRANZA		NUMBER:=6;
			vl_cod_analitico_comprobante NUMBER;
			vl_cod_deudor			    NUMBER;
      vl_cod_cuenta_banco   NUMBER;
      K_PROTESTO_DOCUMENTO  NUMBER:=27;
      K_COB_JUDICIAL_DOCUMENTO  NUMBER:=28;
      K_DEPOSITO	NUMBER:=3;
			K_TRANSBANK	NUMBER:=6;
			K_AJUSTE_SALDO	NUMBER:=99;
			vl_cod_banco	NUMBER;
			vl_cod_tipo_doc_pago NUMBER;
      		

    BEGIN 
		IF (ve_operacion = 'INSERT') THEN
			INSERT INTO documento_pago
                     (cod_documento_pago
                     ,cod_estado_documento_pago
					           ,fecha_registro
                     ,cod_tipo_documento_pago
                     ,nro_documento_pago
                     ,cod_banco
                     ,cod_plaza
                     ,fecha_documento_pago
                     ,monto
                     ,cod_deudor
                     ,cod_familia_deuda
                     ,rut_girador
					 ,dig_verif_girador)
              VALUES (documento_pago_sq.NEXTVAL
                     ,ve_cod_estado_documento_pago
                     ,SYSDATE
                     ,ve_cod_tipo_documento_pago
                     ,ve_nro_documento_pago
                     ,ve_cod_banco
                     ,ve_cod_plaza
                     ,ve_fecha_documento_pago
                     ,ve_monto
                     ,ve_cod_deudor
                     ,ve_cod_familia_deuda
                     ,ve_rut_girador
					 ,ve_dig_verif_girador);

		ELSIF(ve_operacion = 'CAMBIO_ESTADO') THEN 
			SELECT   cod_estado_documento_pago
					,cod_familia_deuda
					,monto
					,cod_deudor
					,COD_TIPO_DOCUMENTO_PAGO
			INTO	 vl_cod_estado_actual
					,vl_cod_familia_deuda
					,vl_monto
					,vl_cod_deudor
					,vl_cod_tipo_doc_pago
			FROM    documento_pago
			WHERE	cod_documento_pago = ve_cod_documento_pago;
			
			IF (vl_cod_estado_actual <> ve_cod_estado_documento_pago)THEN
				UPDATE	documento_pago
				SET		cod_estado_documento_pago = ve_cod_estado_documento_pago
				WHERE	cod_documento_pago = ve_cod_documento_pago;
			END IF;
			IF (ve_cod_estado_documento_pago = KL_ESTADO_PROTESTADO 
				and vl_cod_estado_actual <> KL_ESTADO_PROTESTADO) THEN
					vl_glosa_comprobante	:= 'Docto. Depositado a Protestado. Cód. Docto: ' || TO_CHAR(ve_cod_documento_pago);
					K_ORIGEN_COMP			:= 28; --'PROTESTO'
					
					spu_comprobante ('INSERT'
									,NULL
									,vl_current_date
									,vl_cod_usuario
									,vl_glosa_comprobante
									,vl_cod_familia_deuda
									,K_COMP_PENDIENTE
									,K_TIPO_COMP_TRASPASO
									,K_ORIGEN_COMP
									,NULL
									,vl_current_date);
									
					SELECT  comprobante_sq.CURRVAL
			        INTO vl_cod_comprobante
			        FROM dual;
					
			        select	cod_cuenta_protesto
			        INTO	vl_cod_cuenta_protesto
					from	familia_deuda 
					where	cod_familia_deuda = vl_cod_familia_deuda;
	
					spu_item_comprobante('INSERT'
										,NULL
										,vl_cod_comprobante
										,vl_cod_cuenta_protesto
										,vl_monto 
										,0);
										
					spu_analitico  ('INSERT'
									,NULL
									,vl_cod_comprobante
									,null
									,vl_cod_cuenta_protesto
									,vl_monto 
									,0);
					
					SELECT  analitico_comprobante_sq.CURRVAL
			        INTO vl_cod_analitico_comprobante
			        FROM dual;
					
			        update analitico_comprobante
					set cod_deudor = vl_cod_deudor
					where cod_analitico_comprobante = vl_cod_analitico_comprobante;
					

          -- debe ubicar la cuenta del banco donde esta depositado
				  IF (vl_cod_tipo_doc_pago = K_DEPOSITO or vl_cod_tipo_doc_pago = K_TRANSBANK or vl_cod_tipo_doc_pago = K_AJUSTE_SALDO) THEN
					  --el documento no tiene deposito
					  select cod_banco
            INTO vl_cod_banco
					  from DOCUMENTO_PAGO
					  where cod_documento_pago = ve_cod_documento_pago;
					
					  select cod_cuenta_contable
            INTO vl_cod_cuenta_banco
					  from cuenta_corriente
					  where COD_BANCO = vl_cod_banco
            AND ROWNUM = 1;
				  ELSE
					  --el documento tiene deposito
					  select c.cod_cuenta_contable
            INTO vl_cod_cuenta_banco
					  from deposito_documento dd, deposito d, cuenta_corriente c
					  where dd.cod_documento_pago = ve_cod_documento_pago
					    and d.cod_deposito = dd.cod_deposito
					    and d.cod_estado_deposito = 2 -- CONFIRMADO
					    and c.cod_cuenta_corriente = d.cod_cuenta_corriente;
				  END IF;

					spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_banco,  0 , vl_monto);
					spu_analitico ('INSERT', NULL, vl_cod_comprobante, NULL, vl_cod_cuenta_banco, 0 , vl_monto);
	
					SELECT  analitico_comprobante_sq.CURRVAL
			        INTO vl_cod_analitico_comprobante
			        FROM dual;
			        
					update analitico_comprobante
					set cod_deudor = vl_cod_deudor
					where cod_analitico_comprobante = vl_cod_analitico_comprobante;

          			---crea bitacora protesto
          			spu_bitacora_deudor ('Protesto de Documento Nº: ' || To_Char(ve_cod_documento_pago), K_PROTESTO_DOCUMENTO, vl_cod_deudor, vl_cod_usuario);
					
			ELSIF(ve_cod_estado_documento_pago = KL_ESTADO_COBRANZA 
				  and vl_cod_estado_actual <> KL_ESTADO_COBRANZA)	THEN
					
					vl_glosa_comprobante := 'Docto. Protestado a Cobranza Judicial, Cód. Docto: ' ||  TO_CHAR(ve_cod_documento_pago);
					K_ORIGEN_COMP		 := 29; --'COBRANZA JUDICIAL'
	
					spu_comprobante('INSERT'
									,NULL
									,vl_current_date
									,vl_cod_usuario
									,vl_glosa_comprobante
									,vl_cod_familia_deuda
									,K_COMP_PENDIENTE
									,K_TIPO_COMP_TRASPASO
									,K_ORIGEN_COMP
									,NULL
									,vl_current_date);

					SELECT  comprobante_sq.CURRVAL
			        INTO vl_cod_comprobante
			        FROM dual;
			        
					select	cod_cuenta_cobranza
					INTO	vl_cod_cuenta_cobranza
					from	familia_deuda
					where	cod_familia_deuda = vl_cod_familia_deuda;
					
					spu_item_comprobante('INSERT'
										,NULL
										,vl_cod_comprobante
										,vl_cod_cuenta_cobranza
										,vl_monto
										,0);

					spu_analitico('INSERT'
								 ,NULL
								 ,vl_cod_comprobante
								 ,null
								 ,vl_cod_cuenta_cobranza
								 ,vl_monto
								 ,0);
	
					SELECT  analitico_comprobante_sq.CURRVAL
			        INTO vl_cod_analitico_comprobante
			        FROM dual;
			        
					update analitico_comprobante
					set cod_deudor = vl_cod_deudor
					where cod_analitico_comprobante = vl_cod_analitico_comprobante;
					
					select	cod_cuenta_protesto
					INTO	vl_cod_cuenta_protesto
					from	familia_deuda 
					where	cod_familia_deuda = vl_cod_familia_deuda;
					
					spu_item_comprobante('INSERT'
										 ,NULL
										 ,vl_cod_comprobante
										 ,vl_cod_cuenta_protesto
										 ,0
										 ,vl_monto);
										 
					spu_analitico('INSERT'
								 ,NULL
								 ,vl_cod_comprobante
								 ,NULL
								 ,vl_cod_cuenta_protesto
								 ,0
								 ,vl_monto);
	
					SELECT  analitico_comprobante_sq.CURRVAL
			        INTO vl_cod_analitico_comprobante
			        FROM dual;
			        
					update analitico_comprobante
					set cod_deudor = vl_cod_deudor
					where cod_analitico_comprobante = vl_cod_analitico_comprobante;

           			---crea bitacora cobranza judicial
         			spu_bitacora_deudor ('Cobranza Judicial de Documento Nº: ' || To_Char(ve_cod_documento_pago), K_COB_JUDICIAL_DOCUMENTO, vl_cod_deudor, vl_cod_usuario);

			END IF;
      END IF;
    END;
END;
/