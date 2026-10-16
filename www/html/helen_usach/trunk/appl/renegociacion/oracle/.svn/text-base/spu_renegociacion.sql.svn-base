CREATE OR REPLACE PROCEDURE spu_renegociacion(
   ve_operacion                 IN       VARCHAR2
 , ve_cod_renegociacion         IN       NUMBER
 , ve_fecha_renegociacion       IN       DATE     := NULL
 , ve_cod_usuario               IN       NUMBER   := NULL
 , ve_cod_estado_renegociacion  IN       NUMBER   := NULL
 , ve_obs                       IN       VARCHAR2 := NULL
 , ve_fecha_carta1              IN       DATE     := NULL
 , ve_fecha_carta2              IN       DATE     := NULL
 , ve_fecha_carta3              IN       DATE     := NULL
 , ve_cod_deuda                 IN       NUMBER   := NULL
 , ve_autorizado				IN       VARCHAR2 := 'N'
)
AS
BEGIN
  DECLARE
    K_ESTADO_RENEG_EMITIDA    NUMBER := 1;
    K_ESTADO_RENEG_CONFIRMADA NUMBER := 2;
    vl_estado_anterior NUMBER;
  BEGIN
    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO renegociacion
                  ( cod_renegociacion
                  ,fecha_renegociacion
                  ,cod_usuario
                  ,cod_estado_renegociacion
                  ,obs
                  ,fecha_carta1
                  ,fecha_carta2
                  ,fecha_carta3
                  ,cod_deuda
                  ,autorizado
                  )
      VALUES      (renegociacion_sq.NEXTVAL
                  ,SYSDATE
                  ,ve_cod_usuario
                  ,ve_cod_estado_renegociacion
                  ,ve_obs
                  ,ve_fecha_carta1
                  ,ve_fecha_carta2
                  ,ve_fecha_carta3
                  ,ve_cod_deuda
                  ,ve_autorizado);
    ELSIF (ve_operacion = 'UPDATE') THEN
      SELECT  cod_estado_renegociacion
      INTO    vl_estado_anterior
      FROM    renegociacion
      WHERE   cod_renegociacion = ve_cod_renegociacion;
      
      IF (vl_estado_anterior = K_ESTADO_RENEG_EMITIDA AND ve_cod_estado_renegociacion = K_ESTADO_RENEG_CONFIRMADA) THEN
        sp_reneg_confirma(ve_cod_renegociacion);
      END IF;
      UPDATE  renegociacion
      SET     cod_estado_renegociacion   = ve_cod_estado_renegociacion
              ,obs                        = ve_obs
              ,fecha_carta1               = ve_fecha_carta1
              ,fecha_carta2               = ve_fecha_carta2
              ,fecha_carta3               = ve_fecha_carta3
              ,cod_deuda                  = ve_cod_deuda
              ,autorizado 				  = ve_autorizado
      WHERE   cod_renegociacion          = ve_cod_renegociacion;
    /*ELSIF (ve_operacion = 'DELETE') THEN
      DELETE rendicion
      WHERE cod_rendicion = ve_cod_rendicion;
    */
    END IF;
  END;
END spu_renegociacion;