CREATE OR REPLACE FUNCTION f_deuda_get_estado (
   ve_cod_deuda                        number
 , ve_fecha                            DATE := SYSDATE
)
   RETURN NUMBER
AS
BEGIN
    DECLARE
      K_NO_EXIGIBLE               NUMBER:=1;
      K_EXIGIBLE                  NUMBER:=2;
	    K_PARAM_TIPO_DEUDA_RENEG	  NUMBER:=29;
      vl_cod_estado_deuda         NUMBER;
      vl_cod_cambio_estado_deuda  NUMBER;
      vl_fecha_inicio_cobro       DATE;
   		vl_cod_tipo_deuda				    varchar2(100);
      vl_cod_tipo_deuda_reneg     varchar2(100);


      BEGIN
  	    vl_cod_tipo_deuda_reneg := f_get_parametro(K_PARAM_TIPO_DEUDA_RENEG);

  	    SELECT	cod_estado_deuda
  	    into vl_cod_estado_deuda
		FROM	deuda
		WHERE	cod_deuda = ve_cod_deuda;

		/*
		VM+IS, 30/12/2011
		si el estado de la deuda actual es = 1, no se pregunta por la tabla CAMBIO_ESTADO_DEUDA, 
		hay casos donde retorna la fx = 2 y fallan los saldos
		Sin embargo, se deberan revisar los informes contables
		*/
		IF(vl_cod_estado_deuda = K_NO_EXIGIBLE) THEN
			RETURN vl_cod_estado_deuda;
		END IF;
			
        SELECT MIN(COD_CAMBIO_ESTADO_DEUDA)
        INTO vl_cod_cambio_estado_deuda
        FROM CAMBIO_ESTADO_DEUDA
        WHERE COD_DEUDA = ve_cod_deuda
          AND FECHA_CAMBIO_ESTADO_DEUDA > ve_fecha;

        IF (vl_cod_cambio_estado_deuda IS NULL) THEN
          -- Sio no existe historia retorna el estado que esta en la deuda
          SELECT cod_estado_deuda
                ,fecha_inicio_cobro
                ,cod_tipo_deuda
          INTO vl_cod_estado_deuda
              ,vl_fecha_inicio_cobro
              ,vl_cod_tipo_deuda
          FROM deuda
          WHERE cod_deuda      = ve_cod_deuda;

          -- si la deuda es exigible y no tiene historial el CAMBIO_ESTADO_DEUDA, compra con FECHA_INICIO_COBRO
		      -- la deuda no puede CFR, CSN, CSR, RIN (estas deudas nunca tuvieron SVP)
          IF (vl_cod_estado_deuda = K_EXIGIBLE and vl_cod_tipo_deuda not in ('CFR','CSR','CSN', vl_cod_tipo_deuda_reneg)) THEN
            IF (ve_fecha < vl_fecha_inicio_cobro) THEN
              vl_cod_estado_deuda := K_NO_EXIGIBLE;
            END IF;
          END IF;
        ELSE
          SELECT COD_ESTADO_DEUDA_ANTIGUO
          INTO vl_cod_estado_deuda
          FROM CAMBIO_ESTADO_DEUDA
          WHERE COD_CAMBIO_ESTADO_DEUDA = vl_cod_cambio_estado_deuda;
        END IF;

        RETURN vl_cod_estado_deuda;
      END;
END;
/

