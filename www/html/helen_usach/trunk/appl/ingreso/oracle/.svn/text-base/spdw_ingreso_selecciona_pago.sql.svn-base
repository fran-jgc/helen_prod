PROMPT CREATE OR REPLACE PROCEDURE spdw_ingreso_selecciona_pago
CREATE OR REPLACE PROCEDURE spdw_ingreso_selecciona_pago(ve_rut NUMBER)
AS
BEGIN
	
	-- busca los convenios  para pago inicial
	insert into TEMP_SELEC_PAGO
	   (SELECCION					
		,TIPO						
		,CODIGO
		,FECHA_CUOTA	
		,COD_CUOTA_CONVENIO
		,NRO_CUOTA					
		,MONTO_MONEDA				
		,MONTO_PESOS				
		)		
	select 'N'
			,'CONVENIO_PAGO_INICIAL'
			,C.COD_CONVENIO
      ,TO_CHAR(C.FECHA_CONVENIO, 'DD/MM/YYYY')
			,null
			,0
			,round(f_conv_saldo_pago_inicial(C.COD_CONVENIO) / f_mon_get_valor_moneda(T.COD_MONEDA, SYSDATE), 4)
			,f_conv_saldo_pago_inicial(C.COD_CONVENIO)
	from DEUDOR D, DEUDA DA, TIPO_DEUDA T, CONVENIO C
	where D.RUT = ve_rut
	  and DA.COD_DEUDOR = D.COD_DEUDOR
	  and T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
	  and C.COD_DEUDA = DA.COD_DEUDA
	  and C.COD_ESTADO_CONVENIO = 1	-- emitido
	  and f_conv_saldo_pago_inicial(C.COD_CONVENIO) > 0;

	-----------------
	-- busca los convenios con cuotas pendientes de pago
	insert into TEMP_SELEC_PAGO
	   (SELECCION					
		,TIPO						
		,CODIGO
		,FECHA_CUOTA	
		,COD_CUOTA_CONVENIO		
		,NRO_CUOTA					
		,MONTO_MONEDA				
		,MONTO_PESOS				
		)
	select 'N'
			,'CONVENIO_PAGO_CUOTA'
			,C.COD_CONVENIO
      ,TO_CHAR(CC.FECHA_CUOTA, 'DD/MM/YYYY')
			,CC.COD_CUOTA_CONVENIO
			,CC.NRO_CUOTA
			,f_conv_saldo_cuota(CC.COD_CUOTA_CONVENIO) 
			,round(f_conv_saldo_cuota(CC.COD_CUOTA_CONVENIO) * f_mon_get_valor_moneda(T.COD_MONEDA, SYSDATE), 0)
	from DEUDOR D, DEUDA DA, TIPO_DEUDA T, CONVENIO C, CUOTA_CONVENIO CC
	where D.RUT = ve_rut
	  and DA.COD_DEUDOR = D.COD_DEUDOR
	  and T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
	  and C.COD_DEUDA = DA.COD_DEUDA
	  and C.COD_ESTADO_CONVENIO = 2	-- aprobado
	  and CC.COD_CONVENIO = C.COD_CONVENIO
	  and f_conv_saldo_cuota(CC.COD_CUOTA_CONVENIO) > 0;
	  
	-----------
	-- busca las renegociaciones  para pago inicial
	insert into TEMP_SELEC_PAGO
	   (SELECCION					
		,TIPO						
		,CODIGO
		,FECHA_CUOTA	
		,COD_CUOTA_CONVENIO
		,NRO_CUOTA					
		,MONTO_MONEDA				
		,MONTO_PESOS				
		)		
	select 'N'
			,'RENEG_PAGO_INICIAL'
			,R.COD_RENEGOCIACION
      ,TO_CHAR(R.FECHA_RENEGOCIACION, 'DD/MM/YYYY')
			,null
			,0
			,round(f_reneg_saldo_pago_inicial(R.COD_RENEGOCIACION) / f_mon_get_valor_moneda(T.COD_MONEDA, SYSDATE), 4)
			,f_reneg_saldo_pago_inicial(R.COD_RENEGOCIACION)
	from DEUDOR D, DEUDA DA, TIPO_DEUDA T, RENEGOCIACION R
	where D.RUT = ve_rut
	  and DA.COD_DEUDOR = D.COD_DEUDOR
	  and T.COD_TIPO_DEUDA = DA.COD_TIPO_DEUDA
	  and R.COD_DEUDA = DA.COD_DEUDA
	  and R.COD_ESTADO_RENEGOCIACION = 1	-- emitido
	  and f_reneg_saldo_pago_inicial(R.COD_RENEGOCIACION) > 0;
	
	-----------------
	-- pago normal	
	insert into TEMP_SELEC_PAGO
	   (SELECCION					
		,TIPO						
		,CODIGO
		,FECHA_CUOTA			
		,COD_CUOTA_CONVENIO		
		,NRO_CUOTA					
		,MONTO_MONEDA				
		,MONTO_PESOS				
		)
	values
	   ('N'
		,'PAGO_NORMAL'
		,null
		,null
		,null
		,null
		,null
		,null
		);
	
END;