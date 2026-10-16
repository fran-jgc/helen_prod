PROMPT CREATE OR REPLACE PROCEDURE spu_deuda
CREATE OR REPLACE PROCEDURE spu_deuda (
   ve_operacion               IN       VARCHAR
 , ve_cod_deuda               IN       NUMBER
 , ve_cod_cartera             IN       NUMBER:= NULL
 , ve_cod_universidad         IN       NUMBER:= NULL
 , ve_cod_deudor              IN       NUMBER:= NULL
 , ve_cod_tipo_deuda          IN       VARCHAR := NULL
 , ve_fecha_inicio_cobro      IN       DATE := SYSDATE
 , ve_cod_usuario             IN       NUMBER:=null
 , ve_devoluion_a  			  IN	   VARCHAR := NULL
 , ve_rut_devolucion		  IN       VARCHAR := NULL
 , ve_motivo_devolucion       IN       VARCHAR := NULL
)
AS
BEGIN
   DECLARE
      kl_estado_deuda_no_exigible             NUMBER:= 1;
      KL_MODIFICA_CARTERA                     NUMBER:= 3;
      KL_MODIFICA_UNIVERSIDAD                 NUMBER:= 4;
      KL_ESTADO_PP_MOROSA                     NUMBER:= 4;

      vl_cod_cartera_old                      NUMBER;
      vl_cod_universidad_old                  NUMBER;
      vl_cod_deudor                           NUMBER;
   BEGIN
      IF (ve_operacion = 'INSERT')
      THEN
         INSERT INTO deuda
                     (cod_deuda
                    , cod_deudor
                    , cod_tipo_deuda
                    , cod_cartera
                    , cod_universidad
                    , cod_estado_deuda
                    , cant_cuota
                    , fecha_inicio_cobro
                    , monto_inicio_cobro
                     )
              VALUES (deuda_sq.NEXTVAL
                    , ve_cod_deudor
                    , ve_cod_tipo_deuda
                    , ve_cod_cartera
                    , ve_cod_universidad
                    , kl_estado_deuda_no_exigible
                    , (SELECT cant_cuota
                         FROM tipo_deuda
                        WHERE cod_tipo_deuda = ve_cod_tipo_deuda
                          AND ROWNUM = 1)
                    , ve_fecha_inicio_cobro
                    , NULL
                     );
      ELSIF (ve_operacion = 'UPDATE')
      THEN
        SELECT cod_deudor, cod_cartera, cod_universidad
        INTO vl_cod_deudor, vl_cod_cartera_old, vl_cod_universidad_old
        FROM deuda
        WHERE cod_deuda = ve_cod_deuda;

         UPDATE deuda
            SET cod_cartera = ve_cod_cartera
                ,cod_universidad = ve_cod_universidad
                ,fecha_inicio_cobro = ve_fecha_inicio_cobro
          WHERE cod_deuda = ve_cod_deuda;

          IF (vl_cod_cartera_old <> ve_cod_cartera) THEN
            spu_bitacora_deudor ('Cambio cartera, antigua: ' || To_Char(vl_cod_cartera_old) || ' nueva: ' || to_char(ve_cod_cartera), KL_MODIFICA_CARTERA, vl_cod_deudor, ve_cod_usuario);
          END IF;
          IF (vl_cod_universidad_old <> ve_cod_universidad) THEN
            spu_bitacora_deudor ('Cambio universidad, antigua: ' || To_Char(vl_cod_universidad_old) || ' nueva: ' || to_char(ve_cod_universidad), KL_MODIFICA_UNIVERSIDAD, vl_cod_deudor, ve_cod_usuario);
          END IF;

      ELSIF (ve_operacion = 'DELETE')
      THEN
         DELETE      deuda
               WHERE cod_deuda = ve_cod_deuda;
      ELSIF (ve_operacion = 'DEVOLUCION')
      THEN
	      UPDATE pagare
		    SET cod_estado_pagare       = To_Number(f_get_constante('K_EST_PAGARE_DEV'))
            ,cod_usuario_devolucion = ve_cod_usuario
		        ,fecha_devolucion 		  = SYSDATE
		        ,devolucion_a			  = ve_devoluion_a
				,motivo_devolucion		  = ve_motivo_devolucion
				,rut_devolucion			  = ve_rut_devolucion
	      WHERE cod_deuda 			      = ve_cod_deuda;

      ELSIF(ve_operacion = 'EMPRESA_COBRANZA')THEN
		    -- Se usa el parametro cod_cartera don viene un 0 o > 0  para si pertenece a alguna empresa cobranza
		    -- Si es 999 = COD_EMPRESA_COBRANZA = NULL
		    -- Si es 1 = COD_EMPRESA_COBRANZA = 1
		    -- Si es 2 = COD_EMPRESA_COBRANZA = 2 y asi sucesivamente si es que tiene mas empresas de cobranza
		
		    IF (ve_cod_cartera != 999)THEN
			    update  PLAN_PAGO
			    set		  COD_EMPRESA_COBRANZA = ve_cod_cartera
			    where	  cod_deuda = ve_cod_deuda
			    and		  COD_ESTADO_PLAN_PAGO = KL_ESTADO_PP_MOROSA;
		    else
			    update	PLAN_PAGO
			    set		COD_EMPRESA_COBRANZA = NULL
			    where	cod_deuda = ve_cod_deuda
			    and		COD_ESTADO_PLAN_PAGO = KL_ESTADO_PP_MOROSA;
        END IF;
	    END IF;
   END;
END;
/

