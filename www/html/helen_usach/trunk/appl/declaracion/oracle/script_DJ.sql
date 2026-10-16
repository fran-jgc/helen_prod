create table REVISADO_ARCHIVO
(
    COD_REVISADO_ARCHIVO    NUMERIC                not null,
    NOM_REVISADO_ARCHIVO    VARCHAR2(100)          not null,
    constraint PK_REVISADO_ARCHIVO primary key (COD_REVISADO_ARCHIVO)
);

CREATE SEQUENCE declaracion_archivo_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  CACHE 20 ;

CREATE TABLE declaracion_archivo (
  cod_declaracion_archivo NUMBER(10,0)   NOT NULL,
  cod_declaracion         NUMBER(10,0)   NOT NULL,
  cod_tipo_archivo        NUMBER(10,0)   NULL,
  ruta_archivo            VARCHAR2(500)  NULL,
  nom_archivo             VARCHAR2(100)  NULL,
  tamano                  NUMBER(10,0)   NULL,
  cod_revisado_archivo    NUMBER(10,0)   DEFAULT 1 NOT NULL,
  obs_revisado            VARCHAR2(100)  NULL,
  f_error                 VARCHAR2(4000) NULL,
  f_name                  VARCHAR2(4000) NULL,
  f_type                  VARCHAR2(4000) NULL,
  f_tmp_name              VARCHAR2(4000) NULL,
  fecha_subida            DATE           NULL,
  navegador               VARCHAR2(4000) NULL,
  ip                      VARCHAR2(4000) NULL,
  os                      VARCHAR2(4000) NULL,
  cod_usuario             NUMBER(10,0)   DEFAULT 2 NULL,
  origen				  varchar(10) 	 DEFAULT 'PORTAL WEB'
);

PROMPT CREATE INDEX ind_declarch1
CREATE INDEX ind_declarch1
  ON declaracion_archivo (
    cod_declaracion
  );

PROMPT CREATE INDEX ind_declarch2
CREATE INDEX ind_declarch2
  ON declaracion_archivo (
    cod_tipo_archivo
  );

PROMPT ALTER TABLE declaracion_archivo ADD CONSTRAINT pk_declaracion_archivo PRIMARY KEY
ALTER TABLE declaracion_archivo
  ADD CONSTRAINT pk_declaracion_archivo PRIMARY KEY (
    cod_declaracion_archivo
  );

PROMPT ALTER TABLE declaracion_archivo ADD CONSTRAINT fk_revarch_declarch FOREIGN KEY
ALTER TABLE declaracion_archivo
  ADD CONSTRAINT fk_revarch_declarch FOREIGN KEY (
    cod_revisado_archivo
  ) REFERENCES revisado_archivo (
    cod_revisado_archivo
  );

CREATE INDEX IND_DECLARCH3 ON declaracion_archivo (cod_declaracion, cod_revisado_archivo);


create table TIPO_ARCHIVO
(
    COD_TIPO_ARCHIVO         NUMBER(10)             not null,
    NOM_TIPO_ARCHIVO         VARCHAR2(100)          not null,
    AYUDA                    VARCHAR2(500)          not null,
    CARDINALIDAD             VARCHAR2(2)            not null,
    constraint PK_TIPO_ARCHIVO primary key (COD_TIPO_ARCHIVO)
);

PROMPT ALTER TABLE declaracion_archivo ADD CONSTRAINT fk_tarch_declarch FOREIGN KEY
ALTER TABLE declaracion_archivo
  ADD CONSTRAINT fk_tarch_declarch FOREIGN KEY (
    cod_tipo_archivo
  ) REFERENCES tipo_archivo (
    cod_tipo_archivo
  );


---------------------------------------
insert into TIPO_ARCHIVO values (1, 'Declaración Jurada', 'Debe estar firmada y timbrada ante notario o consulado si está en el extranjero', '1');
insert into TIPO_ARCHIVO values (2, 'Formulario 22 Compacto', 'Se obtiene en www.sii.cl', '1');
insert into TIPO_ARCHIVO values (3, 'Agentes retenedores', 'Se obtiene en www.sii.cl', '1');
insert into TIPO_ARCHIVO values (4, 'Certificado de cotizaciones previsionales', 'Se obtiene en la pagina web de su AFP', '1');
insert into TIPO_ARCHIVO values (5, 'Certificado de remuneraciones', 'Se obtiene de su empleador', '1');
insert into TIPO_ARCHIVO values (6, 'Certificado de nacimiento de hijo', 'Se obtiene en www.registrocivil.cl', '1');
insert into TIPO_ARCHIVO values (7, 'Certificado de matrimonio', 'Se obtiene en www.registrocivil.cl', '1');
insert into TIPO_ARCHIVO values (8, 'Otros', 'pueden ser finiquitos, Licencias medicas, Becas, etc.', 'N');
insert into TIPO_ARCHIVO values (9, 'Liquidaciones', 'Liquidaciones de sueldo percibidas de enero a diciembre del año anterior.', '30');



insert into REVISADO_ARCHIVO values (1, 'Sin revisar');
insert into REVISADO_ARCHIVO values (2, 'Sin observaciones');
insert into REVISADO_ARCHIVO values (3, 'Con observaciones');
insert into REVISADO_ARCHIVO values (4, 'Solicitado');
insert into REVISADO_ARCHIVO values (5, 'Recibido');

INSERT INTO parametro VALUES(35,'Cant max arhivos para subir DJ','20');
INSERT INTO parametro VALUES(36,'Peso max arhivos DJ','2');
INSERT INTO parametro VALUES(37,'Ruta arhivos DJ','declaracionjurada');


COMMIT;