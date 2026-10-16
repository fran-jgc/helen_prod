PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_tgr
CREATE OR REPLACE PROCEDURE spu_usach_cb_tgr(ve_operacion   IN VARCHAR2 
                                      ,ve_rut_deudor        IN VARCHAR2 :=NULL
                                      ,ve_monto_retencion   IN NUMBER   :=NULL
                                      ,ve_fecha_deposito    IN DATE     :=NULL
                                      ,ve_folio_carga       IN VARCHAR2 :=NULL
                                      ,ve_fecha_real_pago   IN DATE     :=NULL
                                      )
AS
BEGIN
  DECLARE vl_rut_deudor NUMBER;

  BEGIN
    IF(ve_operacion = 'INSERT')THEN 
      vl_rut_deudor := substr(ve_rut_deudor,1,length (ve_rut_deudor)-2);

      INSERT INTO USACH_CB_TGR
			    (cod_usach_cb_tgr
            ,rut_deudor
            ,monto_retencion
            ,fecha_deposito
            ,folio_carga
            ,procesado
            ,cargado
            ,fecha_real_pago)
	    VALUES	(usach_cb_tgr_sq.NEXTVAL
            ,vl_rut_deudor
            ,ve_monto_retencion
            ,ve_fecha_deposito
            ,ve_folio_carga
            ,'N'
            ,'N'
            ,ve_fecha_real_pago);
    ELSIF(ve_operacion = 'DELETE')THEN 
      DELETE  usach_cb_tgr
      WHERE   cargado = 'N'
      AND     procesado = 'N';
    END IF;
  END;
END spu_usach_cb_tgr;
/