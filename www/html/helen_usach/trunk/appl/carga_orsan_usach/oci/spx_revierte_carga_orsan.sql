PROMPT CREATE OR REPLACE PROCEDURE spx_revierte_carga_orsan
CREATE OR REPLACE PROCEDURE spx_revierte_carga_orsan(ve_cod_carga_orsan_usach		IN  NUMBER  := NULL)

AS
BEGIN
  DECLARE
    vl_cod_plan_pago			NUMBER;
    vl_count_pp           NUMBER;

	--cursor moroso
	CURSOR 	C_DEUD IS
	  select ano_cuota
			,cod_deuda
      ,cod_comprobante
	  from item_carga_orsan_usach
	  where cod_carga_orsan_usach	= ve_cod_carga_orsan_usach;

	BEGIN

		FOR a IN C_DEUD LOOP

      select count(*)
				into vl_count_pp
				from plan_pago
				where cod_deuda = a.cod_deuda
				and ano_cobro = a.ano_cuota;

      IF(vl_count_pp > 0) then
			select cod_plan_pago
			into vl_cod_plan_pago
			from plan_pago
			where cod_deuda = a.cod_deuda
      AND ano_cobro = a.ano_cuota;

      END IF;

			sp_pp_actualiza_estado(vl_cod_plan_pago);
			sp_deuda_actualiza_estado(a.cod_deuda);

	  DELETE analitico_comprobante WHERE cod_comprobante = a.cod_comprobante;
      DELETE item_comprobante WHERE cod_comprobante = a.cod_comprobante;
      DELETE comprobante WHERE cod_comprobante = a.cod_comprobante;


		END LOOP;

    UPDATE  item_carga_orsan_usach SET cod_comprobante = NULL , cod_estado_carga = 1
    where cod_carga_orsan_usach	= ve_cod_carga_orsan_usach;

     DELETE abono WHERE fecha_registro > To_Date('06/04/2016', 'dd/mm/yyyy');
     DELETE excedente WHERE fecha_excedente > To_Date('06/04/2016', 'dd/mm/yyyy');
     
     update  carga_orsan_usach set  total_reg_carga = 0
									   ,total_reg_exitoso = 0
									   ,total_reg_pdte = 0
									   ,monto_reg_carga = 0
									   ,monto_reg_exitoso = 0
									   ,monto_reg_pdte = 0
		where cod_carga_orsan_usach = ve_cod_carga_orsan_usach;


	END;
END;
/

