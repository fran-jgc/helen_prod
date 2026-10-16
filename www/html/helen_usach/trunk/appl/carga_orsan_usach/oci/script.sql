insert into estado_carga values(12,'No existe cuota');
insert into estado_carga values(13,'Deuda no pertenece a deudor')

update estado_carga
set nom_estado_carga = 'No existe cuota'
where cod_estado_carga = 12;

update estado_carga
set nom_estado_carga = 'Deuda no pertenece a deudor'
where cod_estado_carga = 13;


-----------------26/08/2026-----------------
ALTER TABLE ITEM_CARGA_ORSAN_USACH
ADD (FOLIO VARCHAR(100) null) ;


------------------22/09/2026-----------------
insert into estado_carga values(16,'Deuda Estado Cancelada')