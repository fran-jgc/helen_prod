CREATE OR REPLACE PROCEDURE sp_boleta_calculo(ve_cod_deuda	NUMBER
											 ,ve_cod_plan_pago		NUMBER
											 ,ve_cod_tipo_descuento	NUMBER DEFAULT NULL
											 ,ve_monto_pago_pesos	NUMBER DEFAULT NULL)
AS
/******************************************************************************
   NAME:       spu_sp_boleta_calculoboleta

   Calcula los valores conque se debe crear una boleta
   ve_cod_deuda         : codigo de la deuda
   ve_cod_plan_pago     : codigo plan pago, si viene en null se trata de una deuda no exigible
   ve_monto_pago_pesos  : Monto que desea abonor el deudor, si viene en null se asume que desea abonar el saldo total de cuota o deuda
   vs_monto_capital     : calculo del Abono a capital
   vs_monto_interes     : calculo del Abono a interes normal
   vs_monto_multa       : calculo del Abono a multa
   vs_monto_pesos       : calculo del Abono total en pesos, se calcula a la fecha de sysdate
   vs_dscto_capital     : calculo del Dscto a capital
   vs_dscto_interes     : calculo del Dscto a interes normal
   vs_dscto_multa       : calculo del Dscto a multa
   vs_dscto_pesos       : calculo del Dscto total en pesos, se calcula a la fecha de sysdate

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        10/11/2009   Victor Melo

******************************************************************************/
BEGIN
	DECLARE	vl_saldo_capital		NUMBER;
			vl_saldo_interes		NUMBER;
		    vl_saldo_multa			NUMBER;
		    vl_saldo_total			NUMBER;
		    vl_factor_capital		NUMBER;
		    vl_factor_interes		NUMBER;
		    vl_factor_multa			NUMBER;
		    vl_cod_tipo_deuda		VARCHAR2(10);
		    vl_ano_actual			NUMBER;
		    vl_ano_cobro			NUMBER;
		    vl_monto_pago_pesos		NUMBER;
		    vl_valor_moneda			NUMBER;
		    vl_cod_moneda			NUMBER;
		    vl_decimo				NUMBER;
		    vl_saldo_total_pesos	NUMBER;
		    vl_rango_dscto			NUMBER;
		    vl_porc_dscto			NUMBER;
		    vl_dscto_total_pesos	NUMBER;
		    vl_dscto_total			NUMBER;
		    vl_monto_pago			NUMBER;
		    vl_saldo_deuda			NUMBER;
		    vl_dif_meses			NUMBER;
		    vl_fecha_vencto			DATE;
		    vl_tasa_int_penal		NUMBER;
		    vl_dif					NUMBER;
		    vl_dif2					NUMBER;
		    vl_monto_capital		NUMBER;
		    vl_monto_multa			NUMBER;
		    vs_monto_capital		NUMBER; 
			vs_monto_interes		NUMBER; 
			vs_monto_multa			NUMBER; 
			vs_monto_pesos			NUMBER; 
			vs_dscto_capital		NUMBER; 
			vs_dscto_interes		NUMBER; 
			vs_dscto_multa			NUMBER; 
			vs_dscto_pesos			NUMBER;


	BEGIN
		IF (ve_cod_plan_pago IS NULL) THEN
			vl_saldo_deuda := f_deuda_saldo_total(ve_cod_deuda, SYSDATE);
			
			select	round(vl_saldo_deuda / (1 + (td.tasa_interes_credito/100)),4)
			INTO	vl_saldo_capital
			from	tipo_deuda td, deuda d
			WHERE	d.cod_deuda = ve_cod_deuda
			  AND	td.cod_tipo_deuda = d.cod_tipo_deuda;
			  
			vl_saldo_interes := vl_saldo_deuda - vl_saldo_capital;
			vl_saldo_multa := 0;
			
			-- obtiene datos de la deuda
			SELECT   d.cod_tipo_deuda
					,To_Number(To_Char(SYSDATE, 'yyyy'))
					,td.cod_moneda
			INTO	 vl_cod_tipo_deuda 
					,vl_ano_actual 
					,vl_cod_moneda
			FROM	deuda d, tipo_deuda td
			WHERE	d.cod_deuda = ve_cod_deuda
			AND		td.cod_tipo_deuda = d.cod_tipo_deuda;
		ELSE
			vl_saldo_capital := f_pp_saldo_capital(ve_cod_plan_pago, SYSDATE);
			vl_saldo_interes := f_pp_saldo_interes_normal(ve_cod_plan_pago, SYSDATE);
			vl_saldo_multa := f_pp_saldo_multa(ve_cod_plan_pago, SYSDATE);
			
			-- obtiene datos de la deuda
			SELECT   p.ano_cobro
					,d.cod_tipo_deuda
					,To_Number(To_Char(SYSDATE, 'yyyy'))
					,td.cod_moneda
					,p.fecha_vencto
					,td.tasa_interes_penal / 100.0000
			INTO	 vl_ano_cobro
					,vl_cod_tipo_deuda
					,vl_ano_actual
					,vl_cod_moneda
					,vl_fecha_vencto
					,vl_tasa_int_penal
			FROM plan_pago p, deuda d, tipo_deuda td
			WHERE p.cod_plan_pago = ve_cod_plan_pago
			AND d.cod_deuda = p.cod_deuda
			AND td.cod_tipo_deuda = d.cod_tipo_deuda;
		END IF;
		
		vl_saldo_total := vl_saldo_capital + vl_saldo_interes + vl_saldo_multa;
		
		IF (vl_saldo_total = 0) THEN
			INSERT INTO TEMP_TOTAL_BOLETA 
						(D_MONTO_CAPITAL	
						,D_MONTO_INTERES	
						,D_MONTO_MULTA	
						,D_MONTO_PESOS	
						,D_DSCTO_CAPITAL	
						,D_DSCTO_INTERES	
						,D_DSCTO_MULTA	
						,D_DSCTO_PESOS)
				SELECT	 0	
						,0	
						,0	
						,0	
						,0	
						,0	
						,0	
						,0
				FROM DUAL;
			RETURN;
		END IF;
		
		vl_factor_capital := vl_saldo_capital / vl_saldo_total;
		vl_factor_interes := vl_saldo_interes / vl_saldo_total;
		vl_factor_multa := vl_saldo_multa / vl_saldo_total;

		-- calula valores en pesos
	    vl_valor_moneda := f_mon_get_valor_moneda(vl_cod_moneda);
	    vl_saldo_total_pesos := Round(vl_saldo_total * vl_valor_moneda, 0);
    
    	IF (ve_monto_pago_pesos IS NULL) THEN
      		vl_monto_pago_pesos := vl_saldo_total_pesos;
	    ELSE
	      vl_monto_pago_pesos := ve_monto_pago_pesos;
	    END IF;
	    
	    -------------------------verifica si tiene derecho a descuento
	    vs_dscto_capital := 0;
	    vs_dscto_interes := 0;
	    vs_dscto_multa := 0;
	    vs_dscto_pesos := 0;
	    
	    IF (ve_cod_tipo_descuento is not null)	THEN
			select	porc_descuento
			INTO	vl_porc_dscto
			from tipo_descuento
			where cod_tipo_descuento = ve_cod_tipo_descuento; 

			vl_dscto_total_pesos := Round((vl_saldo_total_pesos * vl_porc_dscto) / 100, 0);
			vl_dscto_total := Round(vl_dscto_total_pesos / vl_valor_moneda, 4);
			
			-- el dscto se va todo a capital
			vs_dscto_capital := vl_dscto_total;
			vs_dscto_pesos := vl_dscto_total_pesos;

			IF (vl_monto_pago_pesos > (vl_saldo_total_pesos - vl_dscto_total_pesos)) THEN 
				vl_monto_pago_pesos := vl_saldo_total_pesos - vl_dscto_total_pesos;
			END IF;
		ELSE
			IF (vl_monto_pago_pesos > vl_saldo_total_pesos) THEN 
				vl_monto_pago_pesos := vl_saldo_total_pesos;
	      	END IF;
		END IF;
		---------- fin calculo dscto
		
		-- calculo de las componetes a pagar
		IF (ve_monto_pago_pesos IS NULL) THEN
			vs_monto_capital := vl_saldo_capital - vs_dscto_capital;
			vs_monto_interes := vl_saldo_interes;
			vs_monto_multa := vl_saldo_multa;
			vs_monto_pesos := vl_monto_pago_pesos;
	    ELSE
			vs_monto_pesos := vl_monto_pago_pesos;
			vl_monto_pago := Round(vs_monto_pesos / vl_valor_moneda, 4);
			vs_monto_capital := Round(vl_monto_pago * vl_factor_capital, 4);
			vs_monto_interes := Round(vl_monto_pago * vl_factor_interes, 4);
   	
			-- Para la multa no se usa el factor, sino que se calcula respecto del K + IN que se paga
			IF (ve_cod_plan_pago IS NULL)	THEN
				vs_monto_multa := 0;
				vl_dif := vl_monto_pago - (vs_monto_capital + vs_monto_interes + vs_monto_multa);
				vs_monto_capital := vs_monto_capital + vl_dif;
			ELSE
				---CEILING  REFERENCIA
				vl_dif_meses := ceil(MONTHS_BETWEEN(sysdate, vl_fecha_vencto));
				IF (vl_dif_meses < 0)	THEN
					vs_monto_multa := 0;
				ELSE
					vs_monto_multa := Round((vs_monto_capital + vs_monto_interes) * vl_tasa_int_penal * vl_dif_meses, 4);
					vl_monto_capital := vs_monto_capital;
					vl_monto_multa := vs_monto_multa;
					vl_dif := vl_monto_pago - (vl_monto_capital + vs_monto_interes + vl_monto_multa);
					
					WHILE (Abs(vl_dif) <> 0)
					LOOP
						IF (vl_dif > 0) THEN
							vl_monto_capital := vl_monto_capital + 0.0001;
						ELSE
							vl_monto_capital := vl_monto_capital - 0.0001;
            			END IF;
            			
            			vl_monto_multa := Round((vl_monto_capital + vs_monto_interes) * vl_tasa_int_penal * vl_dif_meses, 4);
						vl_dif2 := vl_monto_pago - (vl_monto_capital + vs_monto_interes + vl_monto_multa);
						
						IF (Abs(vl_dif2) < Abs(vl_dif))	THEN
							vl_dif := vl_dif2;
							vs_monto_capital := vl_monto_capital;
							vs_monto_multa := vl_monto_multa;
						ELSE
							vl_dif := 0;
						END IF;
					END LOOP;
				END IF;
			END IF;
	    END IF;
	    INSERT INTO TEMP_TOTAL_BOLETA 
						(D_MONTO_CAPITAL	
						,D_MONTO_INTERES	
						,D_MONTO_MULTA	
						,D_MONTO_PESOS	
						,D_DSCTO_CAPITAL	
						,D_DSCTO_INTERES	
						,D_DSCTO_MULTA	
						,D_DSCTO_PESOS)
				SELECT	 vs_monto_capital	
						,vs_monto_interes	
						,vs_monto_multa	
						,vs_monto_pesos	
						,vs_dscto_capital	
						,vs_dscto_interes	
						,vs_dscto_multa	
						,vs_dscto_pesos
				FROM DUAL;
  END;
END;
/