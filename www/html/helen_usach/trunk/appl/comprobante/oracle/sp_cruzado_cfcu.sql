PROMPT CREATE OR REPLACE PROCEDURE sp_cruzado_cfcu
CREATE OR REPLACE PROCEDURE sp_cruzado_cfcu (
   ve_cod_comprobante   NUMBER
)
AS

BEGIN
  DECLARE vl_count              NUMBER;
        vl_porc_cf              NUMBER;
        vl_monto_debe_cf        NUMBER;
        vl_monto_debe_cu        NUMBER;
        vl_monto_haber_cf       NUMBER;
        vl_monto_haber_cu       NUMBER;
        vl_cod_cuenta_contable_cf   NUMBER;
        vl_cod_cuenta_contable_cu   NUMBER;
        vl_cod_analitico_cf     NUMBER;
        vl_cod_analitico_cu     NUMBER;
        vl_procesado            VARCHAR2(1);

      CURSOR C_ANALITICO IS
	    SELECT cod_analitico_comprobante
		    ,ac.cod_deuda
        ,cod_cuenta_contable
		    ,monto_debe
		    ,monto_haber
        ,monto_debe_ORIGINAL
	    FROM analitico_comprobante ac, deuda d
	    WHERE cod_comprobante = ve_cod_comprobante
          AND ac.cod_deuda = d.cod_deuda
          AND cod_tipo_deuda = 'CFCU'  --para asegurarnos que no se procesen deudas distintas a CFCU en caso que la carga de cruzados_cfcu estuviese mala
          AND monto_debe_original IS NULL  --para no considerar registros que se estan reconstruyendo
          AND monto_haber_original IS NULL --para no considerar registros que se estan reconstruyendo
	    ORDER BY cod_analitico_comprobante;

      CURSOR C_CONCEPTO IS
	    SELECT cc.cod_concepto_cuenta
          ,f_comp_get_cuenta_tipo_deuda('CFCU', cc.cod_concepto_cuenta) cod_cuenta_contable_cfcu
	    FROM concepto_cuenta cc, cuenta_tipo_deuda ct
	    WHERE cc.cod_concepto_cuenta = ct.cod_concepto_cuenta
          AND cod_tipo_deuda = 'CFCU'
	    ORDER BY cod_concepto_cuenta;

    BEGIN

      vl_procesado:= 'N';
      FOR a IN C_ANALITICO LOOP

        --tiene deudas en cruzado_cfcu
		    SELECT count(*)
        INTO vl_count
		    FROM cruzado_cfcu
		    WHERE cod_deuda = a.cod_deuda;

        IF(vl_count > 0) THEN

          --obtiene los porcentajes si es cruzado CFCU
          SELECT porc_cf
          INTO vl_porc_cf
		      FROM cruzado_cfcu
		      WHERE cod_deuda = a.cod_deuda;

          --recorrer todos los registros de  CONCEPTO_CUENTA, y para deuda CFCU hasta encontrar la cod_cuenta_contable
          --se supone que los conceptos tendrán cuentas distintas y no se repetiran, validar en la carga de cuenta_tipo_deuda
          vl_monto_debe_cf:= 0;
          vl_monto_debe_cu:=0;
          vl_monto_haber_cf:= 0;
          vl_monto_haber_cu:= 0;

          FOR b IN C_CONCEPTO LOOP

            IF(b.cod_cuenta_contable_cfcu = a.cod_cuenta_contable) THEN

              --marca los analiticos que se eliminaran, ya que se volverán a reconstruir
              UPDATE analitico_comprobante
              SET monto_debe_original = -111111
                  ,monto_haber_original = -111111
              WHERE cod_analitico_comprobante = a.cod_analitico_comprobante;

              vl_monto_debe_cf:= Round(a.monto_debe * (vl_porc_cf / 100), 0);
              vl_monto_debe_cu := a.monto_debe - vl_monto_debe_cf;

              vl_monto_haber_cf:= Round(a.monto_haber * (vl_porc_cf / 100), 0);
              vl_monto_haber_cu := a.monto_haber - vl_monto_haber_cf;

              --obtiene cuenta CF
              SELECT f_comp_get_cuenta_tipo_deuda('CF', b.cod_concepto_cuenta)
              INTO vl_cod_cuenta_contable_cf
              FROM dual;

              --obtiene cuenta CU
              SELECT f_comp_get_cuenta_tipo_deuda('CU', b.cod_concepto_cuenta)
              INTO vl_cod_cuenta_contable_cu
              FROM dual;

              --crea analitico CF
              spu_analitico ('INSERT', NULL, ve_cod_comprobante, a.cod_deuda, vl_cod_cuenta_contable_cf, vl_monto_debe_cf, vl_monto_haber_cf);

              SELECT  analitico_comprobante_sq.CURRVAL
              INTO vl_cod_analitico_cf
              FROM dual;

              --deja marca en analitico creado para no agregar al cursor
              UPDATE analitico_comprobante
              SET monto_debe_original = -999999
                ,monto_haber_original = -999999
              WHERE cod_analitico_comprobante = vl_cod_analitico_cf;

              --crea analitico CU
              spu_analitico ('INSERT', NULL, ve_cod_comprobante, a.cod_deuda, vl_cod_cuenta_contable_cu, vl_monto_debe_cu, vl_monto_haber_cu);

              SELECT  analitico_comprobante_sq.CURRVAL
              INTO vl_cod_analitico_cu
              FROM dual;

              --deja marca en anlitico creado para no agregar al cursor
              UPDATE analitico_comprobante
              SET monto_debe_original = -999999
                ,monto_haber_original = -999999
              WHERE cod_analitico_comprobante = vl_cod_analitico_cu;

              vl_procesado:= 'S'; --marca que indica si se crearon analitico
              EXIT; --fuerza salida del loop
            END IF;
          END LOOP;
        END IF;
      END LOOP;

      -- solo si se proceso, se corre el siguiente script
      IF (vl_procesado = 'S') THEN
        --borra analiticos que fueron reconstruidos
        DELETE analitico_comprobante
        WHERE cod_comprobante = ve_cod_comprobante
          AND monto_debe_original = -111111
          AND monto_haber_original = -111111;

        --elimina marca de analiticos reconstruidos
        UPDATE analitico_comprobante
        SET monto_debe_original = NULL
          ,monto_haber_original = NULL
        WHERE cod_comprobante = ve_cod_comprobante
          AND monto_debe_original = -999999
          AND monto_haber_original = -999999;

        --borra todos los items comprobante
        DELETE item_comprobante
        WHERE cod_comprobante = ve_cod_comprobante;

        --reconstruye los items comprobante
        FOR a IN C_ANALITICO LOOP
          spu_item_comprobante ('INSERT', NULL, ve_cod_comprobante, a.cod_cuenta_contable, a.monto_debe, a.monto_haber);
        END LOOP;
     END IF;

    END;
END;
/

