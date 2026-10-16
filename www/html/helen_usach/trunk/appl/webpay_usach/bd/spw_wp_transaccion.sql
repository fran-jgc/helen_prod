PROMPT CREATE OR REPLACE PROCEDURE spw_wp_transaccion
CREATE OR REPLACE PROCEDURE spw_wp_transaccion(ve_tipo_pago			IN VARCHAR2 := NULL
											  ,ve_cod_deudor		IN NUMBER := NULL
											  ,ve_codsuboletas		IN VARCHAR2 := NULL
											  ,ve_cod_plan_pago		IN NUMBER := NULL
											  ,ve_cod_deuda			IN NUMBER := NULL
											  ,ve_monto_capital		IN NUMBER := NULL
											  ,ve_monto_interes		IN NUMBER := NULL
											  ,ve_monto_multa		IN NUMBER := NULL
											  ,ve_monto_moneda		IN NUMBER := NULL
											  ,ve_monto_pesos		IN NUMBER := NULL
											  ,ve_cod_tipo_descuento	IN NUMBER := NULL
											  ,ve_dscto_utm		IN NUMBER := NULL
											  ,ve_dscto_pesos		IN NUMBER := NULL
											  ,ve_tipo_pago_web		IN VARCHAR2 := NULL
                                              ,ve_ip				IN VARCHAR2 := NULL
						                      ,ve_navegador	        IN VARCHAR2 := NULL
						                      ,ve_sistema_operativo IN VARCHAR2 := NULL
)
AS
BEGIN
   DECLARE
		KL_USUARIO_WEB			NUMBER:= 2;
		vl_cod_deuda			NUMBER;
		vl_current_date			DATE := SYSDATE;
		vl_cod_boleta			NUMBER;
		KL_COMPONENTE_VIGENTE	NUMBER := 3;
		vl_codsuboletas			VARCHAR2(200);
		vl_cod_wp_transaccion	NUMBER;
		vc_item					VARCHAR2(10);
		vl_cod_sub_boleta		NUMBER;

	CURSOR C_SUB_BOLETA (vc_codsuboletas VARCHAR2) IS
		SELECT column_value
		FROM TABLE(split(vc_codsuboletas,'|'));

	BEGIN
		IF (ve_tipo_pago = 'PAGO_ABONO') THEN
		
			if(ve_cod_plan_pago is null)then
				vl_cod_deuda := ve_cod_deuda;
			else 
				SELECT	cod_deuda
				INTO	vl_cod_deuda
				FROM	plan_pago
				WHERE	cod_plan_pago = ve_cod_plan_pago;
			end if;
			
			spu_boleta ('INSERT'
						,NULL
						,KL_USUARIO_WEB
						,vl_cod_deuda
						,ve_cod_plan_pago
						,ve_monto_capital
						,ve_monto_interes
						,ve_monto_multa
						,ve_monto_moneda
						,ve_monto_pesos
						,ve_cod_tipo_descuento --cod_tipo_descuento
						,ve_dscto_utm	--dscto_capital
						,0				--dscto_interes
						,0				--dscto_multa
						,ve_dscto_pesos		--dscto_pesos
						,vl_current_date	--fecha_primera_cuota
						,1		--cant
						,'N');	--cartera_conocida

			SELECT  boleta_sq.CURRVAL
        	INTO vl_cod_boleta
        	FROM dual;

        	spu_sub_boleta ('INSERT'
							,NULL
							,vl_cod_boleta
							,1		-- nro_sub_boleta
							,vl_current_date	-- fecha_vencto
							,ve_monto_capital
							,ve_monto_interes
							,ve_monto_multa
							,NULL	-- fecha_pago
							,KL_COMPONENTE_VIGENTE); --se asigna por simplicidad el componente VIGENTE, ya que no se ocupa

			SELECT  sub_boleta_sq.CURRVAL
        	INTO vl_cod_sub_boleta
        	FROM dual;

			vl_codsuboletas	:= to_char(vl_cod_sub_boleta) || '|';

		END IF; --ve_tipo_pago = 'PAGO_ABONO'
		/* NO OCUPA PAGO PARCIAL.
		else
			vl_codsuboletas	:= ve_codsuboletas;	--para el caso cuando el pago es PAGO_PARCIAL
		*/
		INSERT INTO WP_TRANSACCION (COD_WP_TRANSACCION
								   ,COD_DEUDOR
								   ,TIPO
								   ,FECHA
								   ,EXITO
								   ,TIPO_PAGO_WEB
                                   ,IP
                                   ,SISTEMA_OPERATIVO
                                   ,NAVEGADOR
                                   ,MONTO)
					VALUES	(wp_transaccion_sq.NEXTVAL
							,ve_cod_deudor
							,ve_tipo_pago
							,vl_current_date
							,NULL
							,ve_tipo_pago_web
                            ,ve_ip
                            ,ve_sistema_operativo
                            ,ve_navegador
                            ,ve_monto_pesos); --mientras tanto se graba EXITO = NULL

		SELECT  wp_transaccion_sq.CURRVAL
    	INTO vl_cod_wp_transaccion
    	FROM dual;

		FOR a IN C_SUB_BOLETA (vl_codsuboletas) LOOP
			IF(a.column_value is null) THEN
          		COMMIT;
				RETURN;
      		END IF;

			insert into WP_SUB_BOLETA
								(COD_WP_SUB_BOLETA
								,COD_SUB_BOLETA
								,COD_WP_TRANSACCION)
						values	(wp_sub_boleta_sq.NEXTVAL
								,a.column_value
								,vl_cod_wp_transaccion);
		END LOOP;
		--se esta tomando el ultimo insert que se ingresa en WP_SUB_BOLETA
		--para obtener la cod_sub_boleta => webpay
		COMMIT;
	END;
END;
/

