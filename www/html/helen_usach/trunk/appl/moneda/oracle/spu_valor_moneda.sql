CREATE OR REPLACE PROCEDURE spu_valor_moneda(ve_cod_moneda IN NUMBER, ve_dia IN NUMBER, ve_mes IN NUMBER, ve_ano IN NUMBER, ve_valor IN NUMBER)
AS
  fecha       DATE;
	ultimo_dia  NUMBER;
	cont		    NUMBER;
  BEGIN

    ultimo_dia := f_ultimo_dia(ve_mes,ve_ano);

    IF(ve_dia <= ultimo_dia) THEN
      fecha := To_Date(To_Char(ve_dia, '00')||'/'||To_Char(ve_mes, '00')||'/'||To_Char(ve_ano, '0000'), 'dd/mm/yyyy');
      IF(ve_valor = 0)THEN
        delete  valor_moneda
		    where   cod_moneda = ve_cod_moneda and
                fecha_valor_moneda = fecha;
      else
        begin
		      SELECT  count(*)
          INTO    cont
          from	  valor_moneda
		      where	  cod_moneda = ve_cod_moneda and
				          fecha_valor_moneda = fecha;
        END;
        IF(cont = 0) THEN
          insert into valor_moneda (cod_valor_moneda,fecha_valor_moneda,cod_moneda, monto_valor_moneda)
			    values (valor_moneda_sq.NEXTVAL, fecha,ve_cod_moneda,ve_valor);
		    ELSE
          BEGIN
            update  valor_moneda
			      set     monto_valor_moneda = ve_valor
			      where   cod_moneda = ve_cod_moneda and
				            fecha_valor_moneda = fecha;
        END;
      END IF;
    END IF;
    ELSE
      BEGIN
        RETURN;
      END;
    END if;
  END;
/