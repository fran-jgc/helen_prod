CREATE OR REPLACE PROCEDURE spu_item_carga_otra_ues	(ve_operacion 				       IN  VARCHAR
														,ve_cod_item_carga_otra_ues    IN  NUMBER  := NULL 
													    ,ve_cod_carga_otra_ues         IN  NUMBER  := NULL 
													    ,ve_cod_estado_carga           IN  NUMBER  := NULL
													    ,ve_rut                        IN  NUMBER  := NULL
													    ,ve_dig_verif                  IN  VARCHAR := NULL
													    ,ve_folio                      IN  VARCHAR := NULL 
                                                        ,ve_cod_deuda                  IN  NUMBER  := NULL
                                                        ,ve_ano_cuota                  IN  NUMBER  := NULL
													    ,ve_monto_utm                  IN  NUMBER  := NULL 
													    ,ve_monto_pesos                IN  NUMBER  := NULL
													    ,ve_fecha_pago                 IN  DATE	   := NULL 
													    ,ve_cod_origen_pago			   IN  NUMBER  := NULL
													    ,ve_fecha_deposito             IN  DATE	   := NULL
													    ,ve_cod_usuario      		   IN  NUMBER  := NULL
													    ,ve_nro_cuota	      		   IN  NUMBER  := NULL
														,ve_monto_descto_utm           IN  NUMBER  := NULL
														,ve_monto_descto_pesos         IN  NUMBER  := NULL)	

