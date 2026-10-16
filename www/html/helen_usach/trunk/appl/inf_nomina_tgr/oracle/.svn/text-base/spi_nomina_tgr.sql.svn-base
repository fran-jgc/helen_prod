PROMPT CREATE OR REPLACE PROCEDURE spi_nomina_tgr
CREATE OR REPLACE PROCEDURE spi_nomina_tgr   (ve_cod_usuario		NUMBER)
AS
BEGIN
	DECLARE	 K_COD_FAMILIA_DEUDA			  	NUMBER := 1;
			K_TIPO_NOMINA_TGR			      	NUMBER := 25;
			K_PARAM_RUT_U				        NUMBER := 4;
			K_PARAM_NOM_U				        NUMBER := 5;
			vl_ano_actual						VARCHAR2(4):= extract(YEAR FROM SYSDATE);
			K_FECHA_LIQUIDACION		      		DATE := to_date('31/05/'||vl_ano_actual||'23:59:59', 'dd/mm/yyyy hh24:mi:ss');
			K_FECHA_LIQUIDACION2		    	VARCHAR2(8) := vl_ano_actual||'0531';
			vl_glosa_bitacora			      	VARCHAR2(100);
			vl_count_cruzado			      	NUMBER;
			vc_cod_deudor				        NUMBER;
			vc_cod_deuda				        NUMBER;
			K_TIPO_REGISTRO			        	VARCHAR2(100) := '2';
			vc_rut_deudor				        NUMBER;
			vc_dv_rut_deudor			      	VARCHAR2(100);
			vc_ap_paterno_deudor		    	VARCHAR2(100);
			vc_ap_materno_deudor		    	VARCHAR2(100);
			vc_nombres_deudor			      	VARCHAR2(100);
			vc_rut_retenedor			      	NUMBER;
			vc_ap_paterno_retenedor	    		VARCHAR2(100);
			vc_ap_materno_retenedor	    		VARCHAR2(100);
			vc_nombres_retenedor		    	VARCHAR2(100);
			vc_saldo_componente		      		NUMBER;
			vl_marca_deuda			        	VARCHAR2(1);
			vl_rut_universidad		      		VARCHAR2(8);
			vl_dv_rut_universidad		    	VARCHAR2(1);
			vl_razon_universidad		    	VARCHAR2(100);
			vl_rut_universidad_conjuntas		VARCHAR2(100);
			vl_nro_cta_bancaria_retenedor		VARCHAR2(100);
			vl_banco_cta_retenedor	    		VARCHAR2(36);
			K_TIPO_CTA_RETENEDOR		    	VARCHAR2(1) := '1';
			K_TIPO_MONEDA_UTM			      	VARCHAR2(1) := '2';
			vl_cod_banco				        NUMBER;
			K_AP_MATERNO_RETENEDOR	    		VARCHAR2(100);
			K_NOMBRES_RETENEDOR		      		VARCHAR2(100);
			K_ESTADO_DEUDA_NORMAL		    	NUMBER := 2;
			vl_linea					        VARCHAR2(535);
			vl_linea_control                    VARCHAR2(535);
			vl_rut_deudor				        VARCHAR2(10);
			vl_saldo_componente		      		VARCHAR2(15);
			vl_rut_retenedor			      	VARCHAR2(100);
			vl_ap_paterno_deudor		    	VARCHAR2(100);
			K_PARAM_U_POR_DEFECTO		    	NUMBER := 3;
			
			
			K_origen                   		VARCHAR2(1) := 'M';
			K_TIPO_REG_ENCABEZADO 	   		VARCHAR2(1) := '1';--ENCABEZADO
			vl_nom_archivo					VARCHAR2(20):= RPAD('NOMINA TGR',20, ' ');
			vl_fecha_grabacion				VARCHAR2(20):= RPAD(to_char(sysdate,'dd/mm/yyyy'),20, ' ');
			vl_rut_admim					VARCHAR2(11):= RPAD(' ',11, ' ');
			vl_dv_admim						VARCHAR2(1):= RPAD(' ',1, ' ');
			vl_nro_cuenta					VARCHAR2(15):= RPAD(' ',15, ' ');
			vl_banco_cuenta					VARCHAR2(36):= RPAD(' ',36, ' ');
			vl_tipo_cuenta					VARCHAR2(1):= RPAD(' ',1, ' ');
			vl_origen						VARCHAR2(1):= 'M';
			vl_filler						VARCHAR2(429):= RPAD(' ',429, ' ');
			K_TIPO_REG_CONTROL 				VARCHAR2(1) := '3';--CONTROL
			vl_cantidad_reg					VARCHAR2(5);
			vl_cantidad_tipo1				VARCHAR2(5);
			vl_cantidad_tipo2				NUMBER := 0;
			vl_cantidad_tipo2_string		VARCHAR2(5);
			vl_monto_utm					NUMBER(14,2):= 0;
			vl_monto_utm_string	    		VARCHAR2(17);
			vl_monto_pesos					NUMBER;
			vl_monto_pesos_string     		VARCHAR2(17);
			vl_monto_dolares				VARCHAR2(17):= RPAD(' ',17, ' ');
			vl_origen_control				VARCHAR2(1):= 'M';
			vl_filler_control				VARCHAR2(467):= RPAD(' ',467, ' '); 
			
			

      vcruzada_rut_universidad	  VARCHAR2(10);
      vcruzada_dig_verif_universidad VARCHAR2(1);
      vl_ap_paterno_retenedor     VARCHAR2(100);
      
      
       vl_linea_encabezado				VARCHAR2(535):= K_TIPO_REG_ENCABEZADO
													  ||vl_nom_archivo
													  ||vl_fecha_grabacion
													  ||vl_rut_admim
													  ||vl_dv_admim
													  ||vl_nro_cuenta
													  ||vl_banco_cuenta
													  ||vl_tipo_cuenta
													  ||vl_origen
													  ||vl_filler;


	CURSOR C_NOMINA_TGR IS
	SELECT	 DO.COD_DEUDOR
			,DO.RUT	RUT_DEUDOR
			,DO.DIG_VERIF	DV_RUT_DEUDOR
			,replace(DO.APELLIDO_PATERNO,'Ñ','N')	AP_PATERNO_DEUDOR
			,replace(DO.APELLIDO_MATERNO,'Ñ','N')	AP_MATERNO_DEUDOR
			,replace(DO.NOMBRE,'Ñ','N')	NOMBRES_DEUDOR
		    ,REPLACE(SUBSTR(f_get_parametro(K_PARAM_RUT_U),1,10),'.','') RUT_RETENEDOR
		    ,SUBSTR(f_get_parametro(K_PARAM_RUT_U),12,1) DV_RUT_RETENEDOR
			,f_get_parametro(K_PARAM_NOM_U) AP_PATERNO_RETENEDOR
			,Round(sum(f_deuda_saldo_componente(de.cod_deuda, 2,  K_FECHA_LIQUIDACION)), 2) SALDO_COMPONENTE
			,f_get_nrocuotas_morosas(DO.COD_DEUDOR) NRO_CUOTAS_MOROSAS
	FROM	DEUDOR DO, DEUDA DE, TIPO_DEUDA TD
	WHERE	DO.COD_DEUDOR = DE.COD_DEUDOR
	and de.COD_ESTADO_DEUDA = K_ESTADO_DEUDA_NORMAL
	AND	DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
	AND	TD.COD_FAMILIA_DEUDA = K_COD_FAMILIA_DEUDA
  	AND DE.COD_UNIVERSIDAD = 6  --USACH
  	AND DE.COD_CARTERA = 6
   	AND TD.COD_TIPO_DEUDA not in('CAM', 'CAME', 'CD', 'CRI')
  	GROUP BY DO.COD_DEUDOR
			,DO.RUT
			,DO.DIG_VERIF
			,DO.APELLIDO_PATERNO
			,DO.APELLIDO_MATERNO
			,DO.NOMBRE
	ORDER BY DO.APELLIDO_PATERNO
			,DO.APELLIDO_MATERNO
			,DO.NOMBRE ASC;

  CURSOR C_DEUDA(vc_cod_deudor NUMBER) IS
	select cod_deuda
	from DEUDA DE, TIPO_DEUDA TD
	where cod_deudor = vc_cod_deudor
				AND de.COD_ESTADO_DEUDA = K_ESTADO_DEUDA_NORMAL
				AND	DE.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
				AND	TD.COD_FAMILIA_DEUDA = K_COD_FAMILIA_DEUDA
				AND f_deuda_saldo_componente(de.cod_deuda, 2,  K_FECHA_LIQUIDACION) > 0
				AND DE.COD_UNIVERSIDAD = 6  --USACH
  				AND DE.COD_CARTERA = 6
				AND TD.COD_TIPO_DEUDA not in('CAM', 'CAME', 'CD', 'CRI');

  CURSOR C_UNIV_CRUZADAS(vc_cod_deuda NUMBER) IS
	SELECT	COD_UNIVERSIDAD
	FROM    CRUZADO C
	WHERE   COD_DEUDA = vc_cod_deuda
	AND COD_UNIVERSIDAD	<> f_get_parametro(K_PARAM_U_POR_DEFECTO);


	BEGIN
    SELECT to_date('31/05/'||vl_ano_actual, 'dd/mm/yyyy')
    INTO K_FECHA_LIQUIDACION
    FROM dual;
    
    INSERT INTO TEMP_NOMINA_TGR (LINEA) VALUES(vl_linea_encabezado);

    FOR A IN C_NOMINA_TGR LOOP
			IF(A.SALDO_COMPONENTE > 0.10) THEN 

        --1=NO CONJUNTA, 2=CONJUNTA--- MARCA CORRESPONDE A CRUZADOS
			  vl_marca_deuda := '1';
			  vl_rut_universidad := '00000000';
        vl_dv_rut_universidad := ' ';
        SELECT RPAD(' ',55,' ')
        INTO vl_razon_universidad
        FROM DUAL;
        vl_rut_universidad_conjuntas := '';

        FOR B IN C_DEUDA(A.COD_DEUDOR) LOOP

          SELECT	COUNT(*)
          INTO vl_count_cruzado
			    FROM    CRUZADO C
			    WHERE   COD_DEUDA = B.COD_DEUDA
				    AND COD_UNIVERSIDAD	<> f_get_parametro(K_PARAM_U_POR_DEFECTO);

				  IF(vl_count_cruzado <> 0) THEN
	          vl_marca_deuda := '2';

            FOR C IN C_UNIV_CRUZADAS(B.COD_DEUDA) LOOP

	            SELECT SUBSTR(RUT_UNIVERSIDAD,1,8)
					          ,DIG_VERIF_UNIVERSIDAD
              INTO vcruzada_rut_universidad
                  ,vcruzada_dig_verif_universidad
              FROM UNIVERSIDAD
              WHERE COD_UNIVERSIDAD = C.COD_UNIVERSIDAD;

						  vl_rut_universidad_conjuntas := vl_rut_universidad_conjuntas || vcruzada_rut_universidad || vcruzada_dig_verif_universidad;

            END LOOP; --C_UNIV_CRUZADAS

            vl_rut_universidad_conjuntas := RPAD(vl_rut_universidad_conjuntas,90,' ');

          END IF;
        END LOOP; --C_DEUDA

        --el deudor NO tiene deudas cruzadas
			  IF (vl_marca_deuda = '1') THEN

          vl_rut_universidad := LPAD(REPLACE(SUBSTR(f_get_parametro(K_PARAM_RUT_U),1,10),'.',''), 8, '0');
				  vl_dv_rut_universidad := SUBSTR(f_get_parametro(K_PARAM_RUT_U),12,1);

				  vl_razon_universidad := RPAD(f_get_parametro(K_PARAM_NOM_U), 55, ' ');
				  vl_rut_universidad_conjuntas := RPad(' ',90, ' ');


			  END IF;

        ---NRO_CTA_BANCARIA_RETENEDOR, BANCO_CTA_RETENEDOR
			  SELECT	NRO_CUENTA_CORRIENTE
            ,COD_BANCO
        INTO vl_nro_cta_bancaria_retenedor
					  ,vl_cod_banco
			  FROM    origen_carga_banco ocb, cuenta_corriente cc
			  WHERE   ocb.cod_origen_carga_banco = 6 --tesoreria
			  AND     cc.cod_cuenta_corriente = ocb.cod_cuenta_corriente;

			  select nom_banco
        INTO vl_banco_cta_retenedor
			  from BANCO
			  where COD_BANCO = vl_cod_banco;

        vl_rut_deudor := LPAD(A.rut_deudor, 8, '0');
			  vc_ap_paterno_deudor := RPAD(A.ap_paterno_deudor, 55, ' ');
			  vc_ap_materno_deudor := RPAD(A.ap_materno_deudor, 36, ' ');
			  vc_nombres_deudor	  := RPAD(A.nombres_deudor, 36, ' ');
        vl_saldo_componente  := LPad(REPLACE(REPLACE(REPLACE(To_Char(A.saldo_componente,'9999999999D99'),' ',''), ',',''), '.', ''), 15, '0');

        vl_rut_retenedor := LPAD(A.rut_retenedor, 8, '0');
			  vl_ap_paterno_retenedor := RPAD(A.ap_paterno_retenedor, 55, ' ');
			  K_AP_MATERNO_RETENEDOR := RPAD(' ',36, ' ');
			  K_NOMBRES_RETENEDOR := RPAD(' ',36, ' ');
			  vl_nro_cta_bancaria_retenedor := RPAD(replace(vl_nro_cta_bancaria_retenedor,'-',''), 15, ' ');
			  vl_banco_cta_retenedor := RPAD(vl_banco_cta_retenedor, 36, ' ');
		
		vc_ap_paterno_deudor:=REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(vc_ap_paterno_deudor, 'á', 'a'), 'é','e'), 'í', 'i'), 'ó', 'o'), 'ú','u');
		vc_ap_materno_deudor:=REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(vc_ap_materno_deudor, 'á', 'a'), 'é','e'), 'í', 'i'), 'ó', 'o'), 'ú','u');
		vc_nombres_deudor	:=REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(vc_nombres_deudor, 'á', 'a'), 'é','e'), 'í', 'i'), 'ó', 'o'), 'ú','u');
			
		vc_ap_paterno_deudor:=REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(vc_ap_paterno_deudor, 'Á', 'A'), 'É','E'), 'Í', 'I'), 'Ó', 'O'), 'Ú','U');
		vc_ap_materno_deudor:=REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(vc_ap_materno_deudor, 'Á', 'A'), 'É','E'), 'Í', 'I'), 'Ó', 'O'), 'Ú','U');
		vc_nombres_deudor	:=REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(vc_nombres_deudor, 'Á', 'A'), 'É','E'), 'Í', 'I'), 'Ó', 'O'), 'Ú','U');
					  
		vc_ap_paterno_deudor:= REPLACE(REPLACE(vc_ap_paterno_deudor,'-',' '),'''',' ');
		vc_ap_materno_deudor:= REPLACE(REPLACE(vc_ap_materno_deudor,'-',' '),'''',' ');
		vc_nombres_deudor	:= REPLACE(REPLACE(vc_nombres_deudor,'-',' '),'''',' ');
		
		vc_ap_paterno_deudor:= REPLACE(vc_ap_paterno_deudor,'ñ','N');
		vc_ap_materno_deudor:= REPLACE(vc_ap_materno_deudor,'ñ','N');
		vc_nombres_deudor	:= REPLACE(vc_nombres_deudor,'ñ','N');

        vl_linea := K_TIPO_REGISTRO
							  ||vl_rut_deudor
							  ||A.dv_rut_deudor
							  ||vc_ap_paterno_deudor
							  ||vc_ap_materno_deudor
							  ||vc_nombres_deudor
							  ||K_FECHA_LIQUIDACION2
							  ||vl_saldo_componente
							  ||K_TIPO_MONEDA_UTM
							  ||vl_rut_retenedor
							  ||A.DV_RUT_RETENEDOR
							  ||vl_ap_paterno_retenedor
							  ||K_AP_MATERNO_RETENEDOR
							  ||K_NOMBRES_RETENEDOR
							  ||vl_marca_deuda
							  ||vl_rut_universidad
							  ||vl_dv_rut_universidad
							  ||vl_razon_universidad
							  ||vl_rut_universidad_conjuntas
							  ||vl_nro_cta_bancaria_retenedor
							  ||vl_banco_cta_retenedor
							  ||K_TIPO_CTA_RETENEDOR
							  ||K_origen
							  ||A.NRO_CUOTAS_MOROSAS;

        INSERT INTO TEMP_NOMINA_TGR (LINEA) VALUES(vl_linea);
        
        vl_cantidad_tipo2:= vl_cantidad_tipo2 + 1;
        vl_monto_utm := vl_monto_utm + A.saldo_componente;
        
        END IF; --if (A.saldo_componente > 0.10)
    END LOOP; --C_NOMINA_TGR
    
    vl_cantidad_tipo1 := RPAD('1',5, ' ');
	vl_cantidad_tipo2_string := RPAD(to_char(vl_cantidad_tipo2),5, ' '); 
	vl_cantidad_reg := RPAD(to_char(vl_cantidad_tipo2 + 2),5, ' ');
	
	vl_monto_utm_string  := LPad(REPLACE(REPLACE(To_Char(vl_monto_utm), ',',''), '.', ''), 17, '0');
	vl_monto_pesos_string :=  RPAD(' ',17, ' ');
    
    
    vl_linea_control := K_TIPO_REG_CONTROL
							  ||vl_cantidad_reg
							  ||vl_cantidad_tipo1
							  ||vl_cantidad_tipo2_string
							  ||vl_monto_utm_string
							  ||vl_monto_pesos_string
							  ||vl_monto_dolares
							  ||vl_origen_control
							  ||vl_filler_control;

        INSERT INTO TEMP_NOMINA_TGR (LINEA) VALUES(vl_linea_control);
      
      
   END;
END;
/