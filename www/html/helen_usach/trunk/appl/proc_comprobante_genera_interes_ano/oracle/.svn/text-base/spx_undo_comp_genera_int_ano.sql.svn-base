PROMPT CREATE OR REPLACE PROCEDURE spx_undo_comp_genera_int_ano
CREATE OR REPLACE PROCEDURE spx_undo_comp_genera_int_ano (ve_cod_ejecucion_proceso     NUMBER)
AS
BEGIN
  DECLARE

  CURSOR c_plan_pago_h IS

      SELECT  cod_plan_pago
              ,monto_capital
              ,monto_interes
      FROM    plan_pago_h
      WHERE   cod_origen = ve_cod_ejecucion_proceso
            AND origen = 'spp_cgi_comp_genera_int_ano';
                                                        
  BEGIN
    FOR a IN c_plan_pago_h LOOP
   
      UPDATE plan_pago
      SET monto_capital = a.monto_capital
          ,monto_interes = a.monto_interes
      WHERE cod_plan_pago = a.cod_plan_pago;

      COMMIT;                                      
  END LOOP;
  
  END;    
END spx_undo_comp_genera_int_ano;
/
