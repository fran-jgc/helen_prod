PROMPT CREATE OR REPLACE PROCEDURE sp_deuda_cant_cuota
CREATE OR REPLACE PROCEDURE sp_deuda_cant_cuota(ve_cod_deuda number)
AS
/******************************************************************************
   NAME:       sp_deuda_cant_cuota
   Calcula la cantidad de cuotas de acuerdo al tipo deuda

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        17/02/2010   Victor Melo

   NOTAS:

******************************************************************************/
BEGIN
  DECLARE
    K_DEUDA_NO_EXIGIBLE       NUMBER:=1;
	  K_PARAM_TIPO_DEUDA_RENEG	NUMBER:=29;

    vl_cod_tipo_deuda         tipo_deuda.cod_tipo_deuda%TYPE;
    vl_cod_tipo_deuda_reneg   tipo_deuda.cod_tipo_deuda%TYPE;
    vl_cod_estado_deuda       NUMBER;
    vl_saldo_deuda            NUMBER;
    vl_fecha_inicio_cobro     DATE;
    vl_fecha_31_12_ant        DATE;
    vl_cant_cuota             NUMBER;

  BEGIN
  	vl_cod_tipo_deuda_reneg := f_get_parametro(K_PARAM_TIPO_DEUDA_RENEG);

    SELECT d.cod_tipo_deuda
          ,d.cod_estado_deuda
          ,d.fecha_inicio_cobro
          ,t.cant_cuota
    INTO vl_cod_tipo_deuda
        ,vl_cod_estado_deuda
        ,vl_fecha_inicio_cobro
        ,vl_cant_cuota
    FROM deuda d, tipo_deuda t
    WHERE d.cod_deuda = ve_cod_deuda
      AND t.cod_tipo_deuda = d.cod_tipo_deuda;

    IF (vl_cod_estado_deuda <> K_DEUDA_NO_EXIGIBLE) THEN
      RETURN;
    END IF;

  	-- para el RIN no hace nada
    IF (vl_cod_estado_deuda = vl_cod_tipo_deuda_reneg) THEN
      RETURN;
    END IF;

    IF (vl_cod_tipo_deuda = 'CS' OR vl_cod_tipo_deuda = 'CU' OR vl_cod_tipo_deuda = 'CFU' ) THEN
      vl_fecha_31_12_ant := To_Date('31/12/' || To_Char(To_Number(To_Char(vl_fecha_inicio_cobro, 'yyyy')) - 1), 'dd/mm/yyyy') + 1 - 1/86400 ;
      vl_saldo_deuda := f_deuda_saldo_total(ve_cod_deuda, vl_fecha_31_12_ant);
      IF (vl_cod_tipo_deuda = 'CS') THEN
        IF (vl_saldo_deuda > 200) THEN
          vl_cant_cuota := 15;
        ELSE
          vl_cant_cuota := 12;
        END IF;
      ELSIF (vl_cod_tipo_deuda = 'CU' OR vl_cod_tipo_deuda = 'CFU') THEN
		vl_cant_cuota := 10;
	  END IF;
	  UPDATE deuda
	  SET cant_cuota = vl_cant_cuota
      WHERE cod_deuda = ve_cod_deuda;
    END IF;
  END;
END;
/

