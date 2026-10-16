PROMPT CREATE OR REPLACE PROCEDURE sp_comp_cruzado
CREATE OR REPLACE PROCEDURE sp_comp_cruzado (
   ve_cod_comprobante   NUMBER
)
AS

BEGIN
  DECLARE vl_count_normal        NUMBER;
			vl_count_interno           NUMBER;
			vl_porc_normal             NUMBER;
			vl_porc_fondo              NUMBER;
			vl_univ_defecto            NUMBER;
      KL_PARAM_UNIV_DEFECTO      NUMBER:=3;
			KL_ACREEDOR_INT_FONDO      NUMBER:=2;
      KL_CUENTA_POR_RENDIR       NUMBER:=26;
			vl_sum_monto_debe          NUMBER;
			vl_sum_monto_haber         NUMBER;
			vl_saldo_acum              NUMBER;
			vl_saldo_acum_cruzado      NUMBER;
      vl_monto_debe              NUMBER;
      vl_monto_haber             NUMBER;
			vl_cod_origen_comprobante  NUMBER;
			KL_ORIGEN_INGRESO_PAGO     NUMBER:= 11;
			KL_ORIGEN_BCO              NUMBER:= 30;		
			KL_ORIGEN_OTRAS_UES        NUMBER:= 31;
			KL_ORIGEN_RENDICION		     NUMBER:= 25;
			KL_ORIGEN_PAGARE           NUMBER:= 12;
			vl_total_por_rendir        NUMBER;
			vl_cta_por_rendir          NUMBER;
			vl_cod_tipo_deuda          VARCHAR2(20);
			vl_tiene_item_comprobante  VARCHAR2(1);
			vl_cuenta_otros_ingresos   NUMBER;
			
      CURSOR C_ANALITICO IS
	    SELECT cod_analitico_comprobante
		    ,ac.cod_deuda
		    ,monto_debe
		    ,monto_haber
	    FROM analitico_comprobante ac, deuda d
	    WHERE cod_comprobante = ve_cod_comprobante
		    AND ac.cod_deuda = d.cod_deuda
		    AND cod_cuenta_contable <> f_comp_get_cuenta_tipo_deuda(cod_tipo_deuda, KL_CUENTA_POR_RENDIR) --los cursores son dinamicos, por lo tanto siempre se debe filtrar las ctas "por rendir", ya que posteriormente se crean registros analitico_comprobante
	    ORDER BY cod_analitico_comprobante;

      CURSOR C_ITEM_COMPROBANTE IS
	    SELECT cod_item_comprobante
		    ,cod_cuenta_contable
	    FROM item_comprobante
	    WHERE cod_comprobante = ve_cod_comprobante;

  
  BEGIN
  
    SELECT To_Number(f_get_parametro(KL_PARAM_UNIV_DEFECTO))
    INTO vl_univ_defecto
    FROM dual;
    
	/*
	para el caso de los comprobantes cuyo origen sea ingreso, se estan creando ANALITICO_COMPROBANTE, 
	pero se debe manejar que se cree un solo ITEM_COMPROBANTE con la suma de los ene ANALITICO_COMPROBANTE
	*/
	vl_tiene_item_comprobante := 'N';

	SELECT cod_origen_comprobante
  INTO vl_cod_origen_comprobante
	FROM comprobante
	WHERE cod_comprobante = ve_cod_comprobante;

	-- los comprobante de rendicion a otras ues NO se debe hacer nada respecto de cruzados
	IF (vl_cod_origen_comprobante = KL_ORIGEN_RENDICION) THEN 
		RETURN;
  END IF;
	-- los comprobante de carga pagare NO se debe hacer nada respecto de cruzados
  IF (vl_cod_origen_comprobante = KL_ORIGEN_PAGARE) THEN 
		RETURN;
  END IF;
  
	SELECT f.cod_cuenta_ch_fecha 
  INTO vl_cuenta_otros_ingresos
	FROM familia_deuda f, comprobante c
	WHERE c.cod_comprobante = ve_cod_comprobante
		AND c.cod_familia_deuda = f.cod_familia_deuda;


	vl_saldo_acum := 0;
	vl_saldo_acum_cruzado := 0;
	vl_sum_monto_debe := 0;
	vl_sum_monto_haber := 0;
  
  --dbms_output.put_line('a');

  FOR I IN C_ANALITICO LOOP
    --dbms_output.put_line('b');
  	--copia MONTO_DEBE y MONTO_HABER en campos historicos
		UPDATE analitico_comprobante
		SET monto_debe_original = monto_debe
			,monto_haber_original = monto_haber
		WHERE cod_analitico_comprobante = i.cod_analitico_comprobante;
    --dbms_output.put_line('c');
	 
    --cruzado normal
		SELECT count(*)
    INTO vl_count_normal
		FROM cruzado 
		WHERE cod_deuda = i.cod_deuda
			AND cod_universidad = vl_univ_defecto;


    IF (vl_count_normal=0) THEN
			vl_porc_normal := 1;
		ELSE			
			SELECT porc / 100.0000
      INTO vl_porc_normal
			FROM cruzado 
			WHERE cod_deuda = i.cod_deuda
				AND cod_universidad = vl_univ_defecto;
    END IF;
      
    --cruzado interno
		SELECT count(*)
    INTO vl_count_interno
		FROM cruzado_interno 
		WHERE cod_deuda = i.cod_deuda
			AND cod_acreedor_interno = KL_ACREEDOR_INT_FONDO;

		IF (vl_count_interno=0) THEN
			vl_porc_fondo := 1;
		ELSE
			SELECT porc / 100.0000
      INTO vl_porc_fondo
			FROM cruzado_interno 
			WHERE cod_deuda = i.cod_deuda
				AND cod_acreedor_interno = KL_ACREEDOR_INT_FONDO;
    END IF;

    IF ((vl_count_normal + vl_count_interno) > 0) THEN

      vl_saldo_acum := vl_saldo_acum + i.monto_debe - i.monto_haber;
      IF (vl_cod_origen_comprobante = KL_ORIGEN_INGRESO_PAGO or vl_cod_origen_comprobante = KL_ORIGEN_BCO or vl_cod_origen_comprobante = KL_ORIGEN_OTRAS_UES) THEN

        SELECT cod_tipo_deuda
        INTO vl_cod_tipo_deuda
				FROM deuda
				WHERE cod_deuda = i.cod_deuda;

        --solo para este tipo de deudas se calcula el cruzado
        IF (vl_cod_tipo_deuda = 'CS' or vl_cod_tipo_deuda = 'CSR' or vl_cod_tipo_deuda = 'CSN') THEN
          
          --calcula el monto haber a descontar para asignar a cuenta "por rendir"
					UPDATE analitico_comprobante
					SET monto_haber = round(monto_haber * vl_porc_normal * vl_porc_fondo, 0)
					WHERE cod_analitico_comprobante = i.cod_analitico_comprobante
						AND cod_cuenta_contable <> vl_cuenta_otros_ingresos;

					SELECT vl_sum_monto_debe + monto_debe
							,vl_sum_monto_haber + monto_haber
          INTO vl_sum_monto_debe
              ,vl_sum_monto_haber 
					FROM analitico_comprobante
					WHERE cod_analitico_comprobante = i.cod_analitico_comprobante;
          
          -- estamos al final de un subgrupo y debemos crear analitico_comprobante  e item_comprobante por rendir
          --dbms_output.put_line(vl_saldo_acum);
					IF (vl_saldo_acum = 0) THEN

							vl_total_por_rendir := vl_sum_monto_debe - vl_sum_monto_haber;
							vl_cta_por_rendir := f_comp_get_cuenta_tipo_deuda(vl_cod_tipo_deuda, KL_CUENTA_POR_RENDIR);
							
							IF (vl_tiene_item_comprobante = 'N') THEN
								spu_item_comprobante ('INSERT', NULL, ve_cod_comprobante, vl_cta_por_rendir,  0, vl_total_por_rendir); -- MONTO_HABER por ahora = @vl_total_por_rendir, luego en el cursor C_ITEM_COMPROBANTE se asigna la suma de ANALITICO_COMPROBANTE para la cta por rendir
								vl_tiene_item_comprobante := 'S';
							END IF;
							spu_analitico ('INSERT', NULL, ve_cod_comprobante, i.cod_deuda, vl_cta_por_rendir, 0, vl_total_por_rendir);

							vl_sum_monto_debe := 0;
							vl_sum_monto_haber := 0;
					END IF;
        END IF;
      ELSE
				UPDATE analitico_comprobante
				SET monto_debe = round(monto_debe * vl_porc_normal * vl_porc_fondo, 0)
					,monto_haber = round(monto_haber * vl_porc_normal * vl_porc_fondo, 0)
				WHERE cod_analitico_comprobante = i.cod_analitico_comprobante;
              
				SELECT monto_debe
						,monto_haber
        INTO vl_monto_debe
            ,vl_monto_haber
				FROM analitico_comprobante
				WHERE cod_analitico_comprobante = i.cod_analitico_comprobante;

				vl_saldo_acum_cruzado := vl_saldo_acum_cruzado + vl_monto_debe - vl_monto_haber;
					
				IF (vl_saldo_acum = 0) THEN
					-- estamos al final de un subgrupo y debemos forzar que cuadre
					IF (vl_saldo_acum_cruzado <> 0) THEN
						UPDATE analitico_comprobante
						SET monto_haber = monto_haber + vl_saldo_acum_cruzado 
						WHERE cod_analitico_comprobante = i.cod_analitico_comprobante;
          END IF;
					vl_saldo_acum_cruzado := 0;
				END IF;
      END IF;
    END IF;
  END LOOP;
         
	--actualiza MONTO_DEBE y MONTO_HABER en item_comprobante, en base al analitico_comprobante
	FOR J IN C_ITEM_COMPROBANTE LOOP
  	SELECT sum(monto_debe)
			,sum(monto_haber)
    INTO vl_sum_monto_debe
        ,vl_sum_monto_haber
		FROM analitico_comprobante
		WHERE cod_comprobante = ve_cod_comprobante	
			AND cod_cuenta_contable = j.cod_cuenta_contable;
			
		UPDATE item_comprobante
		SET monto_debe = vl_sum_monto_debe 
			,monto_haber = vl_sum_monto_haber
		WHERE cod_item_comprobante = j.cod_item_comprobante;
  END LOOP;
  END;
END;
/