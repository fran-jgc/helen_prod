PROMPT CREATE OR REPLACE PROCEDURE sp_ingreso_distribuye
CREATE OR REPLACE PROCEDURE sp_ingreso_distribuye(ve_cod_plan_pago	NUMBER
															,ve_fecha					DATE-- fecha abono o descuento
															,ve_cod_componente_deuda	NUMBER
															,ves_monto_capital			IN OUT	NUMBER
															,ves_monto_interes			IN OUT	NUMBER
															,ves_monto_multa			IN OUT	NUMBER
															,vs_excedente				OUT	NUMBER)
AS
BEGIN
	DECLARE K_COMPONENTE_DEUDA_NO_EXIGIBLE	NUMBER:=1;
			vl_monto_capital				NUMBER;
			vl_monto_interes				NUMBER;
			vl_monto_multa					NUMBER;
			vl_monto_pesos					NUMBER;
			vl_saldo_K						NUMBER;
			vl_saldo_IN						NUMBER;
			vl_saldo_IP						NUMBER;
			vl_excedente_moneda				NUMBER;

	BEGIN
		IF (ve_cod_componente_deuda = K_COMPONENTE_DEUDA_NO_EXIGIBLE) THEN
			-- en deudas no exigible todos el abono es a K
			vl_monto_capital := ves_monto_capital + ves_monto_interes + ves_monto_multa;
			vl_monto_interes := 0;
			vl_monto_multa := 0;

			--** FALTA manejo e excedente
		ELSE
			vl_monto_capital := ves_monto_capital;
			vl_monto_interes := ves_monto_interes;
			vl_monto_multa := ves_monto_multa;

			-- obtiene los saldos de K, IN y multa
			vl_saldo_K := f_pp_saldo_capital_caja(ve_cod_plan_pago, ve_fecha);
      IF (vl_saldo_K < 0) THEN
        vl_saldo_K := 0;
      END IF;

      vl_saldo_IN := f_pp_saldo_interes_normal_caja(ve_cod_plan_pago, ve_fecha);
      IF(vl_saldo_IN < 0)THEN
        vl_saldo_IN := 0;
      END IF;

      vl_saldo_IP := f_pp_saldo_multa_caja(ve_cod_plan_pago, ve_fecha);
      IF(vl_saldo_IP < 0)THEN
        vl_saldo_IP := 0;
      END IF;

			vl_excedente_moneda := 0;

			IF (vl_monto_capital > vl_saldo_K) THEN
				vl_excedente_moneda := vl_excedente_moneda + vl_monto_capital - vl_saldo_K;
				vl_monto_capital := vl_saldo_K;
			END IF;
			IF (vl_monto_interes > vl_saldo_IN) THEN
				vl_excedente_moneda := vl_excedente_moneda + vl_monto_interes - vl_saldo_IN;
				vl_monto_interes := vl_saldo_IN;
			END IF;
			IF (vl_monto_multa > vl_saldo_IP) THEN
				vl_excedente_moneda := vl_excedente_moneda + vl_monto_multa - vl_saldo_IP;
				vl_monto_multa := vl_saldo_IP;
			END IF;

			-- reasigna excedente en la misma cuota si es posible
			IF (vl_excedente_moneda > 0) THEN
				-- reclacula los saldos con el abono
				vl_saldo_K	:= vl_saldo_K - vl_monto_capital;
				vl_saldo_IN	:= vl_saldo_IN - vl_monto_interes;
				vl_saldo_IP	:= vl_saldo_IP - vl_monto_multa;

				-- reasigna 1ro multa, luego IN, y finalmente K
				IF (vl_saldo_IP > 0) THEN
					IF (vl_excedente_moneda >= vl_saldo_IP) THEN
						vl_monto_multa := vl_monto_multa + vl_saldo_IP;
						vl_excedente_moneda := vl_excedente_moneda - vl_saldo_IP;
					ELSE
						vl_monto_multa := vl_monto_multa + vl_excedente_moneda;
						vl_excedente_moneda := 0;
					END IF;
				END IF;

				-- intenta asignar excedente a IN
				IF (vl_saldo_IN > 0) THEN
					IF (vl_excedente_moneda >= vl_saldo_IN) THEN
						vl_monto_interes := vl_monto_interes + vl_saldo_IN;
						vl_excedente_moneda := vl_excedente_moneda - vl_saldo_IN;
					ELSE
						vl_monto_interes := vl_monto_interes + vl_excedente_moneda;
						vl_excedente_moneda := 0;
					END  IF;
				END IF;

				-- intenta asignar excedente a K
				IF (vl_saldo_K > 0) THEN
					IF (vl_excedente_moneda >= vl_saldo_K) THEN
						vl_monto_capital := vl_monto_capital + vl_saldo_K;
						vl_excedente_moneda := vl_excedente_moneda - vl_saldo_K;
					ELSE
						vl_monto_capital := vl_monto_capital + vl_excedente_moneda;
						vl_excedente_moneda := 0;
					END IF;
				END IF;
			END IF;
		END IF;

		IF (vl_excedente_moneda > 0) THEN
			vs_excedente := vl_excedente_moneda;
		END IF;

		ves_monto_capital := vl_monto_capital;
		ves_monto_interes := vl_monto_interes;
		ves_monto_multa := vl_monto_multa;
	END;
END;
/