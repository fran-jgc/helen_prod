CREATE OR REPLACE FUNCTION f_pagare_actualizado(
  ve_cod_pagare        NUMERIC
  ,ve_fecha           DATE := SYSDATE
)
  RETURN NUMBER
AS
BEGIN
  DECLARE
    vl_cod_deuda              NUMBER;
    vl_tasa                   NUMBER(14,4);
    vl_fecha_act              DATE;
    vl_monto_pagare           NUMBER(14,4);
  BEGIN

    select cod_deuda
    INTO vl_cod_deuda
	  from pagare
	  where cod_pagare = ve_cod_pagare;

    -- obtiene la tasa de interes
    SELECT  1 + (tasa_interes_credito / 100)
    INTO    vl_tasa
    FROM    tipo_deuda td
           ,deuda d
    WHERE   d.cod_deuda = vl_cod_deuda
        AND td.cod_tipo_deuda = d.cod_tipo_deuda;

    -- Se calcula la actualizacion hasta fines año, es decir se tratan de cobrar los intereses de todo el año
    vl_fecha_act := TO_DATE ('31/12/' || To_Char(ve_fecha, 'yyyy'), 'dd/mm/yyyy');

    SELECT  (monto_moneda - Nvl(monto_moneda_rebaja,0)) * Power(vl_tasa, (vl_fecha_act - fecha_pagare) / 365)
    INTO    vl_monto_pagare
    FROM    pagare
    WHERE   cod_pagare = ve_cod_pagare;

    RETURN vl_monto_pagare; 
  END;
END f_pagare_actualizado;
/

