PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_funcionario
CREATE OR REPLACE PROCEDURE spu_usach_cb_funcionario(ve_operacion   IN VARCHAR2 
                                      ,ve_cod_deuda        IN NUMBER:=NULL
                                      ,ve_monto_pago   		IN NUMBER   :=NULL
                                      ,ve_fecha_deposito    IN DATE     :=NULL
                                      ,ve_folio_carga       IN VARCHAR2 :=NULL
                                      ,ve_fecha_real_pago   IN DATE     :=NULL
                                      )
AS
BEGIN
  DECLARE vl_rut_deudor NUMBER;

  BEGIN
    IF(ve_operacion = 'INSERT')THEN 

      INSERT INTO USACH_CB_FUNCIONARIO
			    (cod_USACH_CB_FUNCIONARIO
            ,cod_deuda
            ,monto_pago
            ,fecha_deposito
            ,folio_carga
            ,procesado
            ,cargado
            ,fecha_real_pago)
	    VALUES	(USACH_CB_FUNCIONARIO_sq.NEXTVAL
            ,ve_cod_deuda
            ,ve_monto_pago
            ,ve_fecha_deposito
            ,ve_folio_carga
            ,'N'
            ,'N'
            ,ve_fecha_real_pago);
    ELSIF(ve_operacion = 'DELETE')THEN 
      DELETE  USACH_CB_FUNCIONARIO
      WHERE   cargado = 'N'
      AND     procesado = 'N';
    END IF;
  END;
END spu_USACH_CB_FUNCIONARIO;
/