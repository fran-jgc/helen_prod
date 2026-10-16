CREATE OR REPLACE PROCEDURE spp_comprobante_ingreso(ve_cod_origen_pago		numeric
													,ve_fecha_desde			date
													,ve_fecha_hasta			date
													,ve_fecha_comprobante	date
													,ve_cod_usuario			numeric
													)
/*
 EXEC spp_comprobante_ingreso(1	,'01/01/2016', '05/01/2016'  ,sysdate , 1);
		
*/										
AS
BEGIN
    DECLARE  
    	
    	vl_glosa_compro				VARCHAR2(100);
    	vl_cod_comprobante			NUMBER;
    	vl_cod_cuenta_ingreso		NUMBER;
    	vl_tipo_abono				VARCHAR2(10);
    	vl_nom_origen_pago			VARCHAR2(100);
    	vl_cod_cuenta_dscto			NUMBER;
    	vl_cod_analitico			NUMBER;
    	vl_cta_excedente          	NUMBER;
    	vl_cod_registro_comp		NUMBER;	
    	vl_contador					NUMBER:= 0;
    	K_CTA_DSCTO					NUMBER:= 9;
    	K_CTA_CAJA					NUMBER:= 17; --consultar
    	K_COMP_PENDIENTE			NUMBER:= 1;
    	K_TIPO_COMP_INGRESO			NUMBER:= 1;
    	K_ORIGEN_INGRESO_PAGO		NUMBER:= 11;
    	K_CONCEPTO_CUENTA_EXCEDENTE NUMBER:=19;
    	vl_cod_tipo_deuda			VARCHAR2(20);
    	vl_cod_deudor				NUMBER;
    	vl_cod_cuenta_bco			NUMBER;
    	vl_excedente_pesos			NUMBER;
    	vl_cod_deuda				NUMBER;
    
    /*No se consideran los abonos y descuentos de Carga de datos*/
    
    CURSOR C_ABONO_DSCTO IS
    select 'ABONO' TIPO,
    	   A.COD_ABONO,
		   A.COD_COMPONENTE_DEUDA,
		   A.FECHA_ABONO,
		   A.MONTO_CAPITAL,
		   A.MONTO_INTERES,
		   A.MONTO_MULTA,
		   A.MONTO_PESOS,
		   nvl(A.DIFERENCIA_PESOS, 0) DIF_PESOS,
		   D.COD_TIPO_DEUDA,
		   T.COD_MONEDA,
		   T.COD_FAMILIA_DEUDA,
		   0 COD_DESCUENTO,
		   0 DSCTO_CAPITAL,
		   0 DSCTO_INT_NORMAL,
		   0 DSCTO_INT_PENAL,
		   0 DSCTO_PESOS,
		   D.COD_DEUDA,
		   0 MONTO_EXCEDENTE
	FROM   ABONO A left outer join PLAN_PAGO P ON P.COD_PLAN_PAGO = A.COD_PLAN_PAGO
		, DEUDA D,TIPO_DEUDA T
	WHERE  A.COD_COMPROBANTE is NULL 
		   AND A.COD_ESTADO_PAGO = 1
    AND A.COD_ORIGEN_PAGO = ve_cod_origen_pago
    AND A.FECHA_ABONO BETWEEN ve_fecha_desde AND ve_fecha_hasta
    AND D.COD_DEUDA = A.COD_DEUDA 
    AND T.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA 
	union
	select 'DESCUENTO' TIPO,
		   0 COD_ABONO,
		   A.COD_COMPONENTE_DEUDA,
		   A.FECHA_DESCUENTO FECHA_ABONO,
		   0 MONTO_CAPITAL,
		   0 MONTO_INTERES,
		   0 MONTO_MULTA,
		   0 MONTO_PESOS,
		   0 DIF_PESOS,
		   D.COD_TIPO_DEUDA,
		   T.COD_MONEDA,
		   T.COD_FAMILIA_DEUDA,
		   A.COD_DESCUENTO,
		   A.MONTO_CAPITAL DSCTO_CAPITAL,
		   A.MONTO_INTERES DSCTO_INT_NORMAL,
		   A.MONTO_MULTA DSCTO_INT_PENAL,
		   A.MONTO_PESOS DSCTO_PESOS,
		   D.COD_DEUDA,
		   0 MONTO_EXCEDENTE
	FROM   DESCUENTO A left outer join PLAN_PAGO P ON P.COD_PLAN_PAGO = A.COD_PLAN_PAGO 
			, DEUDA D, TIPO_DEUDA T
	WHERE  A.COD_COMPROBANTE is NULL 
    AND A.COD_ESTADO_PAGO = 1 
    AND A.COD_ORIGEN_PAGO = ve_cod_origen_pago
    AND A.FECHA_DESCUENTO BETWEEN ve_fecha_desde AND ve_fecha_hasta
    AND D.COD_DEUDA = A.COD_DEUDA
    AND T.COD_TIPO_DEUDA = D.COD_TIPO_DEUDA
    union
    select 'EXCEDENTE' TIPO,
		   E.COD_EXCEDENTE COD_ABONO,
		   0 COD_COMPONENTE_DEUDA,
		   E.FECHA_EXCEDENTE FECHA_ABONO,
		   0 MONTO_CAPITAL,
		   0 MONTO_INTERES,
		   0 MONTO_MULTA,
		   0 MONTO_PESOS,
		   0 DIF_PESOS,
		   NULL COD_TIPO_DEUDA,
		   NULL COD_MONEDA,
		   NULL COD_FAMILIA_DEUDA,
		   0 COD_DESCUENTO,
		   0 DSCTO_CAPITAL,
		   0 DSCTO_INT_NORMAL,
		   0 DSCTO_INT_PENAL,
		   0 DSCTO_PESOS,
		   E.COD_ABONO COD_DEUDA,
       	   E.MONTO_EXCEDENTE
	FROM   EXCEDENTE E 
	WHERE E.COD_COMPROBANTE is NULL
	AND E.COD_ESTADO_EXCEDENTE = 1 
	AND E.COD_ABONO in (select cod_abono from abono 
						where COD_COMPROBANTE is NULL 
						AND COD_ESTADO_PAGO = 1
					    AND COD_ORIGEN_PAGO = ve_cod_origen_pago)
	AND E.FECHA_EXCEDENTE BETWEEN ve_fecha_desde AND ve_fecha_hasta
	order by FECHA_ABONO, TIPO;
     
  BEGIN	
	
	select  nom_origen_pago
  	INTO vl_nom_origen_pago
	from origen_pago
	where COD_ORIGEN_PAGO = ve_cod_origen_pago;
	
	
	vl_glosa_compro := 'Abonos Ingreso:' ||	to_char(vl_nom_origen_pago) || ', desde : ' || to_char(ve_fecha_desde, 'dd/mm/yyyy') || ' hasta: ' || to_char(ve_fecha_hasta, 'dd/mm/yyyy');
	spu_comprobante ('INSERT',NULL
					,ve_fecha_comprobante
					,ve_cod_usuario
					,vl_glosa_compro
					,1 --cod_familia_deuda
					,K_COMP_PENDIENTE
					,K_TIPO_COMP_INGRESO
					,K_ORIGEN_INGRESO_PAGO
					,NULL
					,sysdate);

	SELECT  comprobante_sq.CURRVAL
    INTO vl_cod_comprobante
    FROM dual;
    
    insert into registro_comp_ingreso(cod_registro_comp_ingreso
									 ,fecha_desde 
									 ,fecha_hasta 
									 ,fecha_comprobante)
							  values (registro_comp_ingreso_sq.nextval
							         ,ve_fecha_desde
							         ,ve_fecha_hasta
							         ,ve_fecha_comprobante);
							         
	SELECT  registro_comp_ingreso_sq.CURRVAL
    INTO vl_cod_registro_comp
    FROM dual;
    
    update comprobante set cod_registro_comp_ingreso= vl_cod_registro_comp
    where cod_comprobante = vl_cod_comprobante;
    
    FOR c IN C_ABONO_DSCTO LOOP
    
    	vl_tipo_abono := c.TIPO;
    	  
    	--ABONOS
    	if(vl_tipo_abono = 'ABONO')then 
	    	-- obtiene la cuenta de ingreso por tipo deuda
			vl_cod_cuenta_ingreso := f_comp_get_cuenta_tipo_deuda(c.COD_TIPO_DEUDA, K_CTA_CAJA);
	
			spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_ingreso,  c.MONTO_PESOS, 0);
			spu_analitico ('INSERT', NULL, vl_cod_comprobante, c.COD_DEUDA, vl_cod_cuenta_ingreso, c.MONTO_PESOS, 0);
			
			sp_comp_ing (vl_cod_comprobante, c.COD_ABONO);
			
			update ABONO
			set    COD_COMPROBANTE = vl_cod_comprobante
			where  COD_ABONO = c.COD_ABONO;
			
			vl_contador := vl_contador + 1;
		
		end if;
		
		--DESCUENTOS
		if(vl_tipo_abono = 'DESCUENTO')then 
			vl_cod_cuenta_dscto := f_comp_get_cuenta_tipo_deuda(c.COD_TIPO_DEUDA, K_CTA_DSCTO);
			spu_item_comprobante ('INSERT',NULL,vl_cod_comprobante,vl_cod_cuenta_dscto,c.DSCTO_PESOS,0);
			spu_analitico ('INSERT',NULL,vl_cod_comprobante,c.COD_DEUDA,vl_cod_cuenta_dscto,c.DSCTO_PESOS,0);
			
			sp_comp_ing	(vl_cod_comprobante, NULL, c.COD_DESCUENTO);
			
			update DESCUENTO
			set    COD_COMPROBANTE = vl_cod_comprobante
			where  COD_DESCUENTO = c.COD_DESCUENTO;
			
			vl_contador := vl_contador + 1;
		end if; 
		
		if(vl_tipo_abono = 'EXCEDENTE')then 
		
			vl_excedente_pesos := c.MONTO_EXCEDENTE;
			
			select cod_deuda
			into vl_cod_deuda 
			from abono
			where cod_abono = c.COD_DEUDA;
			
			--DEBE
			vl_cod_cuenta_bco := 11102; --BANCO DE CHILE
			spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cod_cuenta_bco,  vl_excedente_pesos, 0);
	        spu_analitico ('INSERT', NULL, vl_cod_comprobante, vl_cod_deuda, vl_cod_cuenta_bco, vl_excedente_pesos, 0);
	        
	        select cod_tipo_deuda
	        into vl_cod_tipo_deuda
	        from deuda
	        where cod_deuda = vl_cod_deuda;
			
			--HABER
			vl_cta_excedente:= f_comp_get_cuenta_tipo_deuda(vl_cod_tipo_deuda, K_CONCEPTO_CUENTA_EXCEDENTE);
			spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cta_excedente,  0, vl_excedente_pesos);
	        spu_analitico ('INSERT', NULL, vl_cod_comprobante, vl_cod_deuda, vl_cta_excedente, 0, vl_excedente_pesos);
	
			SELECT  analitico_comprobante_sq.CURRVAL
			INTO vl_cod_analitico
			FROM dual;
			
			select cod_deudor 
			into vl_cod_deudor
			from deuda 
			where cod_deuda = vl_cod_deuda;
	
	        update analitico_comprobante
			SET cod_deudor = vl_cod_deudor
			where cod_analitico_comprobante = vl_cod_analitico;
			
			update EXCEDENTE 
			set    COD_COMPROBANTE = vl_cod_comprobante
			where  COD_EXCEDENTE = c.COD_ABONO;
			
			vl_contador := vl_contador + 1;
		end if; 

	END LOOP;
	
	sp_actualiza_item (vl_cod_comprobante);
	
	if(vl_contador = 0)then
		delete comprobante where cod_comprobante = vl_cod_comprobante;
	end if;
	
	commit;
END;
END;