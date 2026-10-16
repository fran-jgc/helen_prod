-- ============================================================
--   Table: CARGA_ORSAN_USACH                                  
-- ============================================================
create table CARGA_ORSAN_USACH
(
    COD_CARGA_ORSAN_USACH       NUMBER(10)             not null,
    COD_USUARIO                 NUMBER(10)             null    ,
    FECHA_CARGA_ORSAN_USACH     DATE                   null    ,
    FECHA_REGISTRO              DATE                   not null,
    TOTAL_REG_CARGA             NUMBER(10)             null    ,
    TOTAL_REG_EXITOSO           NUMBER(10)             null    ,
    TOTAL_REG_PDTE              NUMBER(10)             null    ,
    MONTO_REG_CARGA             NUMBER(14,4)           null    ,
    MONTO_REG_EXITOSO           NUMBER(14,4)           null    ,
    MONTO_REG_PDTE              NUMBER(14,4)           null    ,
    constraint PK_CARGA_ORSAN_USACH primary key (COD_CARGA_ORSAN_USACH)
)
/

-- ============================================================
--   Table: ITEM_CARGA_ORSAN_USACH                             
-- ============================================================
create table ITEM_CARGA_ORSAN_USACH
(
    COD_ITEM_CARGA_ORSAN_USACH  NUMBER(14)             not null,
    COD_CARGA_ORSAN_USACH       NUMBER(10)             null    ,
    COD_ESTADO_CARGA            NUMBER(10)             null    ,
    RUT                         NUMBER(10)             null    ,
    DIG_VERIF                   VARCHAR(1)             null    ,
    NOMBRE                      VARCHAR(100)           null    ,
    RUT_UNIVERSIDAD             VARCHAR(20)            null    ,
    NOMBRE_UNIVERSIDAD          VARCHAR(100)           null    ,
    VALOR_UTM                   NUMBER(14,4)           null    ,
    ANO_CUOTA                   NUMBER(10)             null    ,
    NRO_COMPROBANTE             NUMBER(15)             null    ,
    NRO_CUOTA                   NUMBER(10)             null    ,
    FECHA_PAGO                  DATE                   null    ,
    PORC_USACH                  NUMBER(5,2)            null    ,
    MONTO_CAPITAL_UTM           NUMBER(14,4)           null    ,
    MONTO_CAPITAL_PESOS         NUMBER(14)             null    ,
    MONTO_INTERES_UTM           NUMBER(14,4)           null    ,
    MONTO_INTERES_PESOS         NUMBER(14)             null    ,
    MONTO_DESCTO_UTM            NUMBER(14,4)           null    ,
    MONTO_DESCTO_PESOS          NUMBER(14)             null    ,
    MONTO_TOTAL_PESOS           NUMBER(14)             null    ,
    MONTO_TOTAL_UTM             NUMBER(14,4)           null    ,
    COD_DEUDA                   NUMBER(10)             null    ,
    COD_TIPO_DEUDA              VARCHAR(20)            null    ,
    FECHA_VENCTO                DATE                   null    ,
    TIPO_COBRO                  VARCHAR(10)            null    ,
    COD_ORIGEN_PAGO             NUMBER(10)             null    ,
    FECHA_DEPOSITO				DATE                   null    ,
    COD_COMPROBANTE				NUMBER(14)			   null    ,
    COD_EJECUCION				NUMBER(14)			   null    ,
    constraint PK_ITEM_CARGA_ORSAN_USACH primary key (COD_ITEM_CARGA_ORSAN_USACH)
)
/

-- ============================================================
--   Table: ESTADO_CARGA                                       
-- ============================================================
create table ESTADO_CARGA
(
    COD_ESTADO_CARGA            NUMBER(10)             not null,
    NOM_ESTADO_CARGA            VARCHAR(100)           null    ,
    constraint PK_ESTADO_CARGA primary key (COD_ESTADO_CARGA)
)
/

-- ============================================================
--   Table: BITACORA_CARGA_ORSAN                                       
-- ============================================================
create table BITACORA_CARGA_ORSAN
(
    COD_BITACORA_CARGA_ORSAN    NUMBER(10)             not null,
    FECHA_REGISTRO              DATE                   not null,
    COD_CARGA_ORSAN_USACH       NUMBER(10)             null    ,
    COD_ITEM_CARGA_ORSAN_USACH  NUMBER(14)             null	   ,
    COD_USUARIO                 NUMBER(10)             null    ,
    OBSERVACION              	VARCHAR(200)           null    ,
    constraint PK_BITACORA_CARGA_ORSAN primary key (COD_BITACORA_CARGA_ORSAN)
)
/

alter table CARGA_ORSAN_USACH
    add constraint FK_CARGA_OR_REF_37222_USUARIO foreign key  (COD_USUARIO)
       references USUARIO (COD_USUARIO)
/

alter table ITEM_CARGA_ORSAN_USACH
    add constraint FK_ITEM_CAR_REF_37226_CARGA_OR foreign key  (COD_CARGA_ORSAN_USACH)
       references CARGA_ORSAN_USACH (COD_CARGA_ORSAN_USACH)
/

alter table ITEM_CARGA_ORSAN_USACH
    add constraint FK_ITEM_CAR_REF_37226_ESTADO_C foreign key  (COD_ESTADO_CARGA)
       references ESTADO_CARGA (COD_ESTADO_CARGA)
/


PROMPT CREATE SEQUENCE carga_orsan_usach_sq
CREATE SEQUENCE carga_orsan_usach_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


PROMPT CREATE SEQUENCE item_carga_orsan_usach_sq
CREATE SEQUENCE item_carga_orsan_usach_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


PROMPT CREATE SEQUENCE bitacora_carga_orsan_sq
CREATE SEQUENCE bitacora_carga_orsan_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/

INSERT INTO  tipo_bitacora VALUES (43, 'INGRESO DE PAGO CARGA ORSAN' , 'S');
INSERT INTO  tipo_bitacora VALUES (44, 'INGRESO DE PAGO CARGA OTRA UES', 'S');


INSERT INTO ESTADO_CARGA VALUES (1 , 'Por Procesar');
INSERT INTO ESTADO_CARGA VALUES (2 , 'Carga exitosa');
INSERT INTO estado_carga VALUES (3 , 'Distinto COBRANZA NORMAL y CANCELADA');
INSERT INTO estado_carga VALUES (4 , 'Falta valor moneda para la Fecha pago');
INSERT INTO estado_carga VALUES (5 , 'Origen No ORSAN');
INSERT INTO estado_carga VALUES (6 , 'Error K + Int. = 0');
INSERT INTO estado_carga VALUES (7 , 'Rut no existe');
INSERT INTO estado_carga VALUES (8 , 'Deuda no existe');
INSERT INTO estado_carga VALUES (9 , 'No tiene saldo');

INSERT INTO PROCESO VALUES (21, 'CARGA ORSAN');
INSERT INTO origen_pago VALUES (29 , 'ORSAN');
INSERT INTO origen_comprobante VALUES (47, 'CARGA BANCO ORSAN');

INSERT INTO item_menu VALUES (1511, 'Carga ORSAN', 'N', 'M', 'S', null);

INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 1, 1511, 'E', 'S'); 
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 2, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 3, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 4, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 5, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 6, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 7, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 8, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 9, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 10, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 11, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 12, 1511, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 14, 1511, 'N', 'N');  

alter table descuento add COD_ABONO NUMBER(10,0) NULL;
commit;

