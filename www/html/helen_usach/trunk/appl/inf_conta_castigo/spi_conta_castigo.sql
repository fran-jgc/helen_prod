CREATE OR REPLACE PROCEDURE spi_conta_castigo(ve_cod_familia_deuda	NUMBER
											 ,ve_cod_tipo_deuda		VARCHAR2
										     ,ve_ano				NUMBER
										     ,ve_castigo_AGOSTO		VARCHAR2
										     ,ve_cod_usuario        NUMBER)
AS
/*
exec  spi_conta_castigo(1, 'TODAS', 2017, 'N', 1);

select * from TEMP_CONTA_CASTIGO
where cod_usuario = 1
and rut is not null

*/
BEGIN
      DECLARE
		 K_ESTADO_EXIGIBLE				NUMBER:=2;
		 K_ESTADO_CANCELADA				NUMBER:=4;
		 K_ESTADO_PAGO_VIGENTE			NUMBER:=1;
		 vc_cod_deuda					NUMBER;
		 vl_saldo_3112_ant				NUMBER(14,4);
         vl_saldo_3112_ant_pesos		NUMBER;
         vl_abono_mora_cp				NUMBER(14,4);
         vl_abono_mora_cp_pesos			NUMBER;
         vl_saldo_cuota					NUMBER(14,4);
         vl_saldo_cuota_pesos			NUMBER;
         vl_valor_moneda				NUMBER(14,4);
         vl_cod_plan_pago				NUMBER;
         vl_fecha1						date;
         vl_fecha2						date;
         vl_mes1						NUMBER;
         vl_fecha_abono					date;
         vl_cod_estado_plan_pago		NUMBER;
         vl_abono						NUMBER(14,4);
         vl_abono_ficticio  NUMBER(14,4);
         vl_dscto						NUMBER(14,4);
         vl_porc						NUMBER(14,4);
         vl_porc_univ					NUMBER(10,2);
         vc_abono_mora_cp				NUMBER(14,4);
		 vc_abono_multa					NUMBER(14,4);
		 vc_fecha_abono					date;
		 vc_abono_pesos					NUMBER;
		 vl_count_cruzado				NUMBER;
		 vl_fecha_vencto_cuota			date;
		 vl_count_descto				NUMBER;
		 vl_descto_cr12					NUMBER(14,4):=0;
		 vl_descto_cr12_pesos			NUMBER:=0;
		 vl_cod_estado_pp				NUMBER;

      vl_RUT                NUMBER;
      vl_DIG_VERIF          VARCHAR2(100);
      vl_COD_TIPO_DEUDA     VARCHAR2(100);
      vl_NOM_CARTERA        VARCHAR2(100);
      vl_porc_interno       NUMBER;
			vl_porc_CF            NUMBER;
      vl_porc_CU            NUMBER;
      vl_dscto_CR12         NUMBER;

      vl_count_suspension   NUMBER;
      vl_cod_suspension     NUMBER;
      vl_count              NUMBER;

   CURSOR C_DEUDAS(ve_cod_usuario NUMBER) IS
    SELECT COD_DEUDA
    FROM TEMP_CONTA_CASTIGO
    where cod_usuario = ve_cod_usuario
    ORDER BY COD_DEUDA;


   CURSOR C_ABONO(vl_fecha1 DATE, vl_fecha2 DATE,vl_cod_plan_pago NUMBER) IS
	select MONTO_CAPITAL + MONTO_INTERES monto_mora
			,monto_multa
			,fecha_abono
			,monto_pesos
			,cod_plan_pago
	from ABONO
	where COD_PLAN_PAGO = vl_cod_plan_pago
	and COD_ESTADO_PAGO = K_ESTADO_PAGO_VIGENTE
    and FECHA_ABONO between vl_fecha1 and vl_fecha2;

  CURSOR C_ABONO_FICTICIO(vl_fecha1 DATE, vl_fecha2 DATE,vl_cod_plan_pago NUMBER) IS
	select MONTO_CAPITAL + MONTO_INTERES monto_mora
			,monto_multa
			,fecha_abono
			,monto_pesos
			,cod_plan_pago
	from abono_ficticio
	where COD_PLAN_PAGO = vl_cod_plan_pago
    and FECHA_ABONO between vl_fecha1 and vl_fecha2;

