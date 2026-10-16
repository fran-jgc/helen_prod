PROMPT CREATE OR REPLACE PROCEDURE spu_carga_pagare
CREATE OR REPLACE PROCEDURE spu_carga_pagare( ve_operacion            IN  VARCHAR
                                              ,ve_rut                 IN   NUMBER:= NULL
                                              ,ve_dig_verif           IN   VARCHAR2:= NULL
                                              ,ve_nombre              IN   VARCHAR2:= NULL
                                              ,ve_apellido_paterno    IN   VARCHAR2:= NULL
                                              ,ve_apellido_materno    IN   VARCHAR2:= NULL
                                              ,ve_sexo                IN   VARCHAR2:= NULL
                                              ,ve_cod_estado_civil    IN   NUMBER:= NULL
                                              ,ve_fecha_nacim         IN   DATE:= NULL
                                              ,ve_direccion           IN   VARCHAR2:= NULL
                                              ,ve_cod_comuna          IN   NUMBER:= NULL
                                              ,ve_cod_ciudad          IN   NUMBER:= NULL
                                              ,ve_cod_pais            IN   NUMBER:= NULL
                                              ,ve_cod_tipo_deuda      IN   VARCHAR2:= NULL
                                              ,ve_email               IN   VARCHAR2:= NULL
                                              ,ve_telefono            IN   VARCHAR2:= NULL
                                              ,ve_celular             IN   VARCHAR2:= NULL
                                              ,ve_folio_pagare        IN   VARCHAR2:= NULL
                                              ,ve_fecha_pagare        IN   DATE:= NULL
                                              ,ve_monto_moneda        IN   NUMBER:= NULL
                                              ,ve_monto_pesos         IN   NUMBER:= NULL
                                              ,ve_cargado             IN   VARCHAR2:= NULL
                                              ,ve_fecha_carga         IN   DATE:=NULL
                                            )
AS
/******************************************************************************
   NAME:       SPU_CARGA_PAGARE

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        03/02/2010   SC              Realiza la Carga de los Pagares

   NOTES:

******************************************************************************/
BEGIN
  IF (ve_operacion = 'BORRA_CARGA_PAGARE') THEN
    DELETE CARGA_PAGARE;
  ELSIF (ve_operacion = 'INSERT') THEN
    INSERT INTO CARGA_PAGARE( COD_CARGA_PAGARE
                              ,RUT
                              ,DIG_VERIF
                              ,NOMBRE
                              ,APELLIDO_PATERNO
                              ,APELLIDO_MATERNO
                              ,SEXO
                              ,COD_ESTADO_CIVIL
                              ,FECHA_NACIM
                              ,DIRECCION
                              ,COD_COMUNA
                              ,COD_CIUDAD
                              ,COD_PAIS
                              ,COD_TIPO_DEUDA
                              ,EMAIL
                              ,TELEFONO
                              ,CELULAR
                              ,FOLIO_PAGARE
                              ,FECHA_PAGARE
                              ,MONTO_MONEDA
                              ,MONTO_PESOS
                              ,CARGADO
                              ,FECHA_CARGA)
    VALUES                  ( carga_pagare_sq.NEXTVAL
                              ,ve_rut
                              ,ve_dig_verif
                              ,ve_nombre
                              ,ve_apellido_paterno
                              ,ve_apellido_materno
                              ,ve_sexo
                              ,ve_cod_estado_civil
                              ,ve_fecha_nacim
                              ,ve_direccion
                              ,ve_cod_comuna
                              ,ve_cod_ciudad
                              ,ve_cod_pais
                              ,ve_cod_tipo_deuda
                              ,ve_email
                              ,ve_telefono
                              ,ve_celular
                              ,ve_folio_pagare
                              ,ve_fecha_pagare
                              ,ve_monto_moneda
                              ,ve_monto_pesos
                              ,ve_cargado
                              ,ve_fecha_carga
                            );
  END IF;
END;
/

