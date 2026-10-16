-- ============================================================
--   Table: CARGA_ORSAN_USACH                                  
-- ============================================================
create table CARGA_OTRA_UES
(
    COD_CARGA_OTRA_UES       	NUMBER(10)             not null,
    COD_USUARIO                 NUMBER(10)             null    ,
    FECHA_CARGA_OTRA_UES    	DATE                   null    ,
    FECHA_REGISTRO              DATE                   not null,
    TOTAL_REG_CARGA             NUMBER(10)             null    ,
    TOTAL_REG_EXITOSO           NUMBER(10)             null    ,
    TOTAL_REG_PDTE              NUMBER(10)             null    ,
    MONTO_REG_CARGA             NUMBER(14,4)           null    ,
    MONTO_REG_EXITOSO           NUMBER(14,4)           null    ,
    MONTO_REG_PDTE              NUMBER(14,4)           null    ,
    constraint PK_CARGA_OTRA_UES primary key (COD_CARGA_OTRA_UES)
)
/

-- ============================================================
--   Table: ITEM_CARGA_ORSAN_USACH                             
-- ============================================================
create table ITEM_CARGA_OTRA_UES
(
    COD_ITEM_CARGA_OTRA_UES  	NUMBER(14)             not null,
    COD_CARGA_OTRA_UES       	NUMBER(10)             null    ,
    COD_ESTADO_CARGA            NUMBER(10)             null    ,
    RUT                         NUMBER(10)             null    ,
    DIG_VERIF                   VARCHAR(1)             null    ,
    FOLIO                      	VARCHAR(100)           null    ,
    MONTO_UTM           		NUMBER(14,4)           null    ,
    MONTO_PESOS         		NUMBER(14,0)           null    ,
    FECHA_PAGO                	DATE                   null    ,
    COD_ORIGEN_PAGO             NUMBER(10)             null    ,
    FECHA_DEPOSITO				DATE                   null    ,
    COD_COMPROBANTE				NUMBER(14)			   null    ,	
    COD_EJECUCION				NUMBER(14)			   null    ,
    constraint PK_ITEM_CARGA_OTRA_UES primary key (COD_ITEM_CARGA_OTRA_UES)
)
/
/*
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
*/

-- ============================================================
--   Table: BITACORA_CARGA_ORSAN                                       
-- ============================================================
create table BITACORA_CARGA_OTRA_UES
(
    COD_BITACORA_CARGA_OTRA_UES NUMBER(10)             not null,
    FECHA_REGISTRO              DATE                   not null,
    COD_CARGA_OTRA_UES       	NUMBER(10)             null    ,
    COD_ITEM_CARGA_OTRA_UES  	NUMBER(14)             null	   ,
    COD_USUARIO                 NUMBER(10)             null    ,
    OBSERVACION              	VARCHAR(200)           null    ,
    constraint PK_BITACORA_CARGA_OTRA_UES primary key (COD_BITACORA_CARGA_OTRA_UES)
)
/

alter table CARGA_OTRA_UES
    add constraint FK_CARGA_OUES_REF_USUARIO foreign key  (COD_USUARIO)
       references USUARIO (COD_USUARIO)
/

alter table ITEM_CARGA_OTRA_UES
    add constraint FK_ITEM_CAR__REF_CARGA_OUES foreign key  (COD_CARGA_OTRA_UES)
       references CARGA_OTRA_UES (COD_CARGA_OTRA_UES)
/

alter table ITEM_CARGA_OTRA_UES
    add constraint FK_ITEM_CAR_REF_ESTADO_C foreign key  (COD_ESTADO_CARGA)
       references ESTADO_CARGA (COD_ESTADO_CARGA)
/


PROMPT CREATE SEQUENCE carga_otra_ues_sq
CREATE SEQUENCE carga_otra_ues_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


PROMPT CREATE SEQUENCE item_carga_otra_ues_sq
CREATE SEQUENCE item_carga_otra_ues_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


PROMPT CREATE SEQUENCE bitacora_carga_otra_ues_sq
CREATE SEQUENCE bitacora_carga_otra_ues_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


INSERT INTO estado_carga VALUES (10, 'No tiene Deudas familia 1');
INSERT INTO estado_carga VALUES (11, 'Error, 2 deudas familia 1 COBRANZA NORMAL');

INSERT INTO PROCESO VALUES (22, 'CARGA OTRA UES');
INSERT INTO item_menu VALUES (1512, 'CARGA OTRAS UES', 'N', 'M', 'S', null);

INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 1, 1512, 'E', 'S'); 
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 2, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 3, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 4, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 5, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 6, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 7, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 8, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 9, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 10, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 11, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 12, 1512, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 14, 1512, 'N', 'N');  

commit;