BEGIN

	delete TEMP_CONTA_CASTIGO where cod_usuario = ve_cod_usuario;

  if (ve_castigo_AGOSTO = 'S') then
		vl_fecha1 := to_date(To_Char(01)||'/06/'||To_Char(ve_ano)||' 00:00:00', 'dd/mm/yyyy hh24:mi:ss');
		vl_fecha2 := to_date(To_Char(31)||'/08/'||To_Char(ve_ano)||' 23:59:59', 'dd/mm/yyyy hh24:mi:ss');
		vl_mes1:= 5;
	else
		vl_fecha1 := to_date(To_Char(01)||'/01/'||To_Char(ve_ano)||' 00:00:00', 'dd/mm/yyyy hh24:mi:ss');
		vl_fecha2 := to_date(To_Char(31)||'/03/'||To_Char(ve_ano)||' 23:59:59', 'dd/mm/yyyy hh24:mi:ss');
		vl_mes1:= 12;
	end if;

   --deuda exigible
  INSERT INTO TEMP_CONTA_CASTIGO    (COD_DEUDA, COD_USUARIO)
  SELECT      distinct DE.COD_DEUDA, ve_cod_usuario

  FROM DEUDA DE, PLAN_PAGO PP, TIPO_DEUDA TD
  WHERE DE.cod_estado_deuda = K_ESTADO_EXIGIBLE
  AND DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
  AND PP.COD_DEUDA = DE.COD_DEUDA
  AND TD.COD_FAMILIA_DEUDA = ve_cod_familia_deuda
  AND (DE.COD_TIPO_DEUDA = ve_cod_tipo_deuda or 'TODAS' = ve_cod_tipo_deuda)
  AND PP.ANO_COBRO = ve_ano - 1
  AND To_number(To_Char(PP.FECHA_VENCTO,'mm')) in (vl_mes1)
  AND PP.NRO_CUOTA <> 0 -- dejar fuera cuota inicial CRI
  AND DE.COD_DEUDA NOT IN(1);

   --deuda cancelada
  INSERT INTO TEMP_CONTA_CASTIGO    (COD_DEUDA, COD_USUARIO)
  SELECT      distinct DE.COD_DEUDA, ve_cod_usuario
  FROM DEUDA DE, PLAN_PAGO PP, TIPO_DEUDA TD
  WHERE DE.cod_estado_deuda = K_ESTADO_CANCELADA
  AND DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
  AND PP.COD_DEUDA = DE.COD_DEUDA
  AND TD.COD_FAMILIA_DEUDA = ve_cod_familia_deuda
  AND (DE.COD_TIPO_DEUDA = ve_cod_tipo_deuda or 'TODAS' = ve_cod_tipo_deuda)
  AND PP.ANO_COBRO = ve_ano - 1
  AND To_number(To_Char(PP.FECHA_VENCTO,'mm')) in (vl_mes1)
  AND PP.NRO_CUOTA <> 0 -- dejar fuera cuota inicial CRI
  AND DE.COD_DEUDA NOT IN(1)
  AND (DE.COD_DEUDA IN (SELECT COD_DEUDA FROM ABONO a
    					WHERE a.COD_ESTADO_PAGO = 1 AND A.COD_DEUDA = DE.COD_DEUDA
    					AND To_number(To_Char(A.FECHA_ABONO,'yyyy')) >= ve_ano)
	or DE.COD_DEUDA IN (SELECT COD_DEUDA FROM DESCUENTO D WHERE D.COD_ESTADO_PAGO = 1 AND D.COD_DEUDA = DE.COD_DEUDA AND To_number(To_Char(D.FECHA_DESCUENTO,'yyyy')) >= ve_ano));

  --deudas condonacion CS
  INSERT INTO TEMP_CONTA_CASTIGO    (COD_DEUDA, COD_USUARIO)
  SELECT      distinct DE.COD_DEUDA, ve_cod_usuario
  FROM DEUDA DE, PLAN_PAGO PP, TIPO_DEUDA TD
  WHERE DE.cod_estado_deuda in (6, 121)	-- condonacion CS
  AND DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
  AND PP.COD_DEUDA = DE.COD_DEUDA
  AND TD.COD_FAMILIA_DEUDA = ve_cod_familia_deuda
  AND (DE.COD_TIPO_DEUDA = ve_cod_tipo_deuda or 'TODAS' = ve_cod_tipo_deuda)
  AND PP.ANO_COBRO = ve_ano - 1
  AND To_number(To_Char(PP.FECHA_VENCTO,'mm')) in (vl_mes1)
  AND PP.NRO_CUOTA <> 0 -- dejar fuera cuota inicial CRI
  AND DE.COD_DEUDA NOT IN(1);

  --deudas condonacion
  INSERT INTO TEMP_CONTA_CASTIGO    (COD_DEUDA, COD_USUARIO)
    SELECT      distinct DE.COD_DEUDA, ve_cod_usuario
    FROM DEUDA DE, PLAN_PAGO PP, deuda_condonacion dc, condonacion c, TIPO_DEUDA TD
    WHERE DE.cod_estado_deuda in (3)	-- condonacion
    AND DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
    AND PP.COD_DEUDA = DE.COD_DEUDA
    AND TD.COD_FAMILIA_DEUDA = ve_cod_familia_deuda
    AND (DE.COD_TIPO_DEUDA = ve_cod_tipo_deuda or 'TODAS' = ve_cod_tipo_deuda)
    AND PP.ANO_COBRO = ve_ano - 1
    AND To_number(To_Char(PP.FECHA_VENCTO,'mm')) in (vl_mes1)
    AND PP.NRO_CUOTA <> 0 -- dejar fuera cuota inicial CRI
    and dc.cod_deuda = DE.COD_DEUDA
    and c.cod_condonacion = dc.cod_condonacion
    and c.cod_tipo_condonacion = 1	-- condonacion CS por a?os plazo
    AND DE.COD_DEUDA NOT IN(1);

  COMMIT;
  --------------------------------------------------

    vl_valor_moneda := f_mon_get_valor_moneda(3, to_date(To_Char(31)||'/12/'||To_Char(ve_ano - 1)||'', 'dd/mm/yyyy'));

    FOR a IN C_DEUDAS(ve_cod_usuario) LOOP

    		vc_cod_deuda:= a.cod_deuda;
        vl_descto_cr12:=0;
				vl_descto_cr12_pesos:=0;


			-- saldo cuota al vencimiento cuota
			select  MONTO_CAPITAL + MONTO_INTERES
				   ,COD_PLAN_PAGO
				   ,f_pp_get_estado(cod_plan_pago, vl_fecha2)
				   ,fecha_vencto
			into vl_saldo_3112_ant
				, vl_cod_plan_pago
				,vl_cod_estado_plan_pago
				,vl_fecha_vencto_cuota
			from PLAN_PAGO
			where COD_DEUDA = vc_cod_deuda
			and ANO_COBRO = ve_ano - 1
      AND NRO_CUOTA <> 0; -- dejar fuera cuota inicial CRI


      ----------si tiene suspension el monto cuota se debe buscar en plan_pago_h
      --SUSPENSION
		  SELECT count(*)
		  into vl_count_suspension
		  FROM	suspension
		  WHERE	cod_deuda = vc_cod_deuda
        AND cod_estado_suspension = 2
        AND ano_suspension = ve_ano - 1 
        AND To_Number(to_char(FECHA_SUSPENSION,'yyyy')) >= ve_ano;

      IF (vl_count_suspension > 0) THEN
		    SELECT cod_suspension
		    into vl_cod_suspension
		    FROM	suspension
		    WHERE	cod_deuda = vc_cod_deuda
          AND cod_estado_suspension = 2
          AND ano_suspension = ve_ano - 1 
          AND To_Number(to_char(FECHA_SUSPENSION,'yyyy')) >= ve_ano
          AND ROWNUM = 1;

			  select  Count(*)
			  into vl_count
			  from PLAN_PAGO_H
			  where COD_DEUDA = vc_cod_deuda
			  and ANO_COBRO = ve_ano - 1
        AND origen = 'SUSPENSION'
        AND cod_origen = vl_cod_suspension;

--Dbms_Output.put_line(vl_cod_suspension );
        IF (vl_count > 0) THEN 
			    select  MONTO_CAPITAL + MONTO_INTERES
				      ,fecha_vencto
			    into vl_saldo_3112_ant
				    ,vl_fecha_vencto_cuota
			    from PLAN_PAGO_H
			    where COD_DEUDA = vc_cod_deuda
			    and ANO_COBRO = ve_ano - 1
          AND origen = 'SUSPENSION'
          AND cod_origen = vl_cod_suspension;
        END IF;
      END IF;

			vl_abono := 0;
			vl_dscto := 0;
      vl_abono_ficticio := 0;

			select nvl(sum(MONTO_CAPITAL + MONTO_INTERES), 0)
			into vl_abono
			from ABONO
			where COD_PLAN_PAGO = vl_cod_plan_pago
			and COD_ESTADO_PAGO = K_ESTADO_PAGO_VIGENTE
			and FECHA_ABONO < vl_fecha1;

			select nvl(sum(MONTO_CAPITAL + MONTO_INTERES), 0)
			into vl_dscto
			from DESCUENTO
			where COD_PLAN_PAGO = vl_cod_plan_pago
			and COD_ESTADO_PAGO = K_ESTADO_PAGO_VIGENTE
			and FECHA_DESCUENTO < vl_fecha1;

	    select nvl(sum(MONTO_CAPITAL + MONTO_INTERES), 0)
      INTO vl_abono_ficticio
	    from abono_ficticio
	    where COD_PLAN_PAGO = vl_cod_plan_pago
        and FECHA_ABONO < vl_fecha1;

