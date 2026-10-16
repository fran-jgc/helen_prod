CREATE OR REPLACE PROCEDURE sp_deuda_update_estado(ve_cod_deuda             NUMBER
                                                   ,ve_cod_estado_deuda     NUMBER)
AS
/******************************************************************************
   NAME:       sp_deuda_update_estado
   Cambia el estado de la deuda y deja un historico del estado anterior

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        02/12/2009   Victor Melo

   NOTAS:

******************************************************************************/

BEGIN
      DECLARE
        vl_cod_estado_deuda number;
        KL_ESTADO_PAGARE		NUMBER;
			  KL_CONDONADA			  NUMBER:=3;
			  KL_CANCELADA				NUMBER:=4;

      BEGIN

        KL_ESTADO_PAGARE := f_get_constante('K_EST_PAGARE_PROC_DEV');

        SELECT cod_estado_deuda
        INTO vl_cod_estado_deuda
        FROM deuda
        WHERE cod_deuda = ve_cod_deuda;

        IF (vl_cod_estado_deuda <> ve_cod_estado_deuda) THEN
          INSERT INTO CAMBIO_ESTADO_DEUDA (COD_CAMBIO_ESTADO_DEUDA, FECHA_CAMBIO_ESTADO_DEUDA, COD_DEUDA, COD_ESTADO_DEUDA_ANTIGUO, COD_ESTADO_DEUDA_NUEVO)
          VALUES (cambio_estado_deuda_sq.NEXTVAL, SYSDATE, ve_cod_deuda, vl_cod_estado_deuda, ve_cod_estado_deuda);

          UPDATE deuda
          SET cod_estado_deuda = ve_cod_estado_deuda
          WHERE cod_deuda = ve_cod_deuda;

          if((ve_cod_estado_deuda=KL_CONDONADA)or(ve_cod_estado_deuda=KL_CANCELADA)) THEN
				    update pagare
				    set cod_estado_pagare = KL_ESTADO_PAGARE
				    where cod_deuda = ve_cod_deuda;
			    END IF;


        END IF;
      END;

END;
/



