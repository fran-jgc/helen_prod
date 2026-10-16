CREATE OR REPLACE PROCEDURE spi_deudores_sin_declaracion(ve_ano      NUMBER
														,ve_cod_familia_deuda NUMBER)
AS
BEGIN
  DECLARE
    K_DEUDA_EXIGIBLE      NUMBER:=2;
	  K_PP_SUSPENDIDA     NUMBER:=6;		
	vl_telefono           VARCHAR2(100);
    vl_email              VARCHAR2(100);
    vl_celular            VARCHAR2(100);
    vl_cant_deudor        NUMBER;
    vl_cod_direccion      NUMBER;
    vl_count              NUMBER;
    vc_cod_deudor		  NUMBER;
	vc_rut				  VARCHAR2(20);
	vc_nombre			  VARCHAR2(100);
	vc_apellido_paterno	  VARCHAR2(100);
	vc_apellido_materno	  VARCHAR2(100);
	vc_cod_deuda		  NUMBER;
	vc_nom_cartera		  VARCHAR2(100);
	vl_direccion		  VARCHAR2(100);
	vl_comuna			  VARCHAR2(100);
	vl_ciudad		      VARCHAR2(100);
	vl_saldo_99			  NUMBER;
	vl_cuota_suspendida	  NUMBER;
	vl_cruzado_interno	  NUMBER;
	vl_cruzado_externo	  NUMBER;
	vl_glosa			  VARCHAR2(50);
			
	CURSOR c_deudor IS
    SELECT de.cod_deudor
          ,REPLACE(To_Char(de.rut, '99,999,999'), ',', '.') || '-' || de.dig_verif rut
          ,de.nombre
          ,de.apellido_paterno
          ,de.apellido_materno
          ,d.cod_deuda
          ,C.NOM_CARTERA
    FROM deuda d, deudor de, cartera c, TIPO_DEUDA TD
    WHERE d.cod_tipo_deuda ='CS'
      AND f_deuda_get_estado(d.cod_deuda, To_Date('01/06/' || To_Char(ve_ano),'dd/mm/yyyy')) = K_DEUDA_EXIGIBLE
      AND de.cod_deudor = d.cod_deudor
      and C.COD_CARTERA = d.COD_CARTERA
      AND D.COD_TIPO_DEUDA = TD.COD_TIPO_DEUDA
      AND TD.COD_FAMILIA_DEUDA = ve_cod_familia_deuda
      AND d.cod_deudor NOT IN ( SELECT dcl.cod_deudor
                                FROM declaracion dcl
                                WHERE dcl.ano = ve_ano)
    ORDER BY de.rut;

  BEGIN
    FOR a IN c_deudor LOOP
      SELECT Count(*)
      INTO vl_count
      FROM TEMP_DEUDORES_SIN_DECLARACION
      WHERE RUT = a.rut;

			  
      IF (vl_count = 0 ) THEN
        vl_cod_direccion := f_get_direccion_deudor(a.cod_deudor, 'P');
        IF (vl_cod_direccion IS NULL) THEN
          vl_direccion := '';
          vl_comuna := '';
          vl_ciudad := '';
          vl_telefono := '';
          vl_email := '';
          vl_celular := '';
        ELSE
          SELECT D.DIRECCION
                ,CO.NOM_COMUNA
                ,CI.NOM_CIUDAD
                ,D.TELEFONO
      		    ,D.EMAIL
                ,D.CELULAR
          INTO  vl_direccion
                ,vl_comuna
                ,vl_ciudad
                ,vl_telefono
                ,vl_email
                ,vl_celular
  		    FROM 	DIRECCION D left outer join COMUNA CO ON CO.COD_COMUNA = D.COD_COMUNA
										        left outer join CIUDAD CI ON CI.COD_CIUDAD = D.COD_CIUDAD
 			    WHERE D.COD_DIRECCION = vl_cod_direccion;
        END IF;

        INSERT INTO TEMP_DEUDORES_SIN_DECLARACION
          (RUT
          ,NOMBRE
          ,APELLIDO_PATERNO
          ,APELLIDO_MATERNO
          ,DIRECCION
          ,COMUNA
          ,CIUDAD
          ,TELEFONO
          ,EMAIL
          ,CELULAR
          ,NOM_CRUZADO
          ,NOM_CARTERA)
        VALUES
          (a.rut
          ,a.nombre
          ,a.apellido_paterno
          ,a.apellido_materno
          ,vl_direccion
          ,vl_comuna
          ,vl_ciudad
          ,vl_telefono
          ,vl_email
          ,vl_celular
          ,vl_glosa
          ,a.nom_cartera);
      END IF;
    END LOOP;
  END;
END spi_deudores_sin_declaracion;
/
