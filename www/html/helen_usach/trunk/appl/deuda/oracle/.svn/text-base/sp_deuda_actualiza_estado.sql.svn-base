CREATE OR REPLACE PROCEDURE sp_deuda_actualiza_estado(ve_cod_deuda IN number)
AS
/******************************************************************************
   NAME:       sp_deuda_actualiza_estado
   Debe actualizar el estado de deuda cuando han cambiado los abonos o dsctos
******************************************************************************/

BEGIN
  DECLARE
    KL_NO_EXIGIBLE        number:=1;
    KL_EXIGIBLE           NUMBER:=2;
    KL_CONDONADA          NUMBER:=3;
    KL_PAGADA             NUMBER:=4;
    KL_REPROGRAMADA       NUMBER:=5;
    KL_CONDONADA_CS       NUMBER:=6;
    KL_NOVADA             NUMBER:=7;

    vl_cod_estado_actual	      NUMBER;
    vl_cant_pp                  NUMBER;
    vl_cod_cambio_estado_deuda  NUMBER;
    vl_saldo_deuda              NUMBER;

  BEGIN
    IF (ve_cod_deuda IS NULL) THEN
      RETURN;
    END IF;

    SELECT cod_estado_deuda
		INTO vl_cod_estado_actual
		FROM deuda
		WHERE cod_deuda = ve_cod_deuda;

    IF (vl_cod_estado_actual IN (KL_CONDONADA, KL_REPROGRAMADA, KL_CONDONADA_CS, KL_NOVADA)) THEN
      RETURN;       -- nada
    END IF;

    SELECT Count(*)
		INTO vl_cant_pp
		FROM plan_pago
		WHERE cod_deuda = ve_cod_deuda;

    IF (vl_cod_estado_actual = KL_PAGADA) THEN
      IF (vl_cant_pp > 0) THEN
        sp_deuda_update_estado(ve_cod_deuda, KL_EXIGIBLE);
      ELSE
        sp_deuda_update_estado(ve_cod_deuda, KL_NO_EXIGIBLE);
      END IF;

      SELECT cambio_estado_deuda_sq.CURRVAL INTO vl_cod_cambio_estado_deuda FROM dual;

      vl_saldo_deuda := f_deuda_saldo_total(ve_cod_deuda);
      IF (vl_saldo_deuda <= 0) THEN
        -- hace undo de sp_deuda_update_estado() hecho anteriormente
        DELETE cambio_estado_deuda
        WHERE cod_cambio_estado_deuda = vl_cod_cambio_estado_deuda;

        UPDATE deuda
        SET cod_estado_deuda = KL_PAGADA
        WHERE cod_deuda = ve_cod_deuda;
      END IF;
    ELSE -- no esta pagada
      vl_saldo_deuda := f_deuda_saldo_total(ve_cod_deuda);
      IF (vl_saldo_deuda <= 0) THEN
        sp_deuda_update_estado(ve_cod_deuda, KL_PAGADA);
      END IF;
    END IF;
  END;
END;
/

