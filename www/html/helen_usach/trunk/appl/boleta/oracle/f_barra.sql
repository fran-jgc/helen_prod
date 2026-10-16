PROMPT CREATE OR REPLACE FUNCTION f_barra
CREATE OR REPLACE FUNCTION f_barra(ve_cadena varchar2)

RETURN VARCHAR

AS

vs_checksum      VARCHAR (100);
vl_largo_cadena number(3);
vl_caracter     char(1);
vl_cuenta       number(3);
vl_num_ascii    number(3);
vl_acumula      number(15);
vl_factor       number(6);
vl_resto        number(6);
vl_producto     number(6);


BEGIN

 vl_largo_cadena := length(ve_cadena);
 vl_acumula      := 0;
 vl_cuenta       := vl_largo_cadena;
 vl_factor       := 2;
 vl_resto        := 0;

   while (vl_cuenta > 0)  loop


          vl_caracter := substr(ve_cadena,vl_cuenta,1);

          if vl_caracter <> ' ' then
              vl_num_ascii := ascii(vl_caracter);
          else
              vl_num_ascii := 0;
          end if;

          vl_producto := vl_num_ascii * vl_factor;

          vl_acumula := vl_acumula + vl_producto;


          vl_cuenta := vl_cuenta - 1;
          vl_factor := vl_factor + 1;
   end loop;

    vl_resto := vl_acumula mod 98;
    

	--vs_checksum := vl_resto;
	IF (length(vl_resto) = 1) THEN
		vs_checksum := '0'||vl_resto;
	ELSE
		vs_checksum := vl_resto;
	END IF;                                 

   RETURN vs_checksum;

END;
/