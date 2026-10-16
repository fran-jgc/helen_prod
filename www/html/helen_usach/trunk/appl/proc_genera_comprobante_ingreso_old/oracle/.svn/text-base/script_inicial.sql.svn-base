CREATE TABLE registro_comp_ingreso(
	cod_registro_comp_ingreso  number(18, 0) primary key NOT NULL,
	fecha_desde date NULL,
	fecha_hasta date NULL,
	fecha_comprobante date NULL
); 

PROMPT CREATE SEQUENCE registro_comp_ingreso_sq
CREATE SEQUENCE registro_comp_ingreso_sq
  MINVALUE 1
  MAXVALUE 999999999999
  INCREMENT BY 1
  NOCYCLE
  ORDER
  NOCACHE
/


alter table comprobante add cod_registro_comp_ingreso number(18,0) null;


INSERT INTO item_menu VALUES (2565, 'Genera comprobante ingreso', 'N', 'M', 'S', null);
 
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 1, 2565, 'L', 'S');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 2, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 3, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 4, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 5, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 6, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 7, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 8, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 9, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 10, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 11, 2565, 'N', 'N');
INSERT INTO autoriza_menu VALUES (autoriza_menu_sq.NEXTVAL, 12, 2565, 'N', 'N');

INSERT INTO proceso VALUES (25, 'GENERA COMPROBANTE INGRESO');

commit;