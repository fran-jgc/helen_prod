PROMPT CREATE OR REPLACE PROCEDURE spu_direccion
CREATE OR REPLACE PROCEDURE spu_direccion (
   ve_operacion        IN   VARCHAR
 , ve_cod_direccion    IN   VARCHAR
 , ve_cod_deudor       IN   NUMBER
 , ve_tipo_direccion   IN   VARCHAR
 , ve_direccion        IN   VARCHAR
 , ve_cod_comuna       IN   NUMBER
 , ve_cod_ciudad       IN   NUMBER
 , ve_cod_pais         IN   NUMBER
 , ve_email            IN   VARCHAR
 , ve_telefono         IN   VARCHAR
 , ve_celular          IN   VARCHAR
 , ve_es_valida        IN   VARCHAR
 , ve_cod_usuario      IN   NUMBER
 , ve_ext_region       IN   VARCHAR
 , ve_ext_ciudad       IN   VARCHAR
 , ve_ext_comuna       IN   VARCHAR
)
AS
/****************************************************************************************************
   NAME:       spu_direccion

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        01/10/2009   Sergio Castro   Inserta nueva Direccion de un deudor
   2.0        18/12/2009   Sergio Castro   Se Modifica para que tambien pueda actualizar si es vigente

   NOTAS:


*****************************************************************************************************/
BEGIN
   DECLARE
      kl_modifica_direccion                   NUMBER := 2;
      vl_glosa                                VARCHAR2 (100);
      vl_cod_direccion         				  NUMBER;
	  vl_direccion			  				  VARCHAR2(100);
   BEGIN
      IF (ve_operacion = 'INSERT')
      THEN
      
      
      	select  MAX(COD_DIRECCION)
      	into vl_cod_direccion
		from DIRECCION 
		WHERE COD_DEUDOR = ve_cod_deudor
		and es_valida = 'S';
		
		select DIRECCION
		into vl_direccion
		from DIRECCION
		where COD_DIRECCION = vl_cod_direccion;
      
      
         INSERT INTO direccion
                     (cod_direccion
                    , cod_deudor
                    , fecha_direccion
                    , tipo_direccion
                    , direccion
                    , cod_comuna
                    , cod_ciudad
                    , cod_pais
                    , email
                    , telefono
                    , celular
                    , es_valida
                    , ext_region
				 	, ext_ciudad
				 	, ext_comuna)
              VALUES (direccion_sq.NEXTVAL
                    , ve_cod_deudor
                    , SYSDATE 
                    , ve_tipo_direccion
                    , ve_direccion
                    , ve_cod_comuna
                    , ve_cod_ciudad
                    , ve_cod_pais
                    , ve_email
                    , ve_telefono
                    , ve_celular
                    , ve_es_valida
                    , ve_ext_region
					, ve_ext_ciudad
					, ve_ext_comuna);
      ELSE
         UPDATE direccion
         SET es_valida = 'N'
         WHERE  cod_direccion = ve_cod_direccion;
      END IF;

      IF (ve_tipo_direccion = 'P')
      THEN
         vl_glosa                             := 'Modificación Dirección Particular';
      ELSE
         vl_glosa                             := 'Modificación Dirección Comercial';
      END IF;

      if(vl_direccion <> ve_direccion) then
		spu_bitacora_deudor (vl_glosa, kl_modifica_direccion, ve_cod_deudor, ve_cod_usuario);
	  END if;
      
    END;
END;
/



