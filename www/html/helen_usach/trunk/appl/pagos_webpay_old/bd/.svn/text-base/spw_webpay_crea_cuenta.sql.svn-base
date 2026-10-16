--------------------- spw_webpay_crea_cuenta ------------------------------
CREATE OR REPLACE PROCEDURE spw_webpay_crea_cuenta( ve_cod_deudor	IN	 number
										                   ,ve_password		IN   VARCHAR2
										                   ,ve_email			IN    VARCHAR)
										
AS
BEGIN
	DECLARE   valida					            VARCHAR2(1);
			      vl_cod_deudor				        NUMBER;
			      vl_password			          	varchar2(100);
			      vl_cod_webpay_crea_cuenta 	number;
			      vl_email				  	        varchar2(100);
			      vl_cod_comuna			         	number;
			      vl_cod_ciudad			        	number;
			      vl_cod_pais			          	number;
			      vl_telefono			          	varchar2(100);
			      vl_celular			            varchar2(100);
			      vl_ext_region				        varchar2(100);
			      vl_ext_ciudad			        	varchar2(100);
			      vl_ext_comuna				        varchar2(100);
			      vl_count				            number;
			      vl_direccion				        varchar2(100);
			      vl_cod_dir_mas_nueva	     	number;
			      K_USUARIO_WEB				        number;


	
	
  BEGIN 

  valida:='N';
  K_USUARIO_WEB:=2;

	    insert into webpay_crea_cuenta
            (cod_webpay_crea_cuenta
            ,cod_deudor
				    ,password
				    ,email
				    ,valida)
	    values(webpay_crea_cuenta_sq.NEXTVAL
          ,ve_cod_deudor
			    ,ve_password
			    ,ve_email
			    ,valida);

	
      SELECT webpay_crea_cuenta_sq.CURRVAL
	    INTO vl_cod_webpay_crea_cuenta
	    FROM dual;
	
      SELECT cod_deudor
            ,password
            ,email
      INTO  vl_cod_deudor
            ,vl_password
            ,vl_email
      from 	webpay_crea_cuenta
      where cod_webpay_crea_cuenta = vl_cod_webpay_crea_cuenta;

  

	    /*primero hace update a deudor en el campo password*/
	    update deudor
	    set password = vl_password
	    where cod_deudor = vl_cod_deudor;

	    -- buscar la direccion valida mas nueva
	    vl_cod_dir_mas_nueva:= f_get_direccion_deudor(vl_cod_deudor, 'P');
	
	    select	count(*)
      INTO vl_count
	    from	direccion
	    where	EMAIL = vl_email
	    and		COD_DIRECCION = vl_cod_dir_mas_nueva;

	    -- si es 0, se crea una nueva direccion
	    if(vl_count = 0) then
		    select direccion
				    ,cod_comuna
				    ,cod_ciudad
				    ,cod_pais
				    ,telefono
				    ,celular
				    ,ext_region
				    ,ext_ciudad
				    ,ext_comuna
        INTO vl_direccion
            ,vl_cod_comuna
            ,vl_cod_ciudad
            ,vl_cod_pais
            ,vl_telefono
            ,vl_celular
            ,vl_ext_region
            ,vl_ext_ciudad
            ,vl_ext_comuna 
		    from	direccion	
		    where	COD_DIRECCION = vl_cod_dir_mas_nueva;
 
		
		    spu_direccion('INSERT'			-- operacion
							    , null				-- cod_direccion
							    , vl_cod_deudor	-- cod_deudor       
							    , 'P'				-- cod_tipo_direccion
							    , vl_direccion     -- direccion
							    , vl_cod_comuna	-- cod_comuna
							    , vl_cod_ciudad    -- cod_ciudad
							    , vl_cod_pais      -- cod_pais
							    , vl_email         -- email
							    , vl_telefono      -- telefono
							    , vl_celular       -- celular
							    , 'S'				-- es_valida
							    , K_USUARIO_WEB	-- cod_usuario asume usuario_web
							    , vl_ext_region    -- ext_region	
							    , vl_ext_ciudad    -- ext_ciudad
							    , vl_ext_comuna);
	  END IF;
  END;
END;
/
