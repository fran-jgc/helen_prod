CREATE OR REPLACE PROCEDURE sp_actualiza_item(ve_cod_comprobante NUMBER)
as
Begin
	DECLARE
     CURSOR C_CURSOR_DEBE IS
             SELECT COD_CUENTA_CONTABLE,
                    SUM(MONTO_DEBE) MONTO_DEBE,
                    Sum(MONTO_HABER) MONTO_HABER
             FROM ANALITICO_COMPROBANTE
             WHERE COD_COMPROBANTE = ve_cod_comprobante
             AND MONTO_HABER = 0
             GROUP BY COD_CUENTA_CONTABLE;
             
            
     CURSOR C_CURSOR_HABER IS
             SELECT COD_CUENTA_CONTABLE,
                    SUM(MONTO_DEBE) MONTO_DEBE,
                    Sum(MONTO_HABER) MONTO_HABER
             FROM ANALITICO_COMPROBANTE
             WHERE COD_COMPROBANTE = ve_cod_comprobante
             AND MONTO_DEBE = 0
             GROUP BY COD_CUENTA_CONTABLE;
    BEGIN
    DELETE item_comprobante WHERE cod_comprobante =  ve_cod_comprobante;
    FOR a IN C_CURSOR_DEBE LOOP
                INSERT INTO ITEM_COMPROBANTE 
                VALUES
                (
                item_comprobante_sq.NEXTVAL,
                ve_cod_comprobante,
                a.cod_cuenta_contable,
                a.monto_debe,
                a.monto_haber
                );                 
    END LOOP;
    
    FOR b IN C_CURSOR_HABER LOOP
                INSERT INTO ITEM_COMPROBANTE 
                VALUES
                (
                item_comprobante_sq.NEXTVAL,
                ve_cod_comprobante,
                b.cod_cuenta_contable,
                b.monto_debe,
                b.monto_haber
                );                 
    END LOOP;
    COMMIT;
END;
END sp_actualiza_item;
/
