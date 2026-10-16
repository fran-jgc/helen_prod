PROMPT CREATE OR REPLACE PROCEDURE sp_cruzado_repro
CREATE OR REPLACE PROCEDURE sp_cruzado_repro(ve_cod_tipo_deuda IN VARCHAR2,ve_cod_reprogramacion IN NUMBER)
AS
BEGIN
  	DECLARE vl_count        NUMBER;
    vl_sum_monto_pagare     NUMBER;
    vl_cod_deuda_crpi       NUMBER;
    vl_cod_deuda_cr12       NUMBER;
    vl_porc_pagare          NUMBER;
    vl_cod_cartera          NUMBER;

    CURSOR C_PAGARE IS
    SELECT 	INTERESES * (100 - PORC_CONDONACION_INTERES_PENAL)/100 + SALDO_CAPITAL MONTO_PAGARE
          ,COD_UNIVERSIDAD
    FROM	PAGARE_REPROGRAMACION
    WHERE	COD_REPROGRAMACION = ve_cod_reprogramacion;


BEGIN                                               

  SELECT Count(*)
  INTO vl_count
  FROM 	PAGARE_REPROGRAMACION
  WHERE 	COD_REPROGRAMACION = ve_cod_reprogramacion;


  --la deuda tiene mas de un pagare -> es cruzado
  IF (vl_count > 1) THEN
    SELECT cod_deuda_crpi
        ,cod_deuda_cr12
    INTO vl_cod_deuda_crpi
        ,vl_cod_deuda_cr12
    FROM 	REPROGRAMACION
    WHERE 	COD_REPROGRAMACION = ve_cod_reprogramacion;

    SELECT Sum(INTERESES * (100 - PORC_CONDONACION_INTERES_PENAL)/100 + SALDO_CAPITAL)
    INTO vl_sum_monto_pagare
    FROM 	PAGARE_REPROGRAMACION
    WHERE 	COD_REPROGRAMACION = ve_cod_reprogramacion;

    
    IF(ve_cod_tipo_deuda = 'CRPI') THEN

      FOR a IN C_PAGARE LOOP

          vl_porc_pagare := Round(a.MONTO_PAGARE/vl_sum_monto_pagare*100, 2);
          vl_cod_cartera := a.COD_UNIVERSIDAD;

          INSERT INTO cruzado (cod_cruzado
                                       ,cod_deuda
                                       ,cod_universidad
                                       ,monto_actualizado
                                       ,porc
                                       ,fecha_consolidacion) 
          VALUES (cruzado_sq.NEXTVAL
                  ,vl_cod_deuda_crpi
                  ,vl_cod_cartera
                  ,a.MONTO_PAGARE
                  ,vl_porc_pagare
                  ,SYSDATE                             
                  );
      END LOOP;
    ELSIF(ve_cod_tipo_deuda = 'CR12') THEN
      FOR a IN C_PAGARE LOOP

          vl_porc_pagare := Round(a.MONTO_PAGARE/vl_sum_monto_pagare*100, 2);
          vl_cod_cartera := a.COD_UNIVERSIDAD;

          INSERT INTO cruzado (cod_cruzado
                                       ,cod_deuda
                                       ,cod_universidad
                                       ,monto_actualizado
                                       ,porc
                                       ,fecha_consolidacion)
          VALUES (cruzado_sq.NEXTVAL
                  ,vl_cod_deuda_cr12
                  ,vl_cod_cartera
                  ,a.MONTO_PAGARE
                  ,vl_porc_pagare
                  ,SYSDATE                             
                  );
      END LOOP;
    END IF;
  END IF; --IF (vl_count > 1) 
    
	END;
END;
/