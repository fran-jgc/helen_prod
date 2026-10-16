PROMPT CREATE OR REPLACE PROCEDURE spu_excedente
CREATE OR REPLACE PROCEDURE spu_excedente(
  ve_operacion                IN      VARCHAR2
  ,ve_cod_excedente           IN      NUMBER   := NULL
  ,ve_fecha_excedente         IN      DATE     := NULL
  ,ve_cod_deudor              IN      NUMBER   := NULL
  ,ve_monto_excedente         IN      NUMBER
  ,ve_cod_estado_excedente    IN      NUMBER   := NULL
  ,ve_fecha_egreso            IN      DATE     := NULL
  ,ve_cod_usuario             IN      NUMBER   := NULL
  ,ve_cod_comprobante         IN      NUMBER   := NULL
  ,ve_cod_abono               IN      NUMBER   := NULL
  ,ve_cod_cuenta_corriente    IN      NUMBER   := NULL
  ,ve_nro_cheque			  IN      NUMBER   := NULL
 )
AS
BEGIN
  DECLARE
    vl_cod_estado_excedente_antes   NUMBER;
    vl_cod_familia_deuda            NUMBER;
    vl_rut                          VARCHAR2(10);
    K_EXCEDENTE_EMITIDO             NUMBER := 1;
    K_EXCEDENTE_CON_EGRESO          NUMBER := 2;
    K_CONCEPTO_CUENTA_EXCEDENTE     NUMBER := 19;
    K_EST_COMPROBANTE_PENDIENTE     NUMBER := 1;
    K_TIPO_COMPROBANTE_TRASPASO     NUMBER := 3;
    K_ORIG_COMP_EGRESO_EXCEDENTE    NUMBER := 9;
    vl_cta_banco                    NUMBER;
    vl_cta_excedente                NUMBER;
    vl_cod_comprobante              NUMBER;
    vl_cod_tipo_deuda               VARCHAR2(10);
    vl_cod_deuda                    NUMBER;
    vl_cod_cuenta                   NUMBER;
    vl_cod_deudor                   NUMBER;
    vl_cod_abono                    NUMBER;
    K_EXCEDENTE                     NUMBER := 15;
    K_EXCEDENTE_EGRESO              NUMBER := 16;
    vl_fecha_comprobante            VARCHAR2(100);
    vl_cod_documento_pago           NUMBER;
    vl_nom_banco                    VARCHAR2(100);
    vl_cod_banco                    NUMBER;

  BEGIN
    IF (ve_operacion = 'INSERT') THEN
      INSERT INTO excedente
                  (cod_excedente
                  ,fecha_excedente
                  ,cod_deudor
                  ,monto_excedente
                  ,cod_estado_excedente
                  ,fecha_egreso
                  ,cod_usuario
                  ,cod_comprobante
                  ,cod_abono
                  ,cod_cuenta_corriente
                  )
          VALUES ( excedente_sq.NEXTVAL
                  ,SYSDATE
                  ,ve_cod_deudor
                  ,ve_monto_excedente
                  ,ve_cod_estado_excedente
                  ,ve_fecha_egreso
                  ,ve_cod_usuario
                  ,ve_cod_comprobante
                  ,ve_cod_abono
                  ,ve_cod_cuenta_corriente
                  );
    END IF;
    IF (ve_operacion = 'UPDATE') THEN
      SELECT  e.cod_estado_excedente
              ,dr.rut||'-'||dr.dig_verif rut
              ,td.cod_familia_deuda
              ,td.cod_tipo_deuda
              ,a.cod_abono
      INTO    vl_cod_estado_excedente_antes
              ,vl_rut
              ,vl_cod_familia_deuda
              ,vl_cod_tipo_deuda
              ,vl_cod_abono
      FROM    excedente e
              ,deudor dr
              ,deuda d
              ,abono a
              ,tipo_deuda td
      WHERE   e.cod_excedente   = ve_cod_excedente
              AND dr.cod_deudor = ve_cod_deudor
              AND e.cod_abono   = a.cod_abono
              AND a.cod_deuda   = d.cod_deuda
              AND d.cod_tipo_deuda = td.cod_tipo_deuda;

      UPDATE  excedente
      SET     cod_estado_excedente = ve_cod_estado_excedente
              ,fecha_egreso         = ve_fecha_egreso
              ,cod_usuario          = ve_cod_usuario
              ,cod_cuenta_corriente = ve_cod_cuenta_corriente
              ,nro_cheque 			= ve_nro_cheque
      WHERE   cod_excedente = ve_cod_excedente;

      IF (vl_cod_estado_excedente_antes = K_EXCEDENTE_EMITIDO
          AND ve_cod_estado_excedente = K_EXCEDENTE_CON_EGRESO) THEN
        /********************************************/
        /*  Select para obtener la cuenta contable  */
        /*
        SELECT  cc.cod_cuenta_contable
        INTO    vl_cod_cuenta
        FROM    anula_abono aa,abono ab, deuda da, tipo_deuda td, cuenta_corriente cc
        WHERE   aa.cod_abono = ab.cod_abono
                AND ab.cod_deuda = da.cod_deuda
                AND da.cod_tipo_deuda = td.cod_tipo_deuda
                AND td.cod_familia_deuda = cc.cod_familia_deuda
                AND ROWNUM = 1;
        vl_cta_banco     := vl_cod_cuenta;
        */
        SELECT  cod_cuenta_contable
        INTO    vl_cta_banco
        FROM    excedente e
                ,cuenta_corriente cc
        WHERE   cc.cod_cuenta_corriente = e.cod_cuenta_corriente
                AND cod_excedente = ve_cod_excedente;
        /********************************************/
        vl_cta_excedente := f_comp_get_cuenta_tipo_deuda(vl_cod_tipo_deuda, K_CONCEPTO_CUENTA_EXCEDENTE);

        /*para la glosa, se rescata la fecha del comprobante original = 'INGRESO'*/
        SELECT  To_Char(fecha_comprobante,'dd/mm/yyyy')
                , cod_documento_pago
        INTO    vl_fecha_comprobante
                ,vl_cod_documento_pago
        FROM    abono a, comprobante c
        WHERE   cod_abono = vl_cod_abono
        AND     c.cod_comprobante = a.cod_comprobante;
        IF (vl_cod_documento_pago IS NOT NULL) THEN

          SELECT  cod_banco
          INTO    vl_cod_banco
          FROM    documento_pago
          WHERE   cod_documento_pago = vl_cod_documento_pago;

          IF(vl_cod_banco IS null) THEN
            vl_nom_banco := ' ';
          ELSE
            /*para la glosa, se rescata el banco del documento pago*/
            SELECT  nom_banco
            INTO    vl_nom_banco
            FROM    documento_pago dp, banco b
            WHERE   dp.cod_banco = b.cod_banco
            AND     cod_documento_pago = vl_cod_documento_pago;
          END IF;

        ELSE
          vl_nom_banco := ' ';

        END IF;


        spu_comprobante('INSERT'
                        , NULL
                        , SYSDATE
                        , ve_cod_usuario
                        , 'Egreso excedente '|| TO_CHAR(ve_cod_excedente) || ', del deudor '|| TO_CHAR(vl_rut)||', Fecha de pago '||vl_fecha_comprobante||', Entidad '||vl_nom_banco -- glosa
                        , vl_cod_familia_deuda
                        , K_EST_COMPROBANTE_PENDIENTE
                        , K_TIPO_COMPROBANTE_TRASPASO
                        , K_ORIG_COMP_EGRESO_EXCEDENTE
                        , NULL -- cod_usuario egresa
                        , NULL -- fecha egreso
                        );
        SELECT  comprobante_sq.CURRVAL
        INTO    vl_cod_comprobante
        FROM    dual;

        -- crear item comprobante
        spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cta_excedente, ve_monto_excedente, 0);
        spu_item_comprobante('INSERT', NULL, vl_cod_comprobante, vl_cta_banco, 0, ve_monto_excedente);

        SELECT  d.cod_deuda
                ,d.cod_deudor
        INTO    vl_cod_deuda
                ,vl_cod_deudor
        FROM    abono a
                ,excedente e
                ,deuda d
        WHERE   e.cod_abono = a.cod_abono
                AND a.cod_deuda = d.cod_deuda
                AND a.cod_abono = ve_cod_abono;

        -- crea analitico
        spu_analitico('INSERT',null, vl_cod_comprobante , vl_cod_deuda, vl_cta_banco, 0, ve_monto_excedente);
        spu_analitico('INSERT',null, vl_cod_comprobante , vl_cod_deuda, vl_cta_excedente, ve_monto_excedente,0);

        UPDATE  excedente
        SET     cod_comprobante = vl_cod_comprobante
        WHERE   cod_excedente = ve_cod_excedente;

        spu_bitacora_deudor ('Excedente Con Egreso Nº: ' || To_Char(ve_cod_excedente), K_EXCEDENTE_EGRESO, vl_cod_deudor, ve_cod_usuario);
      ELSE
        SELECT  d.cod_deudor
        INTO    vl_cod_deudor
        FROM    abono a
                ,deuda d
        WHERE   a.cod_deuda = d.cod_deuda
                AND a.cod_abono = ve_cod_abono;

        spu_bitacora_deudor ('Excedente Nº: ' || To_Char(ve_cod_excedente), K_EXCEDENTE, vl_cod_deudor, ve_cod_usuario);
      END IF;
    END IF;
  END;
END spu_excedente;
/

