PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_transbank
CREATE OR REPLACE PROCEDURE spu_usach_cb_transbank(ve_operacion           IN VARCHAR2 
                                                    ,ve_fecha_deposito    IN DATE   :=NULL
                                                    ,ve_cod_sub_boleta    IN NUMBER :=NULL
                                                    ,ve_monto             IN NUMBER :=NULL
                                                    ,ve_fecha_abono       IN DATE   :=NULL)
AS
BEGIN
  IF(ve_operacion = 'INSERT')THEN 
    INSERT INTO usach_cb_transbank
			  (cod_usach_cb_transbank
          ,fecha_deposito
          ,cod_sub_boleta
          ,monto
          ,fecha_abono
          ,procesado
          ,cargado)
	  VALUES	(usach_cb_transbank_sq.NEXTVAL
          ,ve_fecha_deposito
          ,ve_cod_sub_boleta
          ,ve_monto
          ,ve_fecha_abono
          ,'N'
          ,'N');
  ELSIF(ve_operacion = 'DELETE')THEN
    DELETE  usach_cb_transbank
    WHERE   cargado = 'N'
    AND     procesado = 'N'; 
  END IF;
END spu_usach_cb_transbank;
/