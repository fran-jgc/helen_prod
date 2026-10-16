CREATE OR REPLACE PROCEDURE spu_item_carga_tgr	(ve_operacion 				   IN  VARCHAR
												,ve_cod_item_carga_tgr    	   IN  NUMBER  := NULL 
											    ,ve_cod_carga_tgr	           IN  NUMBER  := NULL 
											    ,ve_cod_estado_carga           IN  NUMBER  := NULL
											    ,ve_rut                        IN  NUMBER  := NULL
											    ,ve_dig_verif                  IN  VARCHAR := NULL
											    ,ve_folio                      IN  VARCHAR := NULL 
											    ,ve_monto_deuda_utm            IN  NUMBER  := NULL
											    ,ve_monto_rete_utm         	   IN  NUMBER  := NULL
											    ,ve_monto_pesos                IN  NUMBER  := NULL
											    ,ve_fecha_pago                 IN  DATE	   := NULL 
											    ,ve_fecha_deposito             IN  DATE	   := NULL
											    ,ve_cod_usuario      		   IN  NUMBER  := NULL )	

AS
BEGIN
  DECLARE
    K_ESTADO_SOLICITADA           	NUMBER:= 1;
    vl_cod_item_carga_tgr			NUMBER;
    vl_rut_ant						NUMBER;
	vl_fecha_pago_ant				DATE;
	vl_cod_origen_pago_ant			NUMBER;
	vl_fecha_deposito_ant			DATE;
	vl_rut_pos						NUMBER;
	vl_fecha_pago_pos				DATE;
	vl_obs							varchar(200):='';
    
			
	BEGIN
		if(ve_operacion = 'INSERT') then 
			
			INSERT INTO ITEM_CARGA_TGR
					   ( COD_ITEM_CARGA_TGR
					    ,COD_CARGA_TGR   
					    ,COD_ESTADO_CARGA           
					    ,RUT                        
					    ,DIG_VERIF                  
					    ,FOLIO   
					    ,MONTO_DEUDA_UTM
					    ,MONTO_RETENIDO_UTM
					    ,MONTO_PESOS        
					    ,FECHA_PAGO)
				 VALUES(item_carga_tgr_sq.NEXTVAL
					    ,ve_cod_carga_tgr     
					    ,ve_cod_estado_carga           
					    ,ve_rut                        
					    ,ve_dig_verif                  
					    ,ve_folio
					    ,ve_monto_deuda_utm
					    ,ve_monto_rete_utm
					    ,ve_monto_pesos   
					    ,ve_fecha_pago);
			
			 SELECT  item_carga_tgr_sq.CURRVAL
			 INTO    vl_cod_item_carga_tgr
			 FROM    dual;   
					 
		    insert into BITACORA_CARGA_TGR
		    			(COD_BITACORA_CARGA_TGR
  						,FECHA_REGISTRO
						,COD_CARGA_TGR 
						,COD_ITEM_CARGA_TGR
					    ,COD_USUARIO                 
						,OBSERVACION)
			      values(bitacora_carga_tgr_sq.NEXTVAL
						,sysdate
					    ,ve_cod_carga_tgr
						,vl_cod_item_carga_tgr
						,ve_cod_usuario
						,'Inserta Item de forma exitosa'); 
   
		end if;	
		if(ve_operacion = 'UPDATE') then 
		
			
			select  RUT			 	
				   ,FECHA_PAGO		
			into vl_rut_ant
				,vl_fecha_pago_ant
			from item_carga_tgr
			where cod_item_carga_tgr= ve_cod_item_carga_tgr;
		
			update ITEM_CARGA_TGR
			set RUT			 	= ve_rut
			   ,FECHA_PAGO		= ve_fecha_pago
			where COD_ITEM_CARGA_TGR= ve_cod_item_carga_tgr;
			
			select  RUT			 	
				   ,FECHA_PAGO		
			into vl_rut_pos
				,vl_fecha_pago_pos
			from item_carga_tgr
			where cod_item_carga_tgr= ve_cod_item_carga_tgr;
			
			if(vl_rut_ant <> vl_rut_pos)then
				vl_obs := vl_obs ||' '||'Cambia rut:'||''||to_char(vl_rut_ant);
			end if;
			
			if(vl_fecha_pago_ant <> vl_fecha_pago_pos)then
				vl_obs := vl_obs ||' '||'Cambia F. Pago:'||''||to_char(vl_fecha_pago_ant, 'dd/mm/yyyy');
			end if;
			
			insert into BITACORA_CARGA_TGR
		    			(COD_BITACORA_CARGA_TGR
  						,FECHA_REGISTRO
						,COD_CARGA_TGR       
						,COD_ITEM_CARGA_TGR
					    ,COD_USUARIO                 
						,OBSERVACION)
			      values(bitacora_carga_tgr_sq.NEXTVAL
						,sysdate
					    ,ve_cod_carga_tgr
						,ve_cod_item_carga_tgr
						,ve_cod_usuario
						,vl_obs); 
			
		end if;	
		
		commit;
	 
	END;
END;