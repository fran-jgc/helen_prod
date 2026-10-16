PROMPT CREATE OR REPLACE TRIGGER tr_deuda
CREATE OR REPLACE TRIGGER tr_deuda
BEFORE INSERT OR UPDATE OF fecha_inicio_cobro ON deuda
FOR EACH ROW
DECLARE
  K_VIGENTE                 NUMBER := 1;
  K_UNIVERSIDAD_DEFECTO     NUMBER := 3;
  K_NO_EXIGIBLE             NUMBER := 1;
                                        
  vl_fecha_act              DATE;
  vl_saldo_deuda            NUMBER;
  vl_tasa                   NUMBER;
  vl_monto_pagare           NUMBER;
  vl_monto_otras_ues        NUMBER;
  vl_monto_abono            NUMBER;
  vl_monto_dscto            NUMBER;
                                   
BEGIN
  -- obtiene la tasa de interes
  SELECT  1 + (tasa_interes_credito / 100)
  INTO    vl_tasa
  FROM    tipo_deuda td
  WHERE   td.cod_tipo_deuda = :new.cod_tipo_deuda;
                       
  -- Se calcula la actualizacion hasta fines año, es decir se tratan de cobrar los intereses de todo el año
  vl_fecha_act := To_Date('31/12/' || To_Char(To_Number(To_Char(:NEW.fecha_inicio_cobro, 'yyyy')) - 1),'dd/mm/yyyy');

  SELECT  Nvl(Sum((monto_moneda - Nvl(monto_moneda_rebaja, 0)) * Power(vl_tasa, (vl_fecha_act - fecha_pagare) / 365)), 0)
  INTO    vl_monto_pagare
  FROM    pagare
  WHERE   cod_deuda = :new.cod_deuda;

  SELECT  Nvl(Sum(monto_moneda * Power(vl_tasa, (vl_fecha_act - fecha_abono) / 365)), 0)
  INTO    vl_monto_abono                                                              
  FROM    abono                                                                       
  WHERE   cod_deuda = :new.cod_deuda                                                  
          AND cod_componente_deuda = K_NO_EXIGIBLE                                    
          AND cod_estado_pago = K_VIGENTE;
                                                                                      
  SELECT  Nvl(Sum(monto_moneda * Power(vl_tasa, (vl_fecha_act - fecha_descuento) / 365)), 0)
  INTO    vl_monto_dscto
  FROM    descuento
  WHERE   cod_deuda = :new.cod_deuda
          AND cod_componente_deuda = K_NO_EXIGIBLE
          AND cod_estado_pago = K_VIGENTE;
                                                  
  SELECT  Nvl(Sum(monto_actualizado * Power(vl_tasa, (vl_fecha_act - fecha_consolidacion) / 365)), 0)
  INTO    vl_monto_otras_ues
  FROM    cruzado
  WHERE   cod_deuda = :new.cod_deuda
          AND cod_universidad <> f_get_parametro(K_UNIVERSIDAD_DEFECTO);

  vl_saldo_deuda := Round(vl_monto_pagare + vl_monto_otras_ues - vl_monto_abono - vl_monto_dscto, 4);


  :new.monto_inicio_cobro := vl_saldo_deuda;

END;

/
