CREATE OR REPLACE procedure spi_documentos_cartera(ve_fecha DATE 
												,ve_rut					NUMBER 
												,ve_cod_estado_doc_pago	NUMBER )
as
begin

	IF  (ve_rut is null)THEN 
		insert into TEMP_DOCUMENTOS_CATERA
				(RUT 
				,FECHA_DOC_PAGO
				,NOM_BANCO
				,NOMPROMPT CREATE OR REPLACE PROCEDURE spi_documentos_cartera
CREATE OR REPLACE procedure spi_documentos_cartera(ve_fecha DATE
												,ve_rut					NUMBER
												,ve_cod_estado_doc_pago	NUMBER )
as
begin

	IF  (ve_rut is null)THEN
		insert into TEMP_DOCUMENTOS_CATERA
				(RUT
				,FECHA_DOC_PAGO
				,NOM_BANCO
				,NOM_PLAZA
				,MONTO)
		select REPLACE(To_Char(dr.rut, '99,999,999'), ',', '.') || '-' || dr.dig_verif rut
				,To_Char(fecha_documento_pago) fecha_documento_pago
				,b.nom_banco
				,p.nom_plaza
				,dp.monto
		from	documento_pago dp left outer join banco b on dp.cod_banco = b.cod_banco
					left outer join plaza p on dp.cod_plaza = p.cod_plaza
				, deudor dr
		where	dp.cod_deudor = dr.cod_deudor
		and		dp.cod_estado_documento_pago = ve_cod_estado_doc_pago
		and		dp.fecha_documento_pago <= ve_fecha
		order by nom_banco, fecha_documento_pago ASC ;

	else
	begin
		insert into TEMP_DOCUMENTOS_CATERA
				(RUT
				,FECHA_DOC_PAGO
				,NOM_BANCO
				,NOM_PLAZA
				,MONTO)
		SELECT REPLACE(To_Char(dr.rut, '99,999,999'), ',', '.') || '-' || dr.dig_verif rut
				,To_Char(fecha_documento_pago) fecha_documento_pago
				,b.nom_banco
				,p.nom_plaza
				,dp.monto
		from	documento_pago dp left outer join banco b on dp.cod_banco = b.cod_banco
					left outer join plaza p on dp.cod_plaza = p.cod_plaza
				,deudor dr
		where	dp.cod_deudor = dr.cod_deudor
		and		dp.cod_estado_documento_pago = ve_cod_estado_doc_pago
		and		dp.fecha_documento_pago <= ve_fecha
		and		dr.rut = ve_rut
		order by nom_banco, fecha_documento_pago  ASC;
     END;
    END IF ;
 	END spi_documentos_cartera;
/

_PLAZA
				,MONTO)
		select REPLACE(To_Char(dr.rut, '99,999,999'), ',', '.') || '-' || dr.dig_verif rut
				,To_Char(fecha_documento_pago) fecha_documento_pago
				,b.nom_banco
				,p.nom_plaza
				,dp.monto
		from	documento_pago dp left outer join banco b on dp.cod_banco = b.cod_banco 
					left outer join plaza p on dp.cod_plaza = p.cod_plaza
				, deudor dr
		where	dp.cod_deudor = dr.cod_deudor
		and		dp.cod_estado_documento_pago = ve_cod_estado_doc_pago
		and		dp.fecha_documento_pago <= To_Date(ve_fecha,'YYYY-MM-DD HH24:MI:SS')
		order by nom_banco, fecha_documento_pago ASC ;
	 
	else
	begin
		insert into TEMP_DOCUMENTOS_CATERA
				(RUT 
				,FECHA_DOC_PAGO
				,NOM_BANCO
				,NOM_PLAZA
				,MONTO)
		SELECT REPLACE(To_Char(dr.rut, '99,999,999'), ',', '.') || '-' || dr.dig_verif rut
				,To_Char(fecha_documento_pago) fecha_documento_pago
				,b.nom_banco
				,p.nom_plaza
				,dp.monto
		from	documento_pago dp left outer join banco b on dp.cod_banco = b.cod_banco
					left outer join plaza p on dp.cod_plaza = p.cod_plaza
				,deudor dr
		where	dp.cod_deudor = dr.cod_deudor 
		and		dp.cod_estado_documento_pago = ve_cod_estado_doc_pago
		and		dp.fecha_documento_pago <= To_Date(ve_fecha,'YYYY-MM-DD HH24:MI:SS')
		and		dr.rut = ve_rut
		order by nom_banco, fecha_documento_pago  ASC;
     END; 
    END IF ; 
 	END spi_documentos_cartera;





