CREATE OR REPLACE PROCEDURE spu_cambio_documento (
                                             ve_operacion	   	          IN	VARCHAR2
                                           , ve_cod_cambio_documento      IN	NUMBER    := NULL
                                           , ve_cod_usuario               IN	NUMBER    := NULL
                                           , ve_cod_deudor                IN    NUMBER    := NULL
                                           , ve_recargo                   IN    NUMBER    := NULL
                                           , ve_descuento                 IN    NUMBER    := NULL
                                           , ve_cod_familia_deuda         IN    NUMBER    := NULL
                                           )
AS
BEGIN

DECLARE
    vl_current_date			DATE := SYSDATE;
    K_COMP_PENDIENTE		NUMBER := 1;
    K_TIPO_COMP_TRASPASO	NUMBER := 3;
	K_ESTADO_PROTESTADO		NUMBER := 3;
	K_ESTADO_CAMBIADO 		NUMBER := 5;
		    
    vl_glosa_comprobante	VARCHAR2(100);
    K_ORIGEN_COMP			NUMBER := 27;
    vl_cod_comprobante		NUMBER;
    cod_cuenta_docto_original	NUMBER;
    monto					NUMBER(14,2);
    cod_cuenta_docto_nuevo	NUMBER;
    K_CTA_DSCTO				NUMBER := 9;
    vl_cod_cuenta_dscto		NUMBER;
    vl_cod_cuenta_ch_fecha	NUMBER;
    vl_cod_tipo_deuda		VARCHAR2(100);
    vl_cod_analitico_comprobante	NUMBER;
	vl_cod_cuenta_protesto			NUMBER;

    CURSOR c_doc_original IS
    select f_ingreso_get_cuenta(dp.cod_documento_pago) cod_cuenta_docto_original
		   ,dp.monto
			,dp.cod_estado_documento_pago
			,dp.cod_familia_deuda
			,dp.cod_documento_pago
	from documento_original di, documento_pago dp
		where cod_cambio_documento = ve_cod_cambio_documento
			and dp.cod_documento_pago = di.cod_documento_pago;
	
   CURSOR c_doc_nuevos IS
   select f_ingreso_get_cuenta(dp.cod_documento_pago) cod_cuenta_docto_nuevo
		  ,dp.monto
   from documento_nuevo dn, documento_pago dp
   		where cod_cambio_documento = ve_cod_cambio_documento
			and dp.cod_documento_pago = dn.cod_documento_pago;
   
   BEGIN
   IF (ve_operacion = 'INSERT') THEN
      INSERT INTO cambio_documento
                  (cod_cambio_documento
                 , cod_usuario
                 , fecha_cambio_documento
                 , cod_deudor
                 , recargo
                 , descuento
                 , cod_familia_deuda
                  )
           VALUES (cambio_documento_sq.NEXTVAL
                 , ve_cod_usuario
                 , SYSDATE
                 , ve_cod_deudor
                 , ve_recargo
                 , ve_descuento
                 , ve_cod_familia_deuda
                 );
   ELSIF (ve_operacion = 'UPDATE') THEN
      UPDATE cambio_documento
         SET cod_deudor = ve_cod_deudor
       WHERE cod_cambio_documento = ve_cod_cambio_documento;
   ELSIF (ve_operacion = 'COMPROBANTE') THEN
   		vl_glosa_comprobante := 'Cambio de Documento en Cartera. Cód. Cambio: ' || TO_CHAR(ve_cod_cambio_documento);
   		
   		spu_comprobante('INSERT', NULL, vl_current_date, ve_cod_usuario, vl_glosa_comprobante, ve_cod_familia_deuda, K_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_COMP, null, vl_current_date);
   		
   		SELECT comprobante_sq.CURRVAL
		INTO vl_cod_comprobante
		FROM dual;
		
		--documentos originales
		FOR a IN c_doc_original LOOP
			if (a.cod_estado_documento_pago = K_ESTADO_PROTESTADO) then
				select cod_cuenta_protesto
        INTO vl_cod_cuenta_protesto 
				from familia_deuda where cod_familia_deuda = a.cod_familia_deuda;

				spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_protesto,  0, a.monto);
			else
				spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, a.cod_cuenta_docto_original,  0, a.monto);
			end if;
		  	spu_analitico('INSERT', NULL, vl_cod_comprobante, null, a.cod_cuenta_docto_original, 0, a.monto);
			
			SELECT analitico_comprobante_sq.CURRVAL
			INTO vl_cod_analitico_comprobante
			FROM dual;
			
			update analitico_comprobante
			set cod_deudor = ve_cod_deudor
			where cod_analitico_comprobante = vl_cod_analitico_comprobante;

			-- cambia el estado del documento original
			UPDATE	documento_pago
			SET		cod_estado_documento_pago = K_ESTADO_CAMBIADO
			WHERE	cod_documento_pago = a.cod_documento_pago;
		END LOOP;
		
		--documentos nuevos
		FOR b IN c_doc_nuevos LOOP
			
			spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, b.cod_cuenta_docto_nuevo,  b.monto, 0);
			spu_analitico('INSERT', NULL, vl_cod_comprobante, null, b.cod_cuenta_docto_nuevo, b.monto, 0);
		
			SELECT analitico_comprobante_sq.CURRVAL
			INTO vl_cod_analitico_comprobante
			FROM dual;
			
			update analitico_comprobante
			set cod_deudor = ve_cod_deudor
			where cod_analitico_comprobante = vl_cod_analitico_comprobante;
		END LOOP;
		
		K_CTA_DSCTO := 9;
		
		vl_cod_tipo_deuda := 'CS';
    
    SELECT f_comp_get_cuenta_tipo_deuda(vl_cod_tipo_deuda, K_CTA_DSCTO)
    INTO vl_cod_cuenta_dscto
    FROM dual;
		
		spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_dscto,  ve_descuento, 0);
		spu_analitico('INSERT', NULL, vl_cod_comprobante, null, vl_cod_cuenta_dscto, ve_descuento, 0);
		
		SELECT analitico_comprobante_sq.CURRVAL
		INTO vl_cod_analitico_comprobante
		FROM dual;
		
		update analitico_comprobante
		set cod_deudor = ve_cod_deudor
		where cod_analitico_comprobante = vl_cod_analitico_comprobante;
		
		--intereses
		select cod_cuenta_ch_fecha
		into vl_cod_cuenta_ch_fecha
		from familia_deuda where cod_familia_deuda = ve_cod_familia_deuda;
		
		spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_ch_fecha,  0, ve_recargo);
		spu_analitico('INSERT', NULL, vl_cod_comprobante, null, vl_cod_cuenta_ch_fecha, 0, ve_recargo);
		
		SELECT analitico_comprobante_sq.CURRVAL
		INTO vl_cod_analitico_comprobante
		FROM dual;
		
		update analitico_comprobante
		set cod_deudor = ve_cod_deudor
		where cod_analitico_comprobante = vl_cod_analitico_comprobante; 
		
   END IF;
   END;
END;