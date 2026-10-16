CREATE OR REPLACE PROCEDURE spi_dato_contacto(ve_cod_tipo_deuda   VARCHAR2
                                                ,ve_ano_inicio      NUMBER
                                                ,ve_ano_termino     NUMBER)
AS
BEGIN
  
	INSERT INTO TEMP_DATOS_CONTACTO
      (RUT
      ,DIG_VERIF
      ,COD_DEUDA              
	  ,NOMBRES				  
	  ,PATERNO			      
	  ,MATERNO			      
	  ,TIPO_DEUDA		      
	  ,CARTERA				  
	  ,UNIVERSIDAD    		  
	  ,ESTADO_DEUDA    		  
	  ,MONTO_INICIO_COBRO 	            
	  ,CANT_CUOTA       
	  ,SALDO_DEUDA      
	  ,TELEFONO         
	  ,CELULAR	
	  ,DIRECCION
	  ,COMUNA 	
	  ,CIUDAD	
	  ,REGION	
	  ,MAIL	
    ,AIC
      )
    SELECT To_Char(de.rut) 
          ,de.dig_verif rut
          ,d.cod_deuda
          ,de.NOMBRE 
          ,de.APELLIDO_PATERNO 
          ,de.APELLIDO_MATERNO 
          ,td.NOM_TIPO_DEUDA 
          ,ca.NOM_CARTERA 
          ,u.NOM_UNIVERSIDAD 
          ,e.NOM_ESTADO_DEUDA 
          ,d.MONTO_INICIO_COBRO 
          ,d.CANT_CUOTA 
          ,f_deuda_saldo_total(d.cod_deuda)
          ,nvl(di.TELEFONO,'')
          ,nvl(di.CELULAR ,'')
          ,nvl(di.DIRECCION ,'')
          ,nvl(c.NOM_COMUNA ,'')
          ,nvl(ci.NOM_CIUDAD ,'')
          ,nvl(r.NOM_REGION ,'')
          ,nvl(di.EMAIL ,'')
          ,EXTRACT(YEAR FROM d.FECHA_INICIO_COBRO)
    FROM deuda d, deudor de, ESTADO_DEUDA e,TIPO_DEUDA td,CARTERA ca, UNIVERSIDAD u
    ,DIRECCION di LEFT OUTER JOIN CIUDAD ci ON di.COD_CIUDAD= ci.COD_CIUDAD
    			  LEFT OUTER JOIN COMUNA c ON di.COD_COMUNA = c.COD_COMUNA 
    			  LEFT OUTER JOIN REGION r ON ci.COD_REGION = r.COD_REGION 
    WHERE d.cod_tipo_deuda = ve_cod_tipo_deuda
      AND e.cod_estado_deuda = d.cod_estado_deuda
      AND de.cod_deudor = d.cod_deudor
      AND EXTRACT(YEAR FROM d.FECHA_INICIO_COBRO ) BETWEEN ve_ano_inicio AND ve_ano_termino
      AND di.COD_DIRECCION = F_GET_DIRECCION_DEUDOR(de.cod_deudor,'P')
      AND  td.COD_TIPO_DEUDA = d.COD_TIPO_DEUDA 
      AND ca.COD_CARTERA = d.COD_CARTERA 
      AND u.COD_UNIVERSIDAD = d.COD_UNIVERSIDAD 
    ORDER BY de.rut;

END spi_dato_contacto;
/

               