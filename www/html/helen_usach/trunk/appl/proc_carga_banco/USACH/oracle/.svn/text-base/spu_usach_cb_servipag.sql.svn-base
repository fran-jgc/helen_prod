PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_servipag
CREATE OR REPLACE PROCEDURE spu_usach_cb_servipag
                                      (ve_operacion       IN VARCHAR2 
                                      ,ve_cod_sub_boleta  IN NUMBER:=NULL
                                      ,ve_monto           IN NUMBER:=NULL
                                      ,ve_fecha_pago      IN DATE  :=NULL
                                      ,ve_fecha_deposito  IN DATE  :=NULL
                                      )
AS
BEGIN
  IF(ve_operacion = 'INSERT')THEN 
    INSERT INTO usach_cb_servipag
			    (cod_usach_cb_servipag
          ,cod_sub_boleta
          ,monto
          ,fecha_pago
          ,procesado
          ,cargado
          ,fecha_deposito)
	  VALUES	(usach_cb_servipag_sq.NEXTVAL
          ,ve_cod_sub_boleta
          ,ve_monto
          ,ve_fecha_pago
          ,'N'
          ,'N'
          ,Trunc(ve_fecha_deposito));
  ELSIF(ve_operacion = 'DELETE')THEN
    DELETE  usach_cb_servipag
    WHERE   cargado = 'N'
    AND     procesado = 'N';
  END IF;
END spu_usach_cb_servipag;
/