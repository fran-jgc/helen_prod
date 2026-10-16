CREATE OR REPLACE PROCEDURE spu_convenio_bdd(ve_operacion IN varchar2
									,ve_cod_convenio_bdd      IN    number
									,ve_cod_tipo_deuda        IN    VARCHAR2  :=null
									,ve_ano                   IN    NUMBER    :=null
                  ,ve_convenio              IN    NUMBER    :=null
                  ,ve_con_declaracion       IN    VARCHAR2  :=null
                  )

AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO convenio_bdd
                  (cod_convenio_bdd
                  ,cod_tipo_deuda
                  ,ano
                  ,convenio
                  ,con_declaracion
                  )
           VALUES (convenio_bdd_sq.nextval
                  ,ve_cod_tipo_deuda
                  ,ve_ano
                  ,ve_convenio
                  ,ve_con_declaracion
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE convenio_bdd
         SET cod_tipo_deuda = ve_cod_tipo_deuda
            ,ano            = ve_ano
            ,convenio       = ve_convenio
            ,con_declaracion= ve_con_declaracion

       WHERE cod_convenio_bdd = ve_cod_convenio_bdd;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM convenio_bdd
            WHERE cod_convenio_bdd = ve_cod_convenio_bdd;
   END IF;
END;
/