PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_santander
CREATE OR REPLACE PROCEDURE spu_usach_cb_santander(ve_operacion             IN VARCHAR2 
                                                    ,ve_fecha_pago          IN DATE     :=NULL
                                                    ,ve_cod_sub_boleta      IN NUMBER   :=NULL
                                                    ,ve_monto               IN NUMBER   :=NULL
                                                    ,ve_indicador_resultado IN VARCHAR2 :=NULL
                                                    ,ve_fecha_deposito      IN DATE     :=NULL)
AS
BEGIN
  IF(ve_operacion = 'INSERT')THEN 
    INSERT INTO usach_cb_santander
			  (cod_usach_cb_santander
          ,fecha_pago
          ,cod_sub_boleta
          ,monto_pago
          ,indicador_resultado
          ,procesado
          ,cargado
          ,fecha_deposito)
	  VALUES	(usach_cb_santander_sq.NEXTVAL
          ,ve_fecha_pago
          ,ve_cod_sub_boleta
          ,ve_monto
          ,ve_indicador_resultado
          ,'N'
          ,'N'
          ,ve_fecha_deposito);
  ELSIF (ve_operacion = 'DELETE')THEN 
    DELETE usach_cb_santander
    WHERE cargado = 'N'
    AND   procesado = 'N';
  END IF;
END spu_usach_cb_santander;
/