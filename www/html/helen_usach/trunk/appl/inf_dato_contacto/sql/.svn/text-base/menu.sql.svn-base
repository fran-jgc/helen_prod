insert into item_menu values('4094'		,'Datos de Contacto'		,'S', 'M', 'S',null);

insert into autoriza_menu select autoriza_menu_sq.NEXTVAL, 1, '4094', 'E', 'S' from dual;
insert into autoriza_menu select autoriza_menu_sq.NEXTVAL, COD_PERFIL , '4094', 'N', 'S' from PERFIL p WHERE p.COD_PERFIL <> 1;
COMMIT;