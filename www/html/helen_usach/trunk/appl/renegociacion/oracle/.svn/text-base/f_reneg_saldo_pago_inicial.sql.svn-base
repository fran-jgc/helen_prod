PROMPT CREATE OR REPLACE FUNCTION f_reneg_saldo_pago_inicial
CREATE OR REPLACE FUNCTION f_reneg_saldo_pago_inicial(ve_cod_renegociacion number)
RETURN NUMBER
AS
BEGIN
  DECLARE vl_pago_inicial		NUMBER;
		    vl_sum_pago_inicial	NUMBER;
		    vc_cod_ingreso		  NUMBER;
		    vl_doc_al_dia			  NUMBER;
		    vl_doc_a_fecha		  NUMBER;

    CURSOR C_INGRESO IS
	  select i.cod_ingreso
	  from pago_inicial_renegociacion p, ingreso i
	  where p.cod_renegociacion = ve_cod_renegociacion
	    and i.cod_ingreso = p.cod_ingreso
	    and i.cod_estado_ingreso in (1, 3);	-- emitido, cerrado
		
  BEGIN

	  select pago_inicial
    INTO vl_pago_inicial
	  from renegociacion
	  where cod_renegociacion = ve_cod_renegociacion;

	  vl_sum_pago_inicial := 0;

    FOR i IN C_INGRESO LOOP
		  vl_doc_al_dia := f_ingreso_doc_al_dia(vc_cod_ingreso);
		  vl_doc_a_fecha := f_ingreso_doc_a_fecha(vc_cod_ingreso);
		  vl_sum_pago_inicial := vl_sum_pago_inicial + vl_doc_al_dia + vl_doc_a_fecha;

    END LOOP;
                                                                                
	  return vl_pago_inicial -  vl_sum_pago_inicial;
  END;
END;