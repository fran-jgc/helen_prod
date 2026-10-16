PROMPT CREATE OR REPLACE PROCEDURE spx_undo_carga_pagare
CREATE OR REPLACE PROCEDURE spx_undo_carga_pagare
AS
BEGIN
  DECLARE
    vl_cod_deudor           NUMBER;
    vl_cod_deuda            NUMBER;
    vl_count                NUMBER;

    CURSOR C_CARGA_PAGARE IS
      SELECT  FOLIO_PAGARE
              ,FECHA_PAGARE
              ,MONTO_MONEDA
              ,MONTO_PESOS
      FROM    CARGA_PAGARE
      WHERE   CARGADO = 'S';


  BEGIN
    -- borra los pagares cargados
    FOR a IN C_CARGA_PAGARE LOOP
      SELECT cod_deuda
      INTO vl_cod_deuda
      FROM pagare
      WHERE  FOLIO_PAGARE = a.FOLIO_PAGARE
        AND FECHA_PAGARE = a.FECHA_PAGARE
        AND MONTO_MONEDA = a.MONTO_MONEDA
        AND MONTO_PESOS = a.MONTO_PESOS
        AND ROWNUM = 1;

      SELECT cod_deudor
      INTO vl_cod_deudor
      FROM deuda
      WHERE  cod_deuda = vl_cod_deuda;

      DELETE pagare
      WHERE  FOLIO_PAGARE = a.FOLIO_PAGARE
        AND FECHA_PAGARE = a.FECHA_PAGARE
        AND MONTO_MONEDA = a.MONTO_MONEDA
        AND MONTO_PESOS = a.MONTO_PESOS;

      SELECT Count(*)
      INTO vl_count
      FROM pagare
      WHERE cod_deuda = vl_cod_deuda;

      -- borra las deudas sin pagare
      IF (vl_count=0) then
        DELETE analitico_comprobante
        WHERE cod_deuda = vl_cod_deuda;

        DELETE deuda
        WHERE cod_deuda = vl_cod_deuda;
      END IF;


      SELECT Count(*)
      INTO vl_count
      FROM deuda
      WHERE cod_deudor = vl_cod_deudor;

      -- borra los deudores sin deuda
      IF (vl_count=0) then
        DELETE direccion
        WHERE cod_deudor = vl_cod_deudor;

        DELETE matricula
        WHERE cod_deudor = vl_cod_deudor;

        DELETE bitacora_deudor
        WHERE cod_deudor = vl_cod_deudor;

        DELETE deudor
        WHERE cod_deudor = vl_cod_deudor;
      END IF;

      COMMIT;
    END LOOP;

    DELETE CARGA_PAGARE;
    COMMIT;
  END;
END spx_undo_carga_pagare;
/

