CREATE OR REPLACE PROCEDURE spu_novacion (
   ve_operacion               IN   VARCHAR
 , ve_cod_novacion            IN   NUMBER := NULL
 , ve_cod_usuario             IN   NUMBER := NULL
 , ve_cod_deuda_antigua       IN   NUMBER := NULL
 , ve_monto_svp               IN   NUMBER := NULL
 , ve_monto_mora              IN   NUMBER := NULL
 , ve_monto_cp                IN   NUMBER := NULL
 , ve_monto_lp                IN   NUMBER := NULL
 , ve_obs                     IN   VARCHAR := NULL
 , ve_cod_estado_novacion     IN   NUMBER := NULL
 , ve_cod_usuario_confirma    IN   NUMBER := NULL
 , ve_nro_anos                IN   NUMBER := NULL
)

AS
/******************************************************************************
   NAME:       spu_novacion

   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        04/12/2009   Cristian Sánchez       crea y modifica una novacion

   NOTAS:
   CONDICIONES DEL UPDATE: CUANDO CAMBIAN LOS ESTADOS DE LA SUSPENSION

         solicitada (1) ->  aprobada (2)
                debe crear un comprobante y una nueva deuda


******************************************************************************/
BEGIN
  DECLARE
      vl_cod_estado_novacion  NUMBER;
      K_NOV_SOLICITADA        NUMBER:=1;
      K_NOV_APROBADA          NUMBER:=2;
      K_NOVACION              NUMBER:=11;

      vl_cod_deuda_new        NUMBER;
      vl_cod_comprobante      NUMBER;
      vl_cod_deudor           NUMBER;

  BEGIN
	  IF (ve_operacion = 'INSERT') THEN
      --inserta en novacion
		  INSERT INTO novacion
					    (cod_novacion
              , fecha_novacion
              , cod_usuario
              , cod_deuda_antigua
              , monto_svp
              , monto_mora
              , monto_cp
              , monto_lp
              , obs
              , cod_estado_novacion
              , nro_anos
              )
			VALUES  (novacion_sq.NEXTVAL
              ,SYSDATE
					    ,ve_cod_usuario
              ,ve_cod_deuda_antigua
              ,ve_monto_svp
              ,ve_monto_mora
              ,ve_monto_cp
              ,ve_monto_lp
              ,ve_obs
              ,ve_cod_estado_novacion
              ,ve_nro_anos
					    );
	  ELSIF (ve_operacion = 'UPDATE') THEN
      SELECT  n.cod_estado_novacion
              ,d.cod_deudor
      INTO    vl_cod_estado_novacion
              ,vl_cod_deudor
      FROM    novacion n
              ,deuda d
      WHERE   n.cod_novacion = ve_cod_novacion
              AND d.cod_deuda = n.cod_deuda_antigua;

      UPDATE  novacion
		  SET     cod_deuda_antigua       = ve_cod_deuda_antigua
              ,monto_svp              = ve_monto_svp
              ,monto_mora             = ve_monto_mora
              ,monto_cp               = ve_monto_cp
              ,monto_lp               = ve_monto_lp
              ,obs                    = ve_obs
              ,cod_estado_novacion    = ve_cod_estado_novacion
              ,nro_anos               = ve_nro_anos
		 	WHERE   cod_novacion = ve_cod_novacion;


      IF (vl_cod_estado_novacion = K_NOV_SOLICITADA AND ve_cod_estado_novacion = K_NOV_APROBADA) THEN
        sp_novacion (ve_cod_novacion, ve_cod_usuario_confirma, vl_cod_deuda_new, vl_cod_comprobante);

        UPDATE  novacion
		   	SET     cod_novacion          = ve_cod_novacion,
                cod_usuario_confirma  = ve_cod_usuario_confirma,
                fecha_confirma        = SYSDATE,
                cod_deuda_nueva       = vl_cod_deuda_new,
                cod_comprobante       = vl_cod_comprobante,
                nro_anos              = ve_nro_anos
        WHERE   cod_novacion          = ve_cod_novacion;

        spu_bitacora_deudor ('Novación deuda: ' || To_Char(ve_cod_deuda_antigua) || ' nueva deuda: ' || to_char(vl_cod_deuda_new), K_NOVACION, vl_cod_deudor, ve_cod_usuario_confirma);

      END IF;
    END IF;
  END;
END;