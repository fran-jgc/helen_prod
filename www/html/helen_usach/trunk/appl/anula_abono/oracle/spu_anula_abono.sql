PROMPT CREATE OR REPLACE PROCEDURE spu_anula_abono
CREATE OR REPLACE PROCEDURE spu_anula_abono (
   ve_operacion	   	            IN		VARCHAR2
 , ve_cod_anula_abono           IN		NUMBER
 , ve_cod_usuario               IN		NUMBER    := NULL
 , ve_obs                       IN    	VARCHAR2  := NULL
 , ve_cod_abono                 IN    	NUMBER    := NULL
 , ve_enviar_a_excedente        IN    	VARCHAR2  := NULL
 )
AS
BEGIN
  DECLARE	K_ESTADO_PAGO_ANULADA   NUMBER  := 2;
    		K_EXCEDENTE_EMITIDO       NUMBER  := 1;
    		K_ANULA_ABONO             NUMBER  := 14;
        K_ESTADO_DOCTO_ANULADO    NUMBER  :=8;
        KL_EST_COMPROBANTE_PDTE   NUMBER:=1;
        KL_COMPROB_TRASPASO       NUMBER:=3;
        KL_ORIGEN_COMPROB_ANULA_ABONO NUMBER:=8;
        K_CONCEPTO_CUENTA_EXCEDENTE     NUMBER := 19;

    		vl_cod_deudor			NUMBER;
    		vl_cod_plan_pago		NUMBER;
    		vl_cod_deuda			NUMBER;
    		vl_cod_excedente		NUMBER;
    		vl_monto_pesos			NUMBER;
    		vl_fecha_actual			DATE:=SYSDATE;
			vl_glosa_bitacora		  VARCHAR2(100);
      vl_cod_documento_pago NUMBER;
      vl_count              NUMBER;
      vl_cod_comprobante    NUMBER;
      vl_nom_comprobante    VARCHAR2(200);
      vl_cod_familia_deuda  NUMBER;
      vl_cta_excedente  NUMBER;
      vl_cod_tipo_deuda     VARCHAR(100);
      vl_cod_cuenta_ingreso NUMBER;
      vl_fecha_comprobante  VARCHAR2(100);
      vl_rut                NUMBER;
      vl_dig_verif          VARCHAR2(1);


	BEGIN
		IF (ve_operacion = 'INSERT')THEN

			UPDATE	abono
			SET		cod_estado_pago = K_ESTADO_PAGO_ANULADA
			WHERE	cod_abono = ve_cod_abono;

      SELECT a.cod_deuda
            ,d.cod_tipo_deuda
            ,t.cod_familia_deuda
            ,a.cod_documento_pago
            ,a.cod_comprobante
						,a.monto_pesos
      INTO vl_cod_deuda
          ,vl_cod_tipo_deuda
          ,vl_cod_familia_deuda
          ,vl_cod_documento_pago
          ,vl_cod_comprobante
					,vl_monto_pesos
      FROM abono a, deuda d, tipo_deuda t
      WHERE a.cod_abono = ve_cod_abono
        AND d.cod_deuda = a.cod_deuda
        AND t.cod_tipo_deuda = d.cod_tipo_deuda;

      -- si el abono esta en un comprobante, realizar el comprobante de anulaci�n
      IF (vl_cod_comprobante IS NOT NULL) THEN
        SELECT To_Char(fecha_comprobante,'dd/mm/yyyy')
        INTO vl_fecha_comprobante
        FROM comprobante
        WHERE cod_comprobante  = vl_cod_comprobante;

        SELECT  rut
                , dig_verif
				INTO	  vl_rut
                ,vl_dig_verif
				FROM	  abono a, deuda d, deudor dr
				WHERE	  a.cod_abono = ve_cod_abono
				AND		  d.cod_deuda = a.cod_deuda
        AND     dr.cod_deudor = d.cod_deudor;

        vl_nom_comprobante := 'Anula abono no rendido: Rut:' || TO_CHAR (vl_rut)||'-'||vl_dig_verif || '  Folio abono: ' || TO_CHAR (ve_cod_abono) ||' Fecha pago: '||vl_fecha_comprobante;
        spu_comprobante ('INSERT'
                      , NULL
                      , sysdate
                      , ve_cod_usuario
                      , vl_nom_comprobante
                      , vl_cod_familia_deuda
                      , KL_EST_COMPROBANTE_PDTE
                      , KL_COMPROB_TRASPASO
                      , KL_ORIGEN_COMPROB_ANULA_ABONO
                      , ve_cod_usuario
                      , SYSDATE
                        );

        SELECT comprobante_sq.CURRVAL
          INTO vl_cod_comprobante
          FROM DUAL;

        sp_comp_ing(vl_cod_comprobante, ve_cod_abono , null);
        -- pasa los monto haber al debe
        UPDATE item_comprobante
        SET monto_debe = monto_haber
            ,monto_haber = 0
        WHERE cod_comprobante = vl_cod_comprobante;

        UPDATE analitico_comprobante
        SET monto_debe = monto_haber
            ,monto_haber = 0
        WHERE cod_comprobante = vl_cod_comprobante;
      END IF;


			vl_cod_excedente := NULL;

			IF (ve_enviar_a_excedente = 'S') THEN
				UPDATE comprobante
        SET nom_comprobante = 'Anula abono duplicado: Rut:' || TO_CHAR (vl_rut)||'-'||vl_dig_verif || '  Folio abono: ' || TO_CHAR (ve_cod_abono) ||' Fecha pago: '||To_Char(vl_fecha_comprobante)
        WHERE cod_comprobante  = vl_cod_comprobante;

        SELECT   d.cod_deudor
				INTO	 vl_cod_deudor
				FROM	abono a, deuda d
				WHERE	a.cod_abono = ve_cod_abono
				AND		d.cod_deuda = a.cod_deuda;

				-------------------------
				--  CREANDO EXCEDENTE  --
				-------------------------

				spu_excedente('INSERT'
							 ,NULL
							 ,vl_fecha_actual
							 ,vl_cod_deudor
							 ,vl_monto_pesos
							 ,K_EXCEDENTE_EMITIDO
							 ,NULL
							 ,NULL
							 ,NULL
							 ,ve_cod_abono);

				SELECT	excedente_sq.CURRVAL
				INTO	vl_cod_excedente
				FROM DUAL;


        IF (vl_cod_comprobante IS NOT NULL) THEN
          vl_cta_excedente := f_comp_get_cuenta_tipo_deuda(vl_cod_tipo_deuda, K_CONCEPTO_CUENTA_EXCEDENTE);
          spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cta_excedente, 0, vl_monto_pesos);
          spu_analitico ('INSERT', NULL, vl_cod_comprobante, vl_cod_deuda, vl_cta_excedente, 0, vl_monto_pesos);
        END IF;

      ELSE
        IF (vl_cod_comprobante IS NOT NULL) THEN
            SELECT cod_cuenta_contable
            INTO vl_cod_cuenta_ingreso
            FROM cuenta_corriente
            WHERE cod_familia_deuda = vl_cod_familia_deuda
              AND ROWNUM = 1;

            spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_ingreso, 0, vl_monto_pesos);
            spu_analitico ('INSERT', NULL, vl_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_ingreso, 0, vl_monto_pesos);
        END IF;
			END IF;

			SELECT	 cod_plan_pago
					,cod_deuda
			INTO	 vl_cod_plan_pago
					,vl_cod_deuda
			FROM    abono
			WHERE   cod_abono = ve_cod_abono;

			sp_pp_actualiza_estado (vl_cod_plan_pago);
			sp_deuda_actualiza_estado (vl_cod_deuda);

			SELECT  d.cod_deudor
			INTO	vl_cod_deudor
			FROM    abono a
					,deuda d
			WHERE   a.cod_abono = ve_cod_abono
			AND		d.cod_deuda = a.cod_deuda;

			--ANULA DESCUENTO ASOCIADO
			UPDATE  descuento
		    SET     cod_estado_pago      = K_ESTADO_PAGO_ANULADA
		    WHERE   cod_deuda            = vl_cod_deuda
			  AND	cod_plan_pago        = vl_cod_plan_pago;
		    
			vl_glosa_bitacora := 'Anula Abono N°: ' || TO_CHAR(ve_cod_abono) || ' Comprobante : ' || TO_CHAR(vl_cod_comprobante) || ' , Observación: ' || ve_obs;
			spu_bitacora_deudor (vl_glosa_bitacora
								,K_ANULA_ABONO
								,vl_cod_deudor
								,ve_cod_usuario);

			-- Anulacion del abono
			INSERT INTO anula_abono
				  (cod_anula_abono
				  ,fecha_anula_abono
				  ,cod_usuario
				  ,cod_comprobante
				  ,obs
				  ,cod_abono
				  ,enviar_a_excedente
				  ,cod_excedente)
			VALUES (anula_abono_sq.NEXTVAL
				  ,SYSDATE
				  ,ve_cod_usuario
				  ,vl_cod_comprobante
				  ,ve_obs
				  ,ve_cod_abono
				  ,ve_enviar_a_excedente
				  ,vl_cod_excedente);

      --cuenta los documento distintos a CH_FECHA y que no esten anulados

     IF (vl_cod_comprobante IS NOT NULL) THEN
      UPDATE documento_pago
        SET cod_estado_documento_pago = K_ESTADO_DOCTO_ANULADO
        WHERE cod_documento_pago = vl_cod_documento_pago;
     END IF;
		END IF;
  END;
END;
/