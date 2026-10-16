CREATE OR REPLACE PROCEDURE spi_periodo_gracia (ve_cod_tipo_deuda	VARCHAR2
												,ve_ano				NUMBER)
AS
BEGIN
  DECLARE
    K_DEUDA_NO_EXIGIBLE	NUMBER:=1;
	vl_fecha			DATE;
	
  BEGIN
	vl_fecha := To_Date('31/12/' || To_Char(ve_ano), 'dd/mm/yyyy') + 86399/(24*60*3600);
	  
    INSERT INTO TEMP_PERIODO_GRACIA
		(RUT
		,NOMBRE
		,TIPO_DEUDA
		,COD_DEUDA
		,FECHA_INICIO_COBRO
		,MONTO_INICIO_COBRO
		,SALDO_ACTUAL_DEUDA
		,DIRECCION
		,COMUNA
		,CIUDAD
		,REGION
		,TELEFONO
		,MAIL)
	SELECT	 to_char(do.rut) || '-' || do.dig_verif  rut
			,do.nombre || ' ' || do.apellido_paterno || ' ' || do.apellido_materno NOMBRE
			,de.cod_tipo_deuda
			,de.cod_deuda
			,to_char(de.fecha_inicio_cobro,'dd/mm/yyyy')
			,de.monto_inicio_cobro
			,f_deuda_saldo_total(COD_DEUDA,sysdate) SALDO
			,d.direccion
	   		,c.nom_comuna
	   		,cc.nom_ciudad
	   		,r.nom_region
	   		,d.telefono
			,d.email
    FROM  deuda de, deudor do , direccion d	left outer join comuna c on d.cod_comuna = c.cod_comuna 
											left outer join ciudad cc on d.cod_ciudad = cc.cod_ciudad 
											left outer join pais p on d.cod_pais = p.cod_pais
											left outer join region r on p.cod_pais = r.cod_pais
    WHERE de.cod_estado_deuda = K_DEUDA_NO_EXIGIBLE
      AND de.fecha_inicio_cobro >= vl_fecha
      AND de.cod_tipo_deuda = ve_cod_tipo_deuda
      AND de.cod_deudor = do.cod_deudor
      AND do.cod_deudor = d.cod_deudor
      AND d.TIPO_DIRECCION = 'P'
    ORDER BY do.rut, de.cod_deuda, de.cod_tipo_deuda;
  END;
END spi_periodo_gracia;
/