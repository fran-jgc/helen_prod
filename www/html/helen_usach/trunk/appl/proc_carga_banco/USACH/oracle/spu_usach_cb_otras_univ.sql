PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_otras_univ
CREATE OR REPLACE PROCEDURE spu_usach_cb_otras_univ(ve_operacion                    IN VARCHAR2 
                                                    ,ve_rut                         IN VARCHAR2 :=null
                                                    ,ve_ano_cobro                   IN NUMBER   :=NULL
                                                    ,ve_nro_cuota                   IN NUMBER   :=NULL
                                                    ,ve_monto_cuota                 IN VARCHAR2 :=NULL
                                                    ,ve_fecha_pago                  IN DATE     :=NULL
                                                    ,ve_monto_k_utm_usach           IN NUMBER   :=NULL
                                                    ,ve_monto_k_pesos_usach         IN NUMBER   :=NULL
                                                    ,ve_monto_i_utm_usach           IN NUMBER   :=NULL
                                                    ,ve_monto_i_pesos_usach         IN NUMBER   :=NULL
                                                    ,ve_descto_univ_utm_usach       IN NUMBER   :=NULL
                                                    ,ve_descto_univ_pesos_usach     IN NUMBER   :=NULL
                                                    ,ve_monto_k_utm_otras_ues       IN NUMBER   :=NULL
                                                    ,ve_monto_k_pesos_otras_ues     IN NUMBER   :=NULL
                                                    ,ve_monto_i_utm_otras_ues       IN NUMBER   :=NULL
                                                    ,ve_monto_i_pesos_otras_ues     IN NUMBER   :=NULL
                                                    ,ve_descto_univ_utm_otras_ues   IN NUMBER   :=NULL
                                                    ,ve_descto_univ_pesos_otras_ues IN NUMBER   :=NULL
                                                    ,ve_fecha_deposito              IN DATE     :=NULL)

AS
BEGIN
  DECLARE vl_rut_deudor NUMBER;

  BEGIN
    vl_rut_deudor := substr(ve_rut,1,length (ve_rut)-1);
    
    IF(ve_operacion = 'INSERT')THEN 
      INSERT INTO usach_cb_otras_univ
			        (cod_usach_cb_otras_univ
              ,rut
              ,ano_cobro
              ,nro_cuota
              ,monto_cuota
              ,fecha_pago
              ,monto_k_utm_usach
              ,monto_k_pesos_usach
              ,monto_i_utm_usach
              ,monto_i_pesos_usach
              ,descto_univ_utm_usach
              ,descto_univ_pesos_usach
              ,monto_k_utm_otras_ues
              ,monto_k_pesos_otras_ues
              ,monto_i_utm_otras_ues
              ,monto_i_pesos_otras_ues
              ,descto_utm_otras_ues
              ,descto_pesos_otras_ues
              ,fecha_deposito
              ,procesado
              ,cargado)
	    VALUES (usach_cb_otras_univ_sq.NEXTVAL
              ,vl_rut_deudor
              ,ve_ano_cobro
              ,ve_nro_cuota
              ,ve_monto_cuota
              ,ve_fecha_pago
              ,ve_monto_k_utm_usach
              ,ve_monto_k_pesos_usach
              ,ve_monto_i_utm_usach
              ,ve_monto_i_pesos_usach
              ,ve_descto_univ_utm_usach
              ,ve_descto_univ_pesos_usach
              ,ve_monto_k_utm_otras_ues
              ,ve_monto_k_pesos_otras_ues
              ,ve_monto_i_utm_otras_ues
              ,ve_monto_i_pesos_otras_ues
              ,ve_descto_univ_utm_otras_ues
              ,ve_descto_univ_pesos_otras_ues
              ,ve_fecha_deposito
              ,'N'
              ,'N');
    ELSIF (ve_operacion = 'DELETE')THEN 
      DELETE usach_cb_otras_univ
      WHERE cargado = 'N'
      AND   procesado = 'N';
    END IF;
  END;
END spu_usach_cb_otras_univ;
/