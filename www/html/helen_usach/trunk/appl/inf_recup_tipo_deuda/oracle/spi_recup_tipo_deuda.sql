CREATE OR REPLACE PROCEDURE spi_recup_tipo_deuda(ve_fecha_inicio      DATE
                                                ,ve_fecha_termino     DATE
                                                ,ve_cod_familia_deuda NUMBER)
AS
BEGIN
  DECLARE
    K_ABONO_VIGENTE                 NUMBER:=1;
    K_COMPONENTE_NO_EXIGIBLE        NUMBER:=1;
    K_COMPONENTE_VENCIDA            NUMBER:=2;
    K_COMPONENTE_VIGENTE            NUMBER:=3;
    K_COMPONENTE_FUTURA             NUMBER:=4;
    K_COMPONENTE_OTRO               NUMBER:=5;

    vl_count                        NUMBER;
    vl_monto_mora						        NUMBER;
	  vl_monto_multa					        NUMBER;
	  vl_monto_capital_vigente			  NUMBER;
	  vl_monto_interes_vigente			  NUMBER;
	  vl_monto_capital_futuro			    NUMBER;
	  vl_monto_interes_futuro			    NUMBER;
	  vl_monto_capital_no_exigible		NUMBER;
	  vl_monto_interes_no_exgigible		NUMBER;
	  --pesos
	  vl_monto_total_no_exgigible_p		NUMBER;
	  vl_monto_mora_total_pesos	        NUMBER;
	  vl_monto_total_vigente_pesos		NUMBER;
	  vl_monto_total_futuro_pesos		NUMBER;
	  
    CURSOR c_abono IS
    SELECT d.cod_tipo_deuda
          ,cod_componente_deuda
          ,Sum(monto_capital) monto_capital
          ,Sum(monto_interes) monto_interes
          ,Sum(monto_multa) monto_multa
          ,Sum(monto_pesos) monto_pesos
    FROM abono a, deuda d, tipo_deuda t
    WHERE a.fecha_abono BETWEEN ve_fecha_inicio AND ve_fecha_termino
      AND a.cod_estado_pago = K_ABONO_VIGENTE
      AND d.cod_deuda = a.cod_deuda
      AND t.cod_tipo_deuda = d.cod_tipo_deuda
      AND t.cod_familia_deuda = ve_cod_familia_deuda
    GROUP BY d.cod_tipo_deuda
          ,cod_componente_deuda;

  BEGIN
    FOR a IN c_abono LOOP
      vl_monto_mora := 0;
	    vl_monto_multa := 0;
	    vl_monto_capital_vigente := 0;
	    vl_monto_interes_vigente := 0;
	    vl_monto_capital_futuro := 0;
	    vl_monto_interes_futuro := 0;
	    vl_monto_capital_no_exigible := 0;
	    vl_monto_interes_no_exgigible := 0;
	    --pesos
	  	vl_monto_total_no_exgigible_p := 0;
	  	vl_monto_mora_total_pesos := 0;
	  	vl_monto_total_vigente_pesos := 0;
	  	vl_monto_total_futuro_pesos := 0;
	    

      IF (a.cod_componente_deuda = K_COMPONENTE_NO_EXIGIBLE) THEN
  	    vl_monto_capital_no_exigible := vl_monto_capital_no_exigible + a.monto_capital;
	      vl_monto_interes_no_exgigible := vl_monto_interes_no_exgigible + a.monto_interes;
	      vl_monto_total_no_exgigible_p := vl_monto_total_no_exgigible_p + a.monto_pesos;
      ELSIF (a.cod_componente_deuda = K_COMPONENTE_VENCIDA) THEN
        vl_monto_mora := vl_monto_mora + a.monto_capital + a.monto_interes;
        vl_monto_multa := vl_monto_multa + a.monto_multa;
        vl_monto_mora_total_pesos := vl_monto_mora_total_pesos + a.monto_pesos;
	  ELSIF (a.cod_componente_deuda = K_COMPONENTE_VIGENTE OR a.cod_componente_deuda = K_COMPONENTE_OTRO) THEN
  	    vl_monto_capital_vigente := vl_monto_capital_vigente + a.monto_capital;
	      vl_monto_interes_vigente := vl_monto_interes_vigente + a.monto_interes;
	      vl_monto_total_vigente_pesos := vl_monto_total_vigente_pesos + a.monto_pesos;
      ELSIF (a.cod_componente_deuda = K_COMPONENTE_FUTURA) THEN
  	    vl_monto_capital_futuro := vl_monto_capital_futuro + a.monto_capital;
	      vl_monto_interes_futuro := vl_monto_interes_futuro + a.monto_interes;
	      vl_monto_total_futuro_pesos := vl_monto_total_futuro_pesos + a.monto_pesos; 
      END IF;
		 
      SELECT Count(*)
      INTO vl_count
      FROM TEMP_RECUP_TIPO_DEUDA
      WHERE COD_TIPO_DEUDA = a.cod_tipo_deuda;

      IF (vl_count=0) THEN
        INSERT INTO TEMP_RECUP_TIPO_DEUDA
          (COD_TIPO_DEUDA				,MONTO_MORA
          ,MONTO_MULTA					,MONTO_MULTA_TOTAL_PESOS
          ,MONTO_CAPITAL_VIGENTE		,MONTO_INTERES_VIGENTE
          ,MONTO_CI_TOTAL_VIGENTE_PESOS ,MONTO_CAPITAL_FUTURO
          ,MONTO_INTERES_FUTURO			,MONTO_CI_TOTAL_FUTURO_PESOS		
          ,MONTO_CAPITAL_NO_EXIGIBLE	,MONTO_INTERES_NO_EXGIGIBLE
          ,MONTO_CI_TOTAL_NO_EXGIGIBLE_P)
        VALUES
          (a.cod_tipo_deuda				,vl_monto_mora
          ,vl_monto_multa				,vl_monto_mora_total_pesos
          ,vl_monto_capital_vigente     ,vl_monto_interes_vigente
          ,vl_monto_total_vigente_pesos ,vl_monto_capital_futuro
          ,vl_monto_interes_futuro		,vl_monto_total_futuro_pesos
          ,vl_monto_capital_no_exigible ,vl_monto_interes_no_exgigible
          ,vl_monto_total_no_exgigible_p);
          
      ELSE
        UPDATE TEMP_RECUP_TIPO_DEUDA
        SET MONTO_MORA = MONTO_MORA + vl_monto_mora
            ,MONTO_MULTA = MONTO_MULTA + vl_monto_multa
            ,MONTO_MULTA_TOTAL_PESOS = MONTO_MULTA_TOTAL_PESOS + vl_monto_mora_total_pesos
            ,MONTO_CAPITAL_VIGENTE = MONTO_CAPITAL_VIGENTE + vl_monto_capital_vigente
            ,MONTO_INTERES_VIGENTE = MONTO_INTERES_VIGENTE + vl_monto_interes_vigente
            ,MONTO_CI_TOTAL_VIGENTE_PESOS = MONTO_CI_TOTAL_VIGENTE_PESOS + vl_monto_total_vigente_pesos
            ,MONTO_CAPITAL_FUTURO = MONTO_CAPITAL_FUTURO + vl_monto_capital_futuro
            ,MONTO_INTERES_FUTURO = MONTO_INTERES_FUTURO + vl_monto_interes_futuro
            ,MONTO_CI_TOTAL_FUTURO_PESOS = MONTO_CI_TOTAL_FUTURO_PESOS + vl_monto_total_futuro_pesos
	        ,MONTO_CAPITAL_NO_EXIGIBLE = MONTO_CAPITAL_NO_EXIGIBLE + vl_monto_capital_no_exigible
            ,MONTO_INTERES_NO_EXGIGIBLE = MONTO_INTERES_NO_EXGIGIBLE + vl_monto_interes_no_exgigible
            ,MONTO_CI_TOTAL_NO_EXGIGIBLE_P = MONTO_CI_TOTAL_NO_EXGIGIBLE_P + vl_monto_total_no_exgigible_p
        WHERE cod_tipo_deuda = a.cod_tipo_deuda;
      END IF;
    END LOOP;

    UPDATE TEMP_RECUP_TIPO_DEUDA
    SET MONTO_TOTAL = MONTO_MORA + MONTO_MULTA + MONTO_CAPITAL_VIGENTE + MONTO_INTERES_VIGENTE +
                      MONTO_CAPITAL_FUTURO + MONTO_INTERES_FUTURO + MONTO_CAPITAL_NO_EXIGIBLE + MONTO_INTERES_NO_EXGIGIBLE
		,MONTO_TOTAL_PESOS = MONTO_MULTA_TOTAL_PESOS + MONTO_CI_TOTAL_VIGENTE_PESOS + 
		 					 MONTO_CI_TOTAL_FUTURO_PESOS + MONTO_CI_TOTAL_NO_EXGIGIBLE_P;
  END;
END spi_recup_tipo_deuda;
/