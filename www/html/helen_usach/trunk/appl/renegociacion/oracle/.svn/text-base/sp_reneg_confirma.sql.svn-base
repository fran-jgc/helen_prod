CREATE OR REPLACE PROCEDURE sp_reneg_confirma(
  ve_cod_renegociacion  IN NUMBER
)
AS
BEGIN
  DECLARE
    K_ESTADO_DEUDA_NO_EXIGIBLE NUMBER :=1;
    K_COMP_NO_EXIGIBLE         NUMBER :=1;
    K_COMP_VENCIDA             NUMBER :=2;
    K_COMP_VIGENTE             NUMBER :=3;
    K_COMP_FUTURA              NUMBER :=4;
    K_EST_PP_MOROSA            NUMBER :=4;
    K_BITACORA_RENEGOCIACION   NUMBER :=23;
    vl_monto_capital        plan_pago.monto_capital%TYPE;
    vl_monto_interes        plan_pago.monto_interes%TYPE;
    vl_monto_multa          NUMBER;
    vl_monto_pesos          NUMBER;
    vl_dscto_capital        NUMBER;
    vl_dscto_interes        NUMBER;
    vl_dscto_multa          NUMBER;
    vl_dscto_pesos          NUMBER;
    vl_monto_moneda         NUMBER;
    vl_cod_boleta           NUMBER;
    i                       NUMBER;
    vl_fecha_vencto_sb      VARCHAR(10);
    vl_cod_componente_deuda NUMBER;
    vl_monto_capital_total  NUMBER;
    vl_monto_interes_total  NUMBER;
    vl_monto_multa_total    NUMBER;
    vl_total                NUMBER;
    vl_tasa_int_penal       tipo_deuda.tasa_interes_penal%TYPE;
    vl_fecha_hoy            DATE;
    vl_multa                NUMBER;
    vl_k_a_pagar            NUMBER;
    vl_in_a_pagar           NUMBER;
    vl_k_sub_boleta         NUMBER;
    vl_in_sub_boleta        NUMBER;
    vl_suma_capital	        NUMBER;
	  vl_suma_interes	        NUMBER;
	  vl_suma_multa 	        NUMBER;
	  vl_suma_total 	        NUMBER;
    vl_nro_fila             NUMBER;
    vl_numMeses             NUMBER;
    vl_dif_meses            NUMBER;
    vl_cod_moneda           NUMBER;
    vl_cod_estado_deuda     NUMBER;
    vl_cod_estado_plan_pago NUMBER;
    vl_cod_plan_pago_vig    NUMBER;
    vl_cod_deudor           NUMBER;

    CURSOR C_RENEGOCIACION (vec_cod_renegociacion NUMBER)IS
      SELECT  r.cod_usuario
              ,r.cod_deuda
              ,pp.cod_plan_pago
              ,pp.fecha_vencto
              ,pp.ano_cobro
              ,ppr.fecha_primer_pago
              ,ppr.cant_cuota
              ,ppr.cod_plan_pago_renegociacion
      FROM    renegociacion r
              ,plan_pago_renegociacion ppr
              ,plan_pago pp
      WHERE   r.cod_renegociacion = ppr.cod_renegociacion
              AND pp.cod_plan_pago = ppr.cod_plan_pago
              AND r.cod_renegociacion = vec_cod_renegociacion;

  BEGIN
    FOR a IN C_RENEGOCIACION (ve_cod_renegociacion) LOOP
      sp_boleta_calculo(a.cod_deuda
                        ,a.cod_plan_pago
                        ,NULL
                        ,vl_monto_capital
                        ,vl_monto_interes
                        ,vl_monto_multa
                        ,vl_monto_pesos
                        ,vl_dscto_capital
                        ,vl_dscto_interes
                        ,vl_dscto_multa
                        ,vl_dscto_pesos);


      spu_boleta( 'INSERT'
                  ,NULL
                  ,a.cod_usuario
                  ,a.cod_deuda
                  ,a.cod_plan_pago
                  ,0--vl_monto_capital
                  ,0--vl_monto_interes
                  ,0--vl_monto_multa
                  ,0--vl_monto_moneda --??????????????????????
                  ,0--vl_monto_pesos
                  ,vl_dscto_capital
                  ,vl_dscto_interes
                  ,vl_dscto_multa
                  ,vl_dscto_pesos
                  ,a.fecha_primer_pago
                  ,a.cant_cuota);

      SELECT  boleta_sq.CURRVAL
      INTO    vl_cod_boleta
      FROM    DUAL;

      i := 1;
      vl_monto_capital_total  := 0;
      vl_monto_interes_total  := 0;
      vl_monto_multa_total    := 0;
      vl_fecha_hoy := SYSDATE;

      SELECT  tasa_interes_penal
              ,cod_moneda
              ,cod_estado_deuda
              ,f_deuda_cuota_vigente(d.cod_deuda)
      INTO    vl_tasa_int_penal
              ,vl_cod_moneda
              ,vl_cod_estado_deuda
              ,vl_cod_plan_pago_vig
      FROM    deuda d
              ,tipo_deuda td
      WHERE   d.cod_tipo_deuda = td.cod_tipo_deuda
              AND cod_deuda = a.cod_deuda;

      vl_total := vl_monto_capital + vl_monto_interes + vl_monto_multa;

      /**********texto comentado*******************/
      vl_k_a_pagar 		    := vl_monto_capital;
      vl_in_a_pagar       := Round(vl_monto_interes - vl_dscto_interes,4);
      vl_k_sub_boleta     := Round(vl_k_a_pagar / a.cant_cuota,4);
      vl_in_sub_boleta    := Round(vl_in_a_pagar / a.cant_cuota,4);

      vl_suma_capital	    := 0;
	    vl_suma_interes	    := 0;
	    vl_suma_multa 	    := 0;
	    vl_suma_total 	    := 0;

      WHILE i  <= a.cant_cuota  LOOP
        vl_nro_fila := i;
        SELECT  To_char(Add_Months(a.fecha_primer_pago, i - 1), 'dd/mm/yyyy')
        INTO    vl_fecha_vencto_sb
			  FROM 	  DUAL;
        IF (i = a.cant_cuota)THEN
          vl_k_sub_boleta   := vl_k_a_pagar	- ((a.cant_cuota - 1) * vl_k_sub_boleta );
			    vl_in_sub_boleta  := vl_in_a_pagar - ((a.cant_cuota - 1) * vl_in_sub_boleta);
        END IF;

        IF (a.fecha_vencto > To_Date(vl_fecha_vencto_sb,'dd/mm/yyyy')) THEN
          vl_multa      := 0;
			    vl_numMeses   := 0;
        ELSE
          vl_numMeses   := Abs(Round(Months_Between( To_Date(vl_fecha_vencto_sb,'dd/mm/yyyy'), a.fecha_vencto),0));
          vl_dif_meses  := vl_numMeses;
			    vl_multa      := Round((vl_k_sub_boleta + vl_in_sub_boleta) * vl_tasa_int_penal/100 * vl_dif_meses,4);
        END IF;

        vl_total := Round(vl_k_sub_boleta + vl_in_sub_boleta + vl_multa + (0 / a.cant_cuota),4); --el cero es por otros_sub_boleta

        IF (vl_cod_estado_deuda = K_ESTADO_DEUDA_NO_EXIGIBLE) THEN
				  vl_cod_componente_deuda := K_COMP_NO_EXIGIBLE;
			  ELSIF(vl_cod_estado_plan_pago = K_EST_PP_MOROSA) THEN
				  vl_cod_componente_deuda := K_COMP_VENCIDA;
			  ELSIF(a.cod_plan_pago = vl_cod_plan_pago_vig) THEN
				  vl_cod_componente_deuda := K_COMP_VIGENTE;
			  ELSE			--cualquier otro caso es FUTURA
				  vl_cod_componente_deuda := K_COMP_FUTURA;
        END IF;

        --Dbms_Output.Put_line('vl_fecha_vencto_sb: '||vl_fecha_vencto_sb);
        spu_sub_boleta( 'INSERT'
                        ,NULL
                        ,vl_cod_boleta
                        ,i
                        ,vl_fecha_vencto_sb
                        ,vl_k_sub_boleta
                        ,vl_in_sub_boleta
                        ,vl_multa
                        ,NULL --fecha_pago
                        ,vl_cod_componente_deuda);

        vl_suma_capital   := vl_suma_capital + vl_k_sub_boleta;
        vl_suma_interes   := vl_suma_interes + vl_in_sub_boleta;
        vl_suma_multa     := vl_suma_multa + vl_multa;
        vl_suma_total     := vl_suma_total + (vl_suma_multa + vl_suma_interes + vl_suma_capital);
        i := i + 1;
      END LOOP;
      spu_boleta( 'UPDATE'
                  ,vl_cod_boleta
                  ,a.cod_usuario
                  ,a.cod_deuda
                  ,a.cod_plan_pago
                  ,vl_monto_capital_total
                  ,vl_monto_interes_total
                  ,vl_monto_multa_total
                  ,vl_suma_total
                  ,vl_suma_total * f_mon_get_valor_moneda(vl_cod_moneda)
                  ,vl_dscto_capital
                  ,vl_dscto_interes
                  ,vl_dscto_multa
                  ,vl_dscto_pesos
                  ,a.fecha_primer_pago
                  ,a.cant_cuota);

      UPDATE plan_pago_renegociacion
      SET cod_boleta = vl_cod_boleta
      WHERE cod_plan_pago_renegociacion = a.cod_plan_pago_renegociacion;

      SELECT  cod_deudor
      INTO    vl_cod_deudor
      FROM    deuda
      WHERE   cod_deuda = a.cod_deuda;

      spu_bitacora_deudor ('Renegociación, deuda : ' || To_Char(a.cod_deuda), K_BITACORA_RENEGOCIACION, vl_cod_deudor, a.cod_usuario);

    END LOOP;
  END;
END sp_reneg_confirma;