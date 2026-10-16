PROMPT CREATE OR REPLACE PROCEDURE sp_ingreso_junta_pre
CREATE OR REPLACE PROCEDURE sp_ingreso_junta_pre(ve_cod_usuario NUMBER)
AS
/*
CIERRE CAJA
Junta en 1 COMPROBANTE todos los PRE_COMPROBANTE que no han sido cerrados
crea un comprobante por familia, por dia y por cajero
*/
BEGIN
	DECLARE	 K_COMP_PENDIENTE		NUMBER :=1;
			 K_TIPO_COMP_INGRESO	NUMBER :=1;
			 K_ORIGEN_INGRESO_PAGO	NUMBER :=11;
			 K_INGRESO_EMITIDO		NUMBER :=1;
			 K_INGRESO_CERRADO		NUMBER :=3;
			 vl_glosa_compro		VARCHAR2(100);
			 vl_nom_usuario			VARCHAR2(100);
			 vl_current_date		DATE:=SYSDATE;
			 vc_cod_familia_deuda	NUMBER;
			 vl_count				NUMBER;
			 vl_cod_comprobante		NUMBER;
			 vc_cod_ingreso			NUMBER;
			 vc_fecha_ingreso		DATE;
			 vl_fecha_anterior		DATE;
       vl_cod_analitico_comprobante NUMBER;



	-- el comprobante debe ser por familia_deuda
	CURSOR C_FAMILIA IS
			SELECT  COD_FAMILIA_DEUDA
      		FROM    FAMILIA_DEUDA
          ;

	-- el comprobante debe ser por familia_deuda
	CURSOR C_INGRESO(vc_cod_familia_deuda NUMBER) IS
			SELECT	cod_ingreso
					,fecha_ingreso
			FROM	ingreso
			where	cod_comprobante is null
			  and	cod_familia_deuda = vc_cod_familia_deuda
			  and	cod_estado_ingreso = K_INGRESO_EMITIDO
			order by fecha_ingreso asc;


		-- el comprobante debe ser por familia_deuda
	CURSOR C_PRE_ANALITICO(vc_cod_ingreso NUMBER) IS
			select	 P.COD_DEUDA
					,P.COD_CUENTA_CONTABLE
					,P.MONTO_DEBE
					,P.MONTO_HABER
					,P.COD_DEUDOR
			from PRE_ANALITICO_COMPROBANTE P
			where P.COD_INGRESO = vc_cod_ingreso;

	BEGIN


	    select nom_usuario
	    into vl_nom_usuario
		from usuario
		where cod_usuario =ve_cod_usuario ;

		vl_fecha_anterior := to_date('01/01/1900','dd/mm/yyyy');
		vl_cod_comprobante := null;

		-- finalmente si viene desde boleta se desmarca la subboleta como pagada feha_pago = null
		FOR F IN C_FAMILIA LOOP
		  vl_fecha_anterior := to_date('01/01/1900','dd/mm/yyyy');
		  vl_cod_comprobante := null;

			FOR I IN C_INGRESO (F.cod_familia_deuda) LOOP
        SELECT Count(*)
        INTO vl_count
        FROM pre_comprobante
        WHERE cod_ingreso = I.cod_ingreso;

        IF(vl_count = 0 or F.cod_familia_deuda <> 1) THEN --no tiene PRE_COMPROBANTE or no es de la familia 1, solo cambiará el estado a CERRADO.
          update INGRESO
				  SET COD_ESTADO_INGRESO = K_INGRESO_CERRADO
				  where COD_INGRESO = I.cod_ingreso;

        ELSE

				  IF (to_char(vl_fecha_anterior, 'DD') <> to_char(I.fecha_ingreso, 'DD')
					  or to_char(vl_fecha_anterior, 'MM') <> to_char(I.fecha_ingreso, 'MM')
					  or to_char(vl_fecha_anterior, 'YYYY') <> to_char(I.fecha_ingreso, 'YYYY')) THEN

					  IF (vl_cod_comprobante is not null) THEN
						  sp_comp_simplifica (vl_cod_comprobante);	-- simplifica el comprobante anterior
						  spu_comprobante ('APROBAR', vl_cod_comprobante);
					  END IF;
					 vc_fecha_ingreso := I.fecha_ingreso;

					  -- glosa comprobante
					  vl_glosa_compro := 'Ingresos Fecha: ' || TO_CHAR(vc_fecha_ingreso, 'DD/MM/YYYY') || ', cajero: ' || vl_nom_usuario;

					  spu_comprobante ('INSERT'
									  ,NULL
									  ,vl_current_date
									  ,ve_cod_usuario
									  ,vl_glosa_compro
									  ,F.cod_familia_deuda
									  ,K_COMP_PENDIENTE
									  ,K_TIPO_COMP_INGRESO
									  ,K_ORIGEN_INGRESO_PAGO
									  ,NULL
									  ,vl_current_date);

					  SELECT  comprobante_sq.CURRVAL
			          INTO vl_cod_comprobante
			          FROM dual;

					  vl_fecha_anterior := I.fecha_ingreso;
				  END IF;

				  -- item_comprobante
				  insert INTO ITEM_COMPROBANTE
						  (COD_ITEM_COMPROBANTE
						  ,COD_COMPROBANTE
						  ,COD_CUENTA_CONTABLE
						  ,MONTO_DEBE
						  ,MONTO_HABER)
				  select	 item_comprobante_sq.NEXTVAL
						  ,vl_cod_comprobante
						  ,P.COD_CUENTA_CONTABLE
						  ,P.MONTO_DEBE
						  ,P.MONTO_HABER
				  from PRE_ITEM_COMPROBANTE P
				  where P.COD_INGRESO = I.cod_ingreso;

				  FOR A IN C_PRE_ANALITICO (I.cod_ingreso) LOOP
					  insert INTO ANALITICO_COMPROBANTE
								  (COD_ANALITICO_COMPROBANTE
								  ,COD_COMPROBANTE
								  ,COD_DEUDA
								  ,COD_CUENTA_CONTABLE
								  ,MONTO_DEBE
								  ,MONTO_HABER
								  ,COD_DEUDOR
								  ,COD_INGRESO)
						  VALUES(analitico_comprobante_sq.NEXTVAL
							    ,vl_cod_comprobante
							    ,A.COD_DEUDA
							    ,A.COD_CUENTA_CONTABLE
							    ,A.MONTO_DEBE
							    ,A.MONTO_HABER
							    ,A.COD_DEUDOR
							    ,I.COD_INGRESO);

					  SELECT  analitico_comprobante_sq.CURRVAL
			          INTO vl_cod_analitico_comprobante
			          FROM dual;

			          INSERT INTO ANALITICO_COMPROBANTE_INGRESO
							          (COD_ANALITICO_COMPROBANTE_INGR
							            ,COD_INGRESO
                          ,COD_ANALITICO_COMPROBANTE)
	      				  VALUES (analitico_comprobante_ingr_sq.NEXTVAL
				        			    ,I.cod_ingreso
                          ,vl_cod_analitico_comprobante);

				  END LOOP;

				  -- pasa a estado cerrado el INGRESO, y asigna el COD_COMPROBANTE
				  update INGRESO
				  set COD_COMPROBANTE = vl_cod_comprobante
					  ,COD_ESTADO_INGRESO = K_INGRESO_CERRADO
				  where COD_INGRESO = I.cod_ingreso;

				  --asigna COD_COMPROBANTE a los ABONOS del INGRESO
				  update abono
				  set cod_comprobante = vl_cod_comprobante
				  where cod_abono in (select cod_abono
									  from abono_ingreso
									  where cod_ingreso = I.cod_ingreso);

				  --asigna COD_COMPROBANTE a los DESCUENTOS del INGRESO
				  update descuento
				  set cod_comprobante = vl_cod_comprobante
				  where cod_descuento in (select cod_descuento
										  from descuento_ingreso
										where cod_ingreso = I.cod_ingreso);
        END IF; --IF(vl_count = 0)
			END LOOP;

			sp_comp_simplifica (vl_cod_comprobante);		-- simplifica el ultimo comprobante
			spu_comprobante ('APROBAR', vl_cod_comprobante);

		END LOOP;
	END;
END;
/

