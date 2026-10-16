PROMPT CREATE OR REPLACE PROCEDURE spu_cb_abono_desctos
CREATE OR REPLACE PROCEDURE spu_cb_abono_desctos(ve_operacion				VARCHAR2 := NULL
												  ,ve_tipo_pago				VARCHAR2 := NULL
				                                  ,ve_rut					NUMBER := NULL
				                                  ,ve_ano_cobro				NUMBER := NULL
				                                  ,ve_nro_cuota				NUMBER := NULL
				                                  ,ve_monto_cuota			NUMBER := NULL
				                                  ,ve_fecha_pago			DATE := NULL
				                                  ,ve_capital_utm			NUMBER := NULL
				                                  ,ve_interes_penal			NUMBER := NULL
				                                  ,ve_total_utm				NUMBER := NULL
				                                  ,ve_total_pesos			NUMBER := NULL
				                                  ,ve_porc_cruzado			NUMBER := NULL
				                                  ,ve_capital_utm_rend		NUMBER := NULL
				                                  ,ve_interes_penal_rend	NUMBER := NULL
				                                  ,ve_total_utm_rend		NUMBER := NULL
				                                  ,ve_total_pesos_rend		NUMBER := NULL
				                                  ,ve_cod_tipo_deuda		VARCHAR2 := NULL)
AS
BEGIN
	
    IF (ve_operacion = 'INSERT') THEN
  		INSERT INTO CB_ABONO_DESCTOS
				(COD_CB_ABONO_DESCTOS
				,TIPO_PAGO           
			    ,RUT                   
			    ,ANO_COBRO             
			    ,NRO_CUOTA             
			    ,MONTO_CUOTA           
			    ,FECHA_PAGO            
			    ,CAPITAL_UTM           
			    ,INTERES_PENAL         
			    ,TOTAL_UTM             
			    ,TOTAL_PESOS           
			    ,PORC_CRUZADO          
			    ,CAPITAL_UTM_REND      
			    ,INTERES_PENAL_REND    
			    ,TOTAL_UTM_REND        
			    ,TOTAL_PESOS_REND
			    ,COD_TIPO_DEUDA
			    ,PROCESADO
			    ,CARGADO)
		VALUES	(cb_abono_desctos_sq.NEXTVAL
				  ,ve_tipo_pago			
                  ,ve_rut				
                  ,ve_ano_cobro		
                  ,ve_nro_cuota		
                  ,ve_monto_cuota		
                  ,ve_fecha_pago		
                  ,ve_capital_utm		
                  ,ve_interes_penal	
                  ,ve_total_utm		
                  ,ve_total_pesos		
                  ,ve_porc_cruzado		
                  ,ve_capital_utm_rend	
                  ,ve_interes_penal_rend
                  ,ve_total_utm_rend	
                  ,ve_total_pesos_rend
                  ,ve_cod_tipo_deuda
                  ,'N'
                  ,'N');

  	END IF;

	COMMIT;
END spu_cb_abono_desctos;
/