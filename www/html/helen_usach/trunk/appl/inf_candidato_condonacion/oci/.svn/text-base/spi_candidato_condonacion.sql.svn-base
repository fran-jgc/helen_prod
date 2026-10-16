CREATE OR REPLACE PROCEDURE spi_candidato_condonacion
	AS
	BEGIN
	  DECLARE
      	vl_total_registro		   NUMBER:=0;
	  BEGIN
	    INSERT INTO TEMP_CANDIDATO_CONDONACION
			      (RUT				  
				  ,DIG_VERIF          
				  ,COD_TIPO_DEUDA     
				  ,ANO_INICIO                
				  ,MONTO_INICIO_COBRO   
				  ,NOM_ESTADO_DEUDA   
				  ,NOM_CARTERA        
				  ,NOM_UNIVERSIDAD    
				  ,COD_DEUDA           
				  ,COD_DEUDOR         
				  ,COD_PLAN_PAGO       
				  ,CANT_PP                   
				  ,CANT_CUOTA         
				  )
	    SELECT de.rut
		      ,de.dig_verif
		      ,da.cod_tipo_deuda
		      ,To_Number(To_Char(da.fecha_inicio_cobro, 'yyyy')) ANO_INICIO
		      ,da.monto_inicio_cobro
		      ,e.nom_estado_deuda
		      ,c.nom_cartera
		      ,u.nom_universidad
		      ,da.cod_deuda
		      ,da.cod_deudor
		      ,p.cod_plan_pago
		      ,(SELECT Count(*) FROM plan_pago pp WHERE pp.cod_deuda = da.cod_deuda AND pp.ano_cobro <> 9999) CANT_PP
		      ,da.cant_cuota
		FROM deuda da, plan_pago p, deudor de
		    ,cartera c, universidad u, estado_deuda e
		WHERE da.cod_tipo_deuda = 'CS'
		AND da.cod_estado_deuda = 2
		AND p.cod_deuda = da.cod_deuda
		AND p.ano_cobro = 9999
		AND p.monto_capital + p.monto_interes > 0
		AND c.cod_cartera = da.cod_cartera
		AND de.cod_deudor = da.cod_deudor
		AND u.cod_universidad = da.cod_universidad
		AND e.cod_estado_deuda = da.cod_estado_deuda;
	    
	   
	   	SELECT Count(*)
	   	INTO vl_total_registro	
	    FROM deuda da, plan_pago p, deudor de
		    ,cartera c, universidad u, estado_deuda e
		WHERE da.cod_tipo_deuda = 'CS'
		AND da.cod_estado_deuda = 2
		AND p.cod_deuda = da.cod_deuda
		AND p.ano_cobro = 9999
		AND p.monto_capital + p.monto_interes > 0
		AND c.cod_cartera = da.cod_cartera
		AND de.cod_deudor = da.cod_deudor
		AND u.cod_universidad = da.cod_universidad
		AND e.cod_estado_deuda = da.cod_estado_deuda;
	  
	  
		--se modifico f_deuda_cand_condona_CS para que no considere necesario tener cuota 2021}
		UPDATE TEMP_CANDIDATO_CONDONACION
		SET ES_CAND_CONDONA = f_deuda_cand_condona_CS(cod_deuda)
		WHERE CANT_PP >= CANT_CUOTA;
		
		UPDATE TEMP_CANDIDATO_CONDONACION
		SET ES_CAND_CONDONA = 'N'
		WHERE ES_CAND_CONDONA IS null;

		UPDATE TEMP_CANDIDATO_CONDONACION
		SET SALDO_99 = f_pp_saldo_plan_pago(cod_plan_pago, sysdate)
		WHERE ES_CAND_CONDONA = 'S';
	
		UPDATE TEMP_CANDIDATO_CONDONACION
		SET EMAIL = (SELECT di.email FROM DIRECCION di WHERE di.COD_DIRECCION = f_get_direccion_deudor(TEMP_CANDIDATO_CONDONACION.COD_DEUDOR ,'P'))
		WHERE ES_CAND_CONDONA = 'S';

		UPDATE TEMP_CANDIDATO_CONDONACION
		SET ANO_ULTIMO_PP = (SELECT max(pp.ANO_COBRO) FROM plan_pago pp WHERE pp.cod_deuda = TEMP_CANDIDATO_CONDONACION.cod_deuda AND pp.ano_cobro <> 9999)
		WHERE ES_CAND_CONDONA = 'S';
	
      if(vl_total_registro=0)then
      	INSERT INTO TEMP_CANDIDATO_CONDONACION
			      (RUT				  
				  ,DIG_VERIF          
				  ,COD_TIPO_DEUDA     
				  ,ANO_INICIO                
				  ,MONTO_INICIO_COBRO   
				  ,NOM_ESTADO_DEUDA   
				  ,NOM_CARTERA        
				  ,NOM_UNIVERSIDAD    
				  ,COD_DEUDA           
				  ,COD_DEUDOR         
				  ,COD_PLAN_PAGO       
				  ,CANT_PP                   
				  ,CANT_CUOTA         
				  )
		  VALUES (null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null
		  		 ,null);
      end if;
      
  END;
END spi_candidato_condonacion;
/