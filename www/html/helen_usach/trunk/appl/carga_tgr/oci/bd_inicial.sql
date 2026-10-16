INSERT INTO  tipo_bitacora VALUES (45, 'INGRESO DE PAGO CARGA TGR', 'S');

INSERT INTO item_menu VALUES (1514, 'Carga TGR', 'N', 'M', 'S', null);

INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 1, 1514, 'E', 'S'); 
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 2, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 3, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 4, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 5, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 6, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 7, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 8, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 9, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 10, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 11, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 12, 1514, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 14, 1514, 'N', 'N'); 

-- ============================================================
--   Table: CARGA_ORSAN_USACH                                  
-- ============================================================
create table CARGA_TGR
(
    COD_CARGA_TGR       		NUMBER(10)             not null,
    COD_USUARIO                 NUMBER(10)             null    ,
    FECHA_CARGA_TGR		    	DATE                   null    ,
    FECHA_REGISTRO              DATE                   not null,
    TOTAL_REG_CARGA             NUMBER(10)             null    ,
    TOTAL_REG_EXITOSO           NUMBER(10)             null    ,
    TOTAL_REG_PDTE              NUMBER(10)             null    ,
    MONTO_REG_CARGA             NUMBER(14,4)           null    ,
    MONTO_REG_EXITOSO           NUMBER(14,4)           null    ,
    MONTO_REG_PDTE              NUMBER(14,4)           null    ,
    constraint PK_CARGA_TGR primary key (COD_CARGA_TGR)
)
/

-- ============================================================
--   Table: ITEM_CARGA_ORSAN_USACH                             
-- ============================================================
create table ITEM_CARGA_TGR
(
    COD_ITEM_CARGA_TGR  		NUMBER(14)             not null,
    COD_CARGA_TGR		       	NUMBER(10)             null    ,
    COD_ESTADO_CARGA            NUMBER(10)             null    ,
    RUT                         NUMBER(10)             null    ,
    DIG_VERIF                   VARCHAR(1)             null    ,
    FOLIO                      	VARCHAR(100)           null    ,
    MONTO_DEUDA_UTM           	NUMBER(14,4)           null    ,
    MONTO_RETENIDO_UTM          NUMBER(14,4)           null    ,
    MONTO_PESOS         		NUMBER(14,0)           null    ,
    FECHA_PAGO                	DATE                   null    ,
    COD_COMPROBANTE				NUMBER(14)			   null    ,	
    COD_EJECUCION				NUMBER(14)			   null    ,
    constraint PK_ITEM_CARGA_TGR primary key (COD_ITEM_CARGA_TGR)
)
/

-- ============================================================
--   Table: BITACORA_CARGA_ORSAN                                       
-- ============================================================
create table BITACORA_CARGA_TGR
(
    COD_BITACORA_CARGA_TGR		NUMBER(10)             not null,
    FECHA_REGISTRO              DATE                   not null,
    COD_CARGA_TGR       		NUMBER(10)             null    ,
    COD_ITEM_CARGA_TGR  		NUMBER(14)             null	   ,
    COD_USUARIO                 NUMBER(10)             null    ,
    OBSERVACION              	VARCHAR(200)           null    ,
    constraint PK_BITACORA_CARGA_TGR primary key (COD_BITACORA_CARGA_TGR)
)
/

alter table CARGA_TGR
    add constraint FK_CARGA_TGR_REF_USUARIO foreign key  (COD_USUARIO)
       references USUARIO (COD_USUARIO)
/

alter table ITEM_CARGA_TGR
    add constraint FK_ITEM_TGR_REF_CARGA_TGR foreign key  (COD_CARGA_TGR)
       references CARGA_TGR (COD_CARGA_TGR)
/

alter table ITEM_CARGA_TGR
    add constraint FK_ITEM_TGR_REF_ESTADO_C foreign key  (COD_ESTADO_CARGA)
       references ESTADO_CARGA (COD_ESTADO_CARGA)
/


PROMPT CREATE SEQUENCE carga_tgr_sq
CREATE SEQUENCE carga_tgr_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


PROMPT CREATE SEQUENCE item_carga_tgr_sq
CREATE SEQUENCE item_carga_tgr_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


PROMPT CREATE SEQUENCE bitacora_carga_tgr_sq
CREATE SEQUENCE bitacora_carga_tgr_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/

