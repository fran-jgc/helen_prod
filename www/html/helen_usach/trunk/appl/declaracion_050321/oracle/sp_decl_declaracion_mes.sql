CREATE OR REPLACE PROCEDURE sp_decl_declaracion_mes (
   ve_operacion               IN   VARCHAR2
 , ve_cod_declaracion         IN   declaracion.cod_declaracion%TYPE
 , ve_cod_declaracion_mes1    IN   declaracion_mes.cod_declaracion_mes%TYPE
 , ve_mes1                    IN   declaracion_mes.mes%TYPE
 , ve_monto_deudor_moneda1    IN   declaracion_mes.monto_deudor_moneda%TYPE
 , ve_monto_deudor_pesos1     IN   declaracion_mes.monto_deudor_pesos%TYPE
 , ve_monto_conyuge_moneda1   IN   declaracion_mes.monto_conyuge_moneda%TYPE
 , ve_monto_conyuge_pesos1    IN   declaracion_mes.monto_conyuge_pesos%TYPE
 , ve_cod_declaracion_mes2    IN   declaracion_mes.cod_declaracion_mes%TYPE
 , ve_mes2                    IN   declaracion_mes.mes%TYPE
 , ve_monto_deudor_moneda2    IN   declaracion_mes.monto_deudor_moneda%TYPE
 , ve_monto_deudor_pesos2     IN   declaracion_mes.monto_deudor_pesos%TYPE
 , ve_monto_conyuge_moneda2   IN   declaracion_mes.monto_conyuge_moneda%TYPE
 , ve_monto_conyuge_pesos2    IN   declaracion_mes.monto_conyuge_pesos%TYPE
)
AS
/******************************************************************************
   NAME:       sp_decl_declaracion_mes
   PURPOSE:    Ingresa y Modifica los detalles de las declaraciones del mes
   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        05/12/2009   Sergio Castro


******************************************************************************/
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO declaracion_mes
                  (cod_declaracion_mes
                 , cod_declaracion
                 , mes
                 , monto_deudor_moneda
                 , monto_deudor_pesos
                 , monto_conyuge_moneda
                 , monto_conyuge_pesos
                  )
           VALUES (declaracion_mes_sq.NEXTVAL
                 , ve_cod_declaracion
                 , ve_mes1
                 , ve_monto_deudor_moneda1
                 , ve_monto_deudor_pesos1
                 , ve_monto_conyuge_moneda1
                 , ve_monto_conyuge_pesos1
                  );

      INSERT INTO declaracion_mes
                  (cod_declaracion_mes
                 , cod_declaracion
                 , mes
                 , monto_deudor_moneda
                 , monto_deudor_pesos
                 , monto_conyuge_moneda
                 , monto_conyuge_pesos
                  )
           VALUES (declaracion_mes_sq.NEXTVAL
                 , ve_cod_declaracion
                 , ve_mes2
                 , ve_monto_deudor_moneda2
                 , ve_monto_deudor_pesos2
                 , ve_monto_conyuge_moneda2
                 , ve_monto_conyuge_pesos2
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE declaracion_mes
      SET cod_declaracion = ve_cod_declaracion
        , mes = ve_mes1
        , monto_deudor_moneda = ve_monto_deudor_moneda1
        , monto_deudor_pesos = ve_monto_deudor_pesos1
        , monto_conyuge_moneda = ve_monto_conyuge_moneda1
        , monto_conyuge_pesos = ve_monto_conyuge_pesos1
      WHERE  cod_declaracion_mes = ve_cod_declaracion_mes1;

      UPDATE declaracion_mes
      SET cod_declaracion = ve_cod_declaracion
        , mes = ve_mes2
        , monto_deudor_moneda = ve_monto_deudor_moneda2
        , monto_deudor_pesos = ve_monto_deudor_pesos2
        , monto_conyuge_moneda = ve_monto_conyuge_moneda2
        , monto_conyuge_pesos = ve_monto_conyuge_pesos2
      WHERE  cod_declaracion_mes = ve_cod_declaracion_mes2;
   END IF;
END;
/