/*
      vl_dscto_CR12 := 0;
      IF (vl_cod_estado_plan_pago <> 3) THEN    --<> cancelada
			  select nvl(sum(MONTO_CAPITAL + MONTO_INTERES), 0)
			  into vl_dscto_CR12
			  from DESCUENTO
			  where COD_PLAN_PAGO = vl_cod_plan_pago
			  and COD_ESTADO_PAGO = K_ESTADO_PAGO_VIGENTE
        AND cod_origen_pago = 22
			  and FECHA_DESCUENTO < vl_fecha1;

        vl_dscto := vl_dscto - vl_dscto_CR12;
      END IF;
*/
			vl_saldo_3112_ant := vl_saldo_3112_ant - vl_abono - vl_dscto - vl_abono_ficticio;
		  vl_saldo_3112_ant_pesos := round(vl_saldo_3112_ant * vl_valor_moneda, 0);

			-- abonos a mora en los posterior al vencto
			vl_abono_mora_cp := 0;
			vl_abono_mora_cp_pesos := 0;

			FOR b IN C_ABONO(vl_fecha1, vl_fecha2, vl_cod_plan_pago) LOOP
				vc_abono_mora_cp := b.monto_mora;
				vc_abono_multa	:= b.monto_multa;
				vc_fecha_abono	:= b.fecha_abono;
				vc_abono_pesos	:= b.monto_pesos;


				vl_abono_mora_cp := vl_abono_mora_cp + vc_abono_mora_cp;
			    if (vc_abono_multa = 0)then
					vl_abono_mora_cp_pesos := vl_abono_mora_cp_pesos + vc_abono_pesos;
				else
					vl_abono_mora_cp_pesos := vl_abono_mora_cp_pesos + ROUND(vc_abono_mora_cp * f_mon_get_valor_moneda(3, vc_fecha_abono), 0);
				end if;

			END LOOP;

			FOR b IN C_ABONO_FICTICIO(vl_fecha1, vl_fecha2, vl_cod_plan_pago) LOOP
				vc_abono_mora_cp := b.monto_mora;
				vc_abono_multa	:= b.monto_multa;
				vc_fecha_abono	:= b.fecha_abono;
				vc_abono_pesos	:= b.monto_pesos;

				vl_abono_mora_cp := vl_abono_mora_cp + vc_abono_mora_cp;
	      if (vc_abono_multa = 0)then
					vl_abono_mora_cp_pesos := vl_abono_mora_cp_pesos + vc_abono_pesos;
				else
					vl_abono_mora_cp_pesos := vl_abono_mora_cp_pesos + ROUND(vc_abono_mora_cp * f_mon_get_valor_moneda(3, vc_fecha_abono), 0);
				end if;
			END LOOP;

			if(ve_cod_tipo_deuda = 'CR12')then
				select count(*)
				into vl_count_descto
				from descuento
				where cod_plan_pago = vl_cod_plan_pago
				and cod_origen_pago = 22
				and cod_estado_pago = K_ESTADO_PAGO_VIGENTE;

				if(vl_count_descto > 0)then
					select sum(monto_moneda)
							,sum(monto_pesos)
					into vl_descto_cr12
						,vl_descto_cr12_pesos
					from descuento
					where cod_plan_pago = vl_cod_plan_pago
					and cod_origen_pago = 22
					and cod_estado_pago = K_ESTADO_PAGO_VIGENTE;
				else
					vl_descto_cr12:=0;
					vl_descto_cr12_pesos:=0;
				end if;

				--para las cuotas no PAGADAS CR12 no considera el dscto
				if(vl_saldo_3112_ant - vl_abono_mora_cp > 0 )THEN
          vl_saldo_3112_ant := vl_saldo_3112_ant + vl_descto_cr12;
          vl_saldo_3112_ant_pesos := vl_saldo_3112_ant_pesos +  vl_descto_cr12_pesos;

					vl_descto_cr12:=0;
					vl_descto_cr12_pesos:=0;
				end if;

			end if;

			-- saldos
			vl_saldo_cuota := vl_saldo_3112_ant - vl_abono_mora_cp;
			vl_saldo_cuota_pesos := vl_saldo_3112_ant_pesos - vl_abono_mora_cp_pesos;

			select  count(*)
			into vl_count_cruzado
			from CRUZADO
			where COD_DEUDA = vc_cod_deuda
			and COD_UNIVERSIDAD = 6;

			if (vl_count_cruzado = 0) then
				vl_porc := 100;
			else
				select  porc
				into vl_porc
				from CRUZADO
				where COD_DEUDA = vc_cod_deuda
				and COD_UNIVERSIDAD = 6;
			end if;

			vl_porc_univ := vl_porc;

		--cruzado interno
		select count(*)
		into vl_count_cruzado
		from cruzado_interno
		where COD_DEUDA = vc_cod_deuda
			and COD_ACREEDOR_INTERNO = 1;

		if (vl_count_cruzado=0)then
			vl_porc_interno := 100;
		else
			select porc
			into vl_porc_interno
			from cruzado_interno
			where COD_DEUDA = vc_cod_deuda
			and COD_ACREEDOR_INTERNO = 1;
		end if;

		--cruzado CF-CU
		select count(*)
		into vl_count_cruzado
		from cruzado_cfcu
		where COD_DEUDA = vc_cod_deuda;

		if (vl_count_cruzado=0)then
			vl_porc_CF := null;
			vl_porc_CU := null;
		else
			select porc_cf
            ,porc_cu
			into vl_porc_CF
          ,vl_porc_CU
			from cruzado_cfcu
			where COD_DEUDA = vc_cod_deuda;
		end if;


      SELECT DR.RUT
            ,DR.DIG_VERIF
            ,DE.COD_TIPO_DEUDA
            ,CA.NOM_CARTERA
      INTO vl_RUT
            ,vl_DIG_VERIF
            ,vl_COD_TIPO_DEUDA
            ,vl_NOM_CARTERA
      FROM DEUDOR DR, DEUDA DE, CARTERA CA
      WHERE DR.COD_DEUDOR = DE.COD_DEUDOR
      and DE.COD_CARTERA = CA.COD_CARTERA
      AND DE.COD_DEUDA = vc_cod_deuda;

      UPDATE TEMP_CONTA_CASTIGO
      SET RUT                         = vl_RUT
          ,DV                         = vl_DIG_VERIF
          ,COD_TIPO_DEUDA             = vl_COD_TIPO_DEUDA
			    ,SALDO_ANT                  = vl_saldo_3112_ant
					,SALDO_ANT_PESOS		        = vl_saldo_3112_ant_pesos
					,ABONO_MORA_CP			        = vl_abono_mora_cp
					,ABONO_MORA_CP_PESOS		    = vl_abono_mora_cp_pesos
					,SALDO_CUOTA				        = vl_saldo_cuota
					,SALDO_CUOTA_PESOS          = vl_saldo_cuota_pesos
					,FECHA_VENCTO_CUOTA         = vl_fecha_vencto_cuota
          ,NOM_CARTERA                = vl_NOM_CARTERA
			    ,ANO_CONTABLE               = ve_ano
			    ,HASTA_AGOSTO               = ve_castigo_AGOSTO
			    ,FECHA_REGISTRO             = sysdate
			    ,DESCUENTO_CR12             = vl_descto_cr12
			    ,DESCUENTO_CR12_PESOS       = vl_descto_cr12_pesos
			    ,PORC                       = vl_porc_univ
			    ,PORC_INTERNO               = vl_porc_interno
			    ,PORC_CF                    = vl_porc_CF
			    ,PORC_CU                    = vl_porc_CU
      WHERE cod_deuda = vc_cod_deuda
        AND cod_usuario = ve_cod_usuario;

      END LOOP;
      COMMIT;
END;
END;
/

