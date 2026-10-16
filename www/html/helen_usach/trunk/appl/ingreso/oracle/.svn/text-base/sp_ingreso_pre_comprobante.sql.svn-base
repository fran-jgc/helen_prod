---------------------- sp_ingreso_pre_comprobante --------------------------
CREATE OR REPLACE PROCEDURE sp_ingreso_pre_comprobante  (ve_cod_ingreso		NUMBER
														,ve_cod_comprobante	NUMBER)
AS
/*
Copia de COMPROBANTE => PRE_COMPROBANTE, PRE_ITEM_COMPROBANTE, PRE_ANALITICO_COMPROBANTE
*/
BEGIN
	BEGIN
		-- COMPROBANTE => PRE_COMPROBANTE
		insert into PRE_COMPROBANTE
				(COD_INGRESO
				,COD_COMPROBANTE           
				,FECHA_COMPROBANTE         
				,COD_USUARIO               
				,NOM_COMPROBANTE           
				,COD_FAMILIA_DEUDA         
				,COD_ESTADO_COMPROBANTE    
				,COD_TIPO_COMPROBANTE      
				,COD_ORIGEN_COMPROBANTE)
		select	 ve_cod_ingreso
				,COD_COMPROBANTE           
				,FECHA_COMPROBANTE         
				,COD_USUARIO               
				,NOM_COMPROBANTE           
				,COD_FAMILIA_DEUDA         
				,COD_ESTADO_COMPROBANTE    
				,COD_TIPO_COMPROBANTE      
				,COD_ORIGEN_COMPROBANTE
		from COMPROBANTE
		where COD_COMPROBANTE = ve_cod_comprobante;
		
		-- ITEM_COMPROBANTE => PRE_ITEM_COMPROBANTE
		insert into PRE_ITEM_COMPROBANTE
				(COD_INGRESO
				,COD_ITEM_COMPROBANTE       
				,COD_COMPROBANTE            
				,COD_CUENTA_CONTABLE        
				,MONTO_DEBE                 
				,MONTO_HABER)
		select	 ve_cod_ingreso
				,COD_ITEM_COMPROBANTE       
				,COD_COMPROBANTE            
				,COD_CUENTA_CONTABLE        
				,MONTO_DEBE                 
				,MONTO_HABER
		from ITEM_COMPROBANTE       
		where COD_COMPROBANTE = ve_cod_comprobante;
		
		-- ANALITICO_COMPROBANTE => PRE_ANALITICO_COMPROBANTE
		insert into PRE_ANALITICO_COMPROBANTE
				(COD_INGRESO                
				,COD_ANALITICO_COMPROBANTE  
				,COD_COMPROBANTE            
				,COD_DEUDA                  
				,COD_CUENTA_CONTABLE        
				,MONTO_DEBE                 
				,MONTO_HABER
				,COD_DEUDOR)
		select	 ve_cod_ingreso
				,COD_ANALITICO_COMPROBANTE  
				,COD_COMPROBANTE            
				,COD_DEUDA                  
				,COD_CUENTA_CONTABLE        
				,MONTO_DEBE                 
				,MONTO_HABER
				,COD_DEUDOR
		from ANALITICO_COMPROBANTE
		where COD_COMPROBANTE = ve_cod_comprobante;
		
		-- 
		update abono
		set cod_comprobante = null
		where COD_COMPROBANTE = ve_cod_comprobante;
	
		update descuento
		set cod_comprobante = null
		where COD_COMPROBANTE = ve_cod_comprobante;
	
		-- borra de las tabla originales
		delete ANALITICO_COMPROBANTE
		where COD_COMPROBANTE = ve_cod_comprobante;
	
		delete ITEM_COMPROBANTE
		where COD_COMPROBANTE = ve_cod_comprobante;
	
		delete COMPROBANTE
		where COD_COMPROBANTE = ve_cod_comprobante;

	END; 
END;
/