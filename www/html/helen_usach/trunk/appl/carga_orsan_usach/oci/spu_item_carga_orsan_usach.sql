CREATE OR REPLACE PROCEDURE spu_item_carga_orsan_usach	(ve_operacion 				   IN  VARCHAR
														,ve_cod_item_carga_orsan_usach IN  NUMBER  := NULL 
													    ,ve_cod_carga_orsan_usach      IN  NUMBER  := NULL 
													    ,ve_cod_estado_carga           IN  NUMBER  := NULL
													    ,ve_rut                        IN  NUMBER  := NULL
													    ,ve_dig_verif                  IN  VARCHAR := NULL
													    ,ve_nombre                     IN  VARCHAR := NULL 
													    ,ve_rut_universidad            IN  VARCHAR := NULL
													    ,ve_nombre_universidad         IN  VARCHAR := NULL
													    ,ve_valor_utm                  IN  NUMBER  := NULL
													    ,ve_ano_cuota                  IN  NUMBER  := NULL
													    ,ve_nro_comprobante            IN  NUMBER  := NULL 
													    ,ve_nro_cuota                  IN  NUMBER  := NULL
													    ,ve_fecha_pago                 IN  DATE	   := NULL
													    ,ve_porc_usach                 IN  NUMBER  := NULL
													    ,ve_monto_capital_utm          IN  NUMBER  := NULL 
													    ,ve_monto_capital_pesos        IN  NUMBER  := NULL
													    ,ve_monto_interes_utm          IN  NUMBER  := NULL
													    ,ve_monto_interes_pesos        IN  NUMBER  := NULL
													    ,ve_monto_descto_utm           IN  NUMBER  := NULL
													    ,ve_monto_descto_pesos         IN  NUMBER  := NULL 
													    ,ve_monto_total_pesos          IN  NUMBER  := NULL 
													    ,ve_monto_total_utm            IN  NUMBER  := NULL 
													    ,ve_cod_deuda                  IN  NUMBER  := NULL
													    ,ve_cod_tipo_deuda             IN  VARCHAR := NULL
													    ,ve_fecha_vencto               IN  DATE	   := NULL 
													    ,ve_tipo_cobro                 IN  VARCHAR := NULL
													    ,ve_cod_origen_pago			   IN  NUMBER  := NULL
													    ,ve_fecha_deposito             IN  DATE	   := NULL
													    ,ve_cod_usuario      		   IN  NUMBER  := NULL
														,ve_folio					   IN  VARCHAR := NULL)	

AS
BEGIN
  DECLARE
    K_ESTADO_SOLICITADA           	NUMBER:= 1;
    vl_cod_item_carga_orsan_usach	NUMBER;
    vl_rut_ant						NUMBER;
	vl_ano_cuota_ant				NUMBER;
	vl_cod_deuda_ant				NUMBER;
	vl_nro_cuota_ant				NUMBER;
	vl_fecha_pago_ant				DATE;
	vl_cod_origen_pago_ant			NUMBER;
	vl_fecha_deposito_ant			DATE;
	vl_rut_pos						NUMBER;
	vl_ano_cuota_pos				NUMBER;
	vl_cod_deuda_pos				NUMBER;
	vl_nro_cuota_pos				NUMBER;
	vl_fecha_pago_pos				DATE;
	vl_cod_origen_pago_pos			NUMBER;
	vl_fecha_deposito_pos			DATE;
	vl_obs							varchar(200):='';
    
			
	BEGIN
		if(ve_operacion = 'INSERT') then 
			
			INSERT INTO ITEM_CARGA_ORSAN_USACH
					   ( COD_ITEM_CARGA_ORSAN_USACH  
					    ,COD_CARGA_ORSAN_USACH       
					    ,COD_ESTADO_CARGA           
					    ,RUT                        
					    ,DIG_VERIF                  
					    ,NOMBRE                      
					    ,RUT_UNIVERSIDAD            
					    ,NOMBRE_UNIVERSIDAD         
					    ,VALOR_UTM                  
					    ,ANO_CUOTA                 
					    ,NRO_COMPROBANTE             
					    ,NRO_CUOTA                   
					    ,FECHA_PAGO                 
					    ,PORC_USACH                 
					    ,MONTO_CAPITAL_UTM           
					    ,MONTO_CAPITAL_PESOS        
					    ,MONTO_INTERES_UTM          
					    ,MONTO_INTERES_PESOS        
					    ,MONTO_DESCTO_UTM            
					    ,MONTO_DESCTO_PESOS          
					    ,MONTO_TOTAL_PESOS           
					    ,MONTO_TOTAL_UTM             
					    ,COD_DEUDA                  
					    ,COD_TIPO_DEUDA             
					    ,FECHA_VENCTO                
					    ,TIPO_COBRO                
					    ,COD_ORIGEN_PAGO
					    ,FECHA_DEPOSITO
						,FOLIO)
				 VALUES(item_carga_orsan_usach_sq.NEXTVAL
					    ,ve_cod_carga_orsan_usach     
					    ,ve_cod_estado_carga           
					    ,ve_rut                        
					    ,ve_dig_verif                  
					    ,ve_nombre                    
					    ,ve_rut_universidad            
					    ,ve_nombre_universidad         
					    ,ve_valor_utm                  
					    ,ve_ano_cuota                 
					    ,ve_nro_comprobante            
					    ,ve_nro_cuota                 
					    ,ve_fecha_pago                
					    ,ve_porc_usach                
					    ,ve_monto_capital_utm          
					    ,ve_monto_capital_pesos        
					    ,ve_monto_interes_utm          
					    ,ve_monto_interes_pesos       
					    ,ve_monto_descto_utm          
					    ,ve_monto_descto_pesos        
					    ,ve_monto_total_pesos         
					    ,ve_monto_total_utm            
					    ,ve_cod_deuda                  
					    ,ve_cod_tipo_deuda             
					    ,ve_fecha_vencto               
					    ,ve_tipo_cobro                 
					    ,ve_cod_origen_pago
					    ,ve_fecha_deposito
						,ve_folio);
			
			 SELECT  item_carga_orsan_usach_sq.CURRVAL
			 INTO    vl_cod_item_carga_orsan_usach
			 FROM    dual;   
					 
		    insert into BITACORA_CARGA_ORSAN 
		    			(COD_BITACORA_CARGA_ORSAN 
  						,FECHA_REGISTRO
						,COD_CARGA_ORSAN_USACH       
						,COD_ITEM_CARGA_ORSAN_USACH  
					    ,COD_USUARIO                 
						,OBSERVACION)
			      values(bitacora_carga_orsan_sq.NEXTVAL
						,sysdate
					    ,ve_cod_carga_orsan_usach
						,vl_cod_item_carga_orsan_usach
						,ve_cod_usuario
						,'Inserta Item de forma exitosa'); 
   
		end if;	
		if(ve_operacion = 'UPDATE') then 
		
			
			select  RUT			 	
				   ,ANO_CUOTA	 	
				   ,COD_DEUDA
				   ,NRO_CUOTA	 	
				   ,FECHA_PAGO		
				   ,COD_ORIGEN_PAGO 
				   ,FECHA_DEPOSITO 
			into vl_rut_ant
				,vl_ano_cuota_ant
				,vl_cod_deuda_ant
				,vl_nro_cuota_ant
				,vl_fecha_pago_ant
				,vl_cod_origen_pago_ant
				,vl_fecha_deposito_ant
			from item_carga_orsan_usach
			where cod_item_carga_orsan_usach= ve_cod_item_carga_orsan_usach;
		
			update ITEM_CARGA_ORSAN_USACH
			set RUT			 	= ve_rut
			   ,ANO_CUOTA	 	= ve_ano_cuota
			   ,COD_DEUDA	 	= ve_cod_deuda
			   ,NRO_CUOTA	 	= ve_nro_cuota
			   ,FECHA_PAGO		= ve_fecha_pago
			   ,COD_ORIGEN_PAGO = ve_cod_origen_pago
			   ,FECHA_DEPOSITO  = ve_fecha_deposito
			where COD_ITEM_CARGA_ORSAN_USACH= ve_cod_item_carga_orsan_usach;
			
			select  RUT			 	
				   ,ANO_CUOTA	 	
				   ,COD_DEUDA	
				   ,NRO_CUOTA 	
				   ,FECHA_PAGO		
				   ,COD_ORIGEN_PAGO 
				   ,FECHA_DEPOSITO  
			into vl_rut_pos
				,vl_ano_cuota_pos
				,vl_cod_deuda_pos
				,vl_nro_cuota_pos
				,vl_fecha_pago_pos
				,vl_cod_origen_pago_pos
				,vl_fecha_deposito_pos
			from item_carga_orsan_usach
			where cod_item_carga_orsan_usach= ve_cod_item_carga_orsan_usach;
			
			if(vl_rut_ant <> vl_rut_pos)then
				vl_obs := vl_obs ||' '||'Cambia rut:'||''||to_char(vl_rut_ant);
			end if;
			
			if(vl_ano_cuota_ant <> vl_ano_cuota_pos)then
				vl_obs := vl_obs ||' '||'Cambia aÑo:'||''||to_char(vl_ano_cuota_ant);
			end if;
			
			if(vl_cod_deuda_ant <> vl_cod_deuda_pos)then
				vl_obs := vl_obs ||' '||'Cambia Deuda:'||''||to_char(vl_cod_deuda_ant);
			end if;
			
			if(vl_fecha_pago_ant <> vl_fecha_pago_pos)then
				vl_obs := vl_obs ||' '||'Cambia F. Pago:'||''||to_char(vl_fecha_pago_ant, 'dd/mm/yyyy');
			end if;
			
			if(vl_cod_origen_pago_ant <> vl_cod_origen_pago_pos)then
				vl_obs := vl_obs ||' '||'Cambia Origen:'||''||to_char(vl_cod_origen_pago_ant);
			end if;
			
			if(vl_fecha_deposito_ant <> vl_fecha_deposito_pos)then
				vl_obs := vl_obs ||' '||'Cambia F. Deposito:'||''||to_char(vl_fecha_deposito_ant, 'dd/mm/yyyy');
			end if;

			if(vl_nro_cuota_ant <> vl_nro_cuota_pos)then
				vl_obs := vl_obs ||' '||'Cambia nro_cuota:'||''||to_char(vl_nro_cuota_ant);
			end if;
			
			insert into BITACORA_CARGA_ORSAN 
		    			(COD_BITACORA_CARGA_ORSAN 
  						,FECHA_REGISTRO
						,COD_CARGA_ORSAN_USACH       
						,COD_ITEM_CARGA_ORSAN_USACH  
					    ,COD_USUARIO                 
						,OBSERVACION)
			      values(bitacora_carga_orsan_sq.NEXTVAL
						,sysdate
					    ,ve_cod_carga_orsan_usach
						,ve_cod_item_carga_orsan_usach
						,ve_cod_usuario
						,vl_obs); 
			
		end if;	
		
		commit;
		
	END;
END;