CREATE OR REPLACE PROCEDURE spw_webpay_activa_cuenta(ve_cod_deudor in NUMBER)									
  AS
  BEGIN
	        declare vl_cod_log_cambio  NUMBER;
      BEGIN
          vl_cod_log_cambio:=0;

	        UPDATE deudor
	        SET CUENTA_ACTIVA = 'S'
	        WHERE COD_DEUDOR = ve_cod_deudor;	

           
          sp_log_cambio('DEUDOR'
                        ,ve_cod_deudor
                        ,2     --COD_USUARIO, se asigna USUARIO WEB.
                        ,'U');

    
        SELECT log_cambio_sq.CURRVAL
	      INTO   vl_cod_log_cambio
	      FROM dual;

          sp_detalle_cambio(vl_cod_log_cambio,'ACTIVA_CUENTA', 'N', 'S');
  END;
END;