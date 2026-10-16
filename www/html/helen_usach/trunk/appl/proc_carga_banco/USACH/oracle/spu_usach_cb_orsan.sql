PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_orsan
CREATE OR REPLACE PROCEDURE spu_usach_cb_orsan(ve_operacion                   IN VARCHAR2
                                              ,ve_rut                         IN NUMBER   :=NULL
                                              ,ve_dig_verif                   IN VARCHAR2 :=NULL
                                              ,ve_nom_deudor                  IN VARCHAR2 :=NULL
                                              ,ve_rut_universidad             IN VARCHAR2 :=NULL
                                              ,ve_nom_universidad             IN VARCHAR2 :=NULL
                                              ,ve_monto_utm                   IN NUMBER   :=NULL
                                              ,ve_ano_cuota                   IN NUMBER   :=NULL
                                              ,ve_nro_comprobante             IN NUMBER   :=NULL
                                              ,ve_nro_cuota                   IN NUMBER   :=NULL
                                              ,ve_fecha_pago                  IN DATE     :=NULL
                                              ,ve_porc_usach                  IN NUMBER   :=NULL
                                              ,ve_monto_k_utm                 IN NUMBER   :=NULL
                                              ,ve_monto_k_pesos               IN NUMBER   :=NULL
                                              ,ve_monto_i_utm                 IN NUMBER   :=NULL
                                              ,ve_monto_i_pesos               IN NUMBER   :=NULL
                                              ,ve_descto_utm                  IN NUMBER   :=NULL
                                              ,ve_descto_pesos                IN NUMBER   :=NULL
                                              ,ve_monto_total_pesos           IN NUMBER   :=NULL
                                              ,ve_monto_total_utm             IN NUMBER   :=NULL
                                              ,ve_cod_deuda                   IN NUMBER   :=NULL
                                              ,ve_cod_tipo_deuda              IN VARCHAR2 :=NULL
                                              ,ve_fecha_vencto                IN DATE     :=NULL
                                              ,ve_tipo_cobro                  IN VARCHAR2 :=NULL
                                              ,ve_fecha_deposito              IN DATE     :=NULL
                                              ,ve_cod_origen_carga_banco      IN NUMBER   :=NULL)

AS
BEGIN
  IF (ve_operacion = 'INSERT')THEN 
    INSERT INTO usach_cb_orsan
			        (cod_usach_cb_orsan
              ,rut
              ,dig_verif
              ,nom_deudor
              ,rut_universidad
              ,nom_universidad
              ,valor_utm
              ,ano_cuota
              ,nro_comprobante
              ,nro_cuota
              ,fecha_pago
              ,porc_usach
              ,monto_capital_utm
              ,monto_capital_pesos
              ,monto_interes_utm
              ,monto_interes_pesos
              ,monto_descto_utm
              ,monto_descto_pesos
              ,monto_total_pesos
              ,monto_total_utm
              ,cod_deuda
              ,cod_tipo_deuda
              ,fecha_vencto
              ,tipo_cobro
              ,fecha_deposito
              ,cod_origen_carga_banco
              ,procesado
              ,cargado
              )
	  VALUES (usach_cb_orsan_sq.NEXTVAL
              ,ve_rut
              ,ve_dig_verif
              ,ve_nom_deudor
              ,ve_rut_universidad
              ,ve_nom_universidad
              ,ve_monto_utm
              ,ve_ano_cuota
              ,ve_nro_comprobante
              ,ve_nro_cuota
              ,ve_fecha_pago
              ,ve_porc_usach
              ,ve_monto_k_utm
              ,ve_monto_k_pesos
              ,ve_monto_i_utm
              ,ve_monto_i_pesos
              ,ve_descto_utm
              ,ve_descto_pesos
              ,ve_monto_total_pesos
              ,ve_monto_total_utm
              ,ve_cod_deuda
              ,ve_cod_tipo_deuda
              ,ve_fecha_vencto
              ,ve_tipo_cobro
              ,ve_fecha_deposito
              ,ve_cod_origen_carga_banco
              ,'N'
              ,'N'
              );
  ELSIF (ve_operacion = 'DELETE')THEN 
    DELETE usach_cb_orsan
    WHERE cargado = 'N'
    AND   procesado = 'N';
  END IF;
END spu_usach_cb_orsan;
/