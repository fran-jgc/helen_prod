ALTER TABLE ITEM_CARGA_OTRA_UES
ADD (
   ANO_CUOTA NUMBER(4),
   COD_DEUDA NUMBER(10)
);




--------------06-11-2025----------------
ALTER TABLE ITEM_CARGA_OTRA_UES
ADD (
   NRO_CUOTA NUMBER(10)
);

INSERT INTO ESTADO_CARGA VALUES(13,'No existe Plan Pago');
INSERT INTO ESTADO_CARGA VALUES(14,'Deuda no pertenece a Rut ingresado');



--------------21-08-2026----------------
insert into estado_carga values(15,'Saldo negativo');
ALTER TABLE ITEM_CARGA_OTRA_UES
ADD (MONTO_DESCTO_UTM NUMBER(14,4) null,
    MONTO_DESCTO_PESOS NUMBER(14) null ) ;