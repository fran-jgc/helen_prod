PROMPT CREATE OR REPLACE PROCEDURE sp_susp_comprobante_cat
CREATE OR REPLACE PROCEDURE sp_susp_comprobante_cat(  ve_cod_comprobante          IN  NUMBER  := NULL
                                                  ,ve_fecha_comprobante       IN  DATE    := NULL
                                                  ,ve_cod_usuario             IN  NUMBER  := NULL
                                                  ,ve_cod_familia_deuda       IN  NUMBER  := NULL
                                                  ,ve_cod_origen_comprobante  IN  NUMBER  := NULL
                                                  ,ve_cod_tipo_deuda          IN  VARCHAR := NULL
                                                  ,ve_cod_plan_pago           IN  NUMBER  := NULL
                                                  ,ve_cod_deuda               IN  NUMBER  := NULL
                                                  ,ve_cod_suspension          IN  NUMBER  := NULL
)
AS
/******************************************************************************
   NAME:       sp_susp_comprobante

   REVISION:
    Creacion del comprobante contable de una Suspensión
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        30/03/2011   Iván Sánchez     Crea comprobante para Plan_Pago nuevo

   NOTAS:

******************************************************************************/
BEGIN
  DECLARE
    K_TIPO_CUENTA_CP        NUMBER := 2;
    K_TIPO_CUENTA_LP        NUMBER := 3;
    K_TIPO_CASTIGO          NUMBER := 4;
    K_TIPO_UTILIDAD_NORMAL  NUMBER := 6;
    K_ESTADO_COMP_PENDIENTE NUMBER := 1;
    K_TIPO_COMP_TRASPASO    NUMBER := 3;
    K_MONEDA_UTM            NUMBER := 3;
    vl_cuenta_cp_cs         NUMBER;
    vl_cuenta_lp_cs         NUMBER;
    vl_cuenta_castigo       NUMBER;
    vl_cuenta_util_normal   NUMBER;
    vl_nom_comprobante      VARCHAR(60);
    vl_cod_comprobante      NUMBER;
    vl_monto_K              NUMBER;
    vl_count                NUMBER;
    vl_ano_cobro            NUMBER;

  BEGIN
    vl_cuenta_cp_cs := f_comp_get_cuenta_tipo_deuda (ve_cod_tipo_deuda, K_TIPO_CUENTA_CP);
    vl_cuenta_lp_cs := f_comp_get_cuenta_tipo_deuda (ve_cod_tipo_deuda, K_TIPO_CUENTA_LP);
    vl_cuenta_castigo := f_comp_get_cuenta_tipo_deuda (ve_cod_tipo_deuda, K_TIPO_CASTIGO);
    vl_cuenta_util_normal := f_comp_get_cuenta_tipo_deuda (ve_cod_tipo_deuda, K_TIPO_UTILIDAD_NORMAL);

    SELECT  'Comprobante de Suspensión ' || CAST (ve_cod_suspension AS VARCHAR2 (10)) || ', Deuda ' || CAST (ve_cod_deuda AS VARCHAR2 (10))
    INTO    vl_nom_comprobante
    FROM    DUAL;

    --1. INGRESAR EN COMPROBANTE_CONTABLE  c/ SP ya echo
    spu_comprobante(  'INSERT'
                      ,NULL
                      ,ve_fecha_comprobante
                      ,ve_cod_usuario
                      ,vl_nom_comprobante
                      ,ve_cod_familia_deuda
                      ,K_ESTADO_COMP_PENDIENTE
                      ,K_TIPO_COMP_TRASPASO
                      ,ve_cod_origen_comprobante
                      ,NULL
                      ,NULL);
    SELECT  comprobante_sq.CURRVAL
    INTO    vl_cod_comprobante
    FROM    dual;

    SELECT ano_cobro
    INTO vl_ano_cobro
    FROM plan_pago
    WHERE cod_plan_pago = ve_cod_plan_pago;


    -- saldos de la cuota
    vl_monto_K  := Round(f_deuda_cat(ve_cod_deuda, vl_ano_cobro) *  f_mon_get_valor_moneda(K_MONEDA_UTM), 0);

    -- año actual
    spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_cp_cs, 0, vl_monto_K);
    spu_item_comprobante ('INSERT', NULL, vl_cod_comprobante, vl_cuenta_lp_cs, vl_monto_K, 0);

    spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_cp_cs, 0, vl_monto_K);
    spu_analitico ('INSERT', NULL, vl_cod_comprobante, ve_cod_deuda, vl_cuenta_lp_cs, vl_monto_K, 0);

    /* Este codigo se usa para los tipo_deuda = 'CS',
    ya que el comprobante puede ser 0*/
    SELECT Count(*)
    INTO vl_count
    FROM comprobante
    WHERE cod_comprobante = vl_cod_comprobante;

    IF (vl_count <> 0)THEN
      UPDATE suspension
      SET cod_comprobante = vl_cod_comprobante
      WHERE  cod_suspension = ve_cod_suspension;
    END IF;

  END;
END sp_susp_comprobante_cat;
/

