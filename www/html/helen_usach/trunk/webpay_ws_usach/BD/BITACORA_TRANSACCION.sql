-- ============================================================
--   Table: BITACORA_TRANSACCION                               
-- ============================================================
create table BITACORA_TRANSACCION
(
    COD_BITACORA_TRANSACCION  NUMBER(10)             not null,
    COD_WP_TRANSACCION        NUMBER(10)             null    ,
    FASE                      VARCHAR(100)           null    ,
    MENSAJE                   VARCHAR(100)           null    ,
    FECHA                     DATE                   DEFAULT SYSDATE,
    constraint PK_BITACORA_TRANSACCION primary key (COD_BITACORA_TRANSACCION)
)
/

alter table BITACORA_TRANSACCION
    add constraint FK_BITA_WP_TRANS foreign key  (COD_WP_TRANSACCION)
       references WP_TRANSACCION (COD_WP_TRANSACCION)
/
-- ============================================================
--  Secuencia: BITACORA_TRANSACCION                               
-- ============================================================
CREATE SEQUENCE bitacora_transaccion_sq
  MINVALUE 1
  MAXVALUE 9999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/     