AS
BEGIN
  DECLARE
    K_ESTADO_SOLICITADA           	NUMBER:= 1;
    vl_cod_item_carga_otra_ues		NUMBER;
    vl_rut_ant						NUMBER;
	vl_fecha_pago_ant				DATE;
	vl_cod_origen_pago_ant			NUMBER;
	vl_fecha_deposito_ant			DATE;
	vl_nro_cuota_ant				NUMBER;
    vl_cod_deuda_ant				NUMBER;
    vl_ano_cuota_ant				NUMBER;
	vl_rut_pos						NUMBER;
	vl_fecha_pago_pos				DATE;
	vl_cod_origen_pago_pos			NUMBER;
	vl_fecha_deposito_pos			DATE;
	vl_nro_cuota_pos				NUMBER;
    vl_cod_deuda_pos				NUMBER;
    vl_ano_cuota_pos				NUMBER;
	vl_obs							varchar(200):='';
    
			
	BEGIN
		if(ve_operacion = 'INSERT') then 
			
			INSERT INTO ITEM_CARGA_OTRA_UES
					   ( COD_ITEM_CARGA_OTRA_UES
					    ,COD_CARGA_OTRA_UES     
					    ,COD_ESTADO_CARGA           
					    ,RUT                        
					    ,DIG_VERIF                  
					    ,FOLIO    
                        ,COD_DEUDA
                        ,ANO_CUOTA
					    ,MONTO_UTM           
					    ,MONTO_PESOS        
					    ,FECHA_PAGO                
					    ,COD_ORIGEN_PAGO
					    ,FECHA_DEPOSITO
						,NRO_CUOTA
						,MONTO_DESCTO_UTM
						,MONTO_DESCTO_PESOS)
				 VALUES(item_carga_otra_ues_sq.NEXTVAL
					    ,ve_cod_carga_otra_ues     
					    ,ve_cod_estado_carga           
					    ,ve_rut                        
					    ,ve_dig_verif                  
					    ,ve_folio 
                        ,ve_cod_deuda
                        ,ve_ano_cuota
					    ,ve_monto_utm          
					    ,ve_monto_pesos   
					    ,ve_fecha_pago                
					    ,ve_cod_origen_pago
					    ,ve_fecha_deposito
						,ve_nro_cuota
						,ve_monto_descto_utm
						,ve_monto_descto_pesos);
			
			 SELECT  item_carga_otra_ues_sq.CURRVAL
			 INTO    vl_cod_item_carga_otra_ues
			 FROM    dual;   
					 
		    insert into BITACORA_CARGA_OTRA_UES 
		    			(COD_BITACORA_CARGA_OTRA_UES 
  						,FECHA_REGISTRO
						,COD_CARGA_OTRA_UES       
						,COD_ITEM_CARGA_OTRA_UES 
					    ,COD_USUARIO                 
						,OBSERVACION)
			      values(bitacora_carga_otra_ues_sq.NEXTVAL
						,sysdate
					    ,ve_cod_carga_otra_ues
						,vl_cod_item_carga_otra_ues
						,ve_cod_usuario
						,'Inserta Item de forma exitosa'); 
   
		end if;	
		if(ve_operacion = 'UPDATE') then 
		
			
			select  RUT			 	
				   ,FECHA_PAGO		
				   ,COD_ORIGEN_PAGO 
				   ,FECHA_DEPOSITO  
				   ,NRO_CUOTA
                   ,COD_DEUDA
                   ,ANO_CUOTA
			into vl_rut_ant
				,vl_fecha_pago_ant
				,vl_cod_origen_pago_ant
				,vl_fecha_deposito_ant
				,vl_nro_cuota_ant
                ,vl_cod_deuda_ant
                ,vl_ano_cuota_ant
			from item_carga_otra_ues
			where cod_item_carga_otra_ues= ve_cod_item_carga_otra_ues;
		
			update ITEM_CARGA_OTRA_UES
			set RUT			 	= ve_rut
			   ,FECHA_PAGO		= ve_fecha_pago
			   ,COD_ORIGEN_PAGO = ve_cod_origen_pago
			   ,FECHA_DEPOSITO  = ve_fecha_deposito
			   ,NRO_CUOTA       = ve_nro_cuota
               ,COD_DEUDA       = ve_cod_deuda
               ,ANO_CUOTA       = ve_ano_cuota
			where COD_ITEM_CARGA_OTRA_UES= ve_cod_item_carga_otra_ues;
			
			select  RUT			 	
				   ,FECHA_PAGO		
				   ,COD_ORIGEN_PAGO 
				   ,FECHA_DEPOSITO  
				   ,NRO_CUOTA
                   ,COD_DEUDA
                   ,ANO_CUOTA
			into vl_rut_pos
				,vl_fecha_pago_pos
				,vl_cod_origen_pago_pos
				,vl_fecha_deposito_pos
				,vl_nro_cuota_pos
                ,vl_cod_deuda_pos
                ,vl_ano_cuota_pos
			from item_carga_otra_ues
			where cod_item_carga_otra_ues= ve_cod_item_carga_otra_ues;
			
			if(vl_rut_ant <> vl_rut_pos)then
				vl_obs := vl_obs ||' '||'Cambia rut:'||''||to_char(vl_rut_ant);
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
				vl_obs := vl_obs ||' '||'Cambia Nro. Cuota:'||''||to_char(vl_nro_cuota_ant);
			end if;
            
            if(vl_cod_deuda_ant <> vl_cod_deuda_pos)then
				vl_obs := vl_obs ||' '||'Cambia Cod. Deuda:'||''||to_char(vl_cod_deuda_ant);
			end if;
            
            if(vl_ano_cuota_ant <> vl_ano_cuota_pos)then
				vl_obs := vl_obs ||' '||'Cambia Año Cuota:'||''||to_char(vl_ano_cuota_ant);
			end if;
			
			insert into BITACORA_CARGA_OTRA_UES 
		    			(COD_BITACORA_CARGA_OTRA_UES
  						,FECHA_REGISTRO
						,COD_CARGA_OTRA_UES       
						,COD_ITEM_CARGA_OTRA_UES
					    ,COD_USUARIO                 
						,OBSERVACION)
			      values(bitacora_carga_otra_ues_sq.NEXTVAL
						,sysdate
					    ,ve_cod_carga_otra_ues
						,ve_cod_item_carga_otra_ues
						,ve_cod_usuario
						,vl_obs); 
			
		end if;	
		
		commit;
		
	END;
END;