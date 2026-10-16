----------------- f_doc_nro_ingreso ------------------
CREATE OR REPLACE FUNCTION f_doc_nro_ingreso (ve_cod_documento_pago IN   NUMBER)
   RETURN VARCHAR
AS
BEGIN
   DECLARE
      vl_cod_ingreso                NUMBER;
      vl_origen                     VARCHAR(2);
      vl_count                      NUMBER;
   BEGIN
      vl_origen := 'IC';

      SELECT Count(*)
      INTO vl_count
	    from documento_ingreso 
	    where cod_documento_pago = ve_cod_documento_pago;
      IF (vl_count > 0) THEN
        SELECT cod_ingreso
        INTO vl_cod_ingreso
	      from documento_ingreso 
	      where cod_documento_pago = ve_cod_documento_pago;
      ELSE
        SELECT Count(*)
        INTO vl_count
	      from documento_ingreso_ch_fecha 
	      where cod_documento_pago = ve_cod_documento_pago;
        IF (vl_count > 0) THEN
          SELECT cod_ingreso
          INTO vl_cod_ingreso
	        from documento_ingreso_ch_fecha 
	        where cod_documento_pago = ve_cod_documento_pago;
        ELSE
          vl_origen := 'CD';
			    select cod_cambio_documento
          INTO vl_cod_ingreso
			    from documento_nuevo
			    where cod_documento_pago = ve_cod_documento_pago;
        END IF;
      END IF;
	
	    return vl_origen || '-' || To_Char(vl_cod_ingreso);
   END;
END;
/
