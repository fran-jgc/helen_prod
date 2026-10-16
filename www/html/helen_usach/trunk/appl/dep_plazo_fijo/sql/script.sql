-- ============================================================
--   Database name:  HELEN                                     
--   DBMS name:      ORACLE Version 8                          
--   Created on:     11/04/2022  13:59                         
-- ============================================================

drop table ESTADO_DEP_PLAZO_FIJO cascade constraints
/

drop table DEP_PLAZO_FIJO cascade constraints
/

-- ============================================================
--   Table: DEP_PLAZO_FIJO                                     
-- ============================================================
create table DEP_PLAZO_FIJO
(
    COD_DEP_PLAZO_FIJO         NUMBER(10)             not null,
    COD_USUARIO                NUMBER(10)             not null,
    COD_ESTADO_DEP_PLAZO_FIJO  NUMBER(10)             not null,
    COD_MONEDA                 NUMBER(10)             null    ,
    FECHA_DEP_PLAZO_FIJO       DATE                   not null,
    FECHA_EMISION              DATE                   not null,
    DIAS                       NUMBER(10)             not null,
    FECHA_VENCTO               DATE                   not null,
    TASA_INTERES_BASE          NUMBER(18,5)           not null,
    TASA_INTERES_PERIODO       NUMBER(18,5)           not null,
    VALOR_INICIAL              NUMBER(18,5)           not null,
    INTERESES                  NUMBER(18,5)           not null,
    VALOR_FINAL                NUMBER(18,5)           not null,
    DESCRIPCION                VARCHAR(1000)          null    ,
    constraint PK_DEP_PLAZO_FIJO primary key (COD_DEP_PLAZO_FIJO)
)
/

-- ============================================================
--   Table: ESTADO_DEP_PLAZO_FIJO                              
-- ============================================================
create table ESTADO_DEP_PLAZO_FIJO
(
    COD_ESTADO_DEP_PLAZO_FIJO  NUMBER(10)             not null,
    NOM_ESTADO_DEP_PLAZO_FIJO  VARCHAR2(100)          not null,
    constraint PK_ESTADO_DEP_PLAZO_FIJO primary key (COD_ESTADO_DEP_PLAZO_FIJO)
)
/

alter table DEP_PLAZO_FIJO
    add constraint FK_DEP_PLAZO_FIJO_USUARIO foreign key  (COD_USUARIO)
       references USUARIO (COD_USUARIO)
/

alter table DEP_PLAZO_FIJO
    add constraint FK_EST_DEP_PLAZO_FIJO foreign key  (COD_ESTADO_DEP_PLAZO_FIJO)
       references ESTADO_DEP_PLAZO_FIJO (COD_ESTADO_DEP_PLAZO_FIJO)
/

alter table DEP_PLAZO_FIJO
    add constraint FK_DEP_PLAZO_FIJO_MONEDA foreign key  (COD_MONEDA)
       references MONEDA (COD_MONEDA)
/






INSERT INTO ESTADO_DEP_PLAZO_FIJO VALUES(1,'EMITIDA');
INSERT INTO ESTADO_DEP_PLAZO_FIJO VALUES(2,'CONFIRMADA');
INSERT INTO ESTADO_DEP_PLAZO_FIJO VALUES(3,'ANULADA');

INSERT INTO item_menu VALUES('1540','Depósito plazo Fijo', 'S', 'M', 'S', NULL);
INSERT INTO AUTORIZA_MENU SELECT autoriza_menu_sq.NEXTVAL, COD_PERFIL, '1540', 'N', 'S' FROM PERFIL WHERE COD_PERFIL <> 1;


PROMPT CREATE SEQUENCE dep_plazo_fijo_sq
CREATE SEQUENCE dep_plazo_fijo_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


INSERT INTO ORIGEN_COMPROBANTE VALUES (48, 'DEPOSITO PLAZO FIJO');

/*****************24/03/2022***************/
 alter table dep_plazo_fijo     add cod_comprobante number(10)  null;
 alter table dep_plazo_fijo     add COD_CUENTA_CORRIENTE number(10)  null;
 