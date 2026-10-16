CREATE OR REPLACE PROCEDURE spx_undo_carga_matricula
AS
BEGIN
  DECLARE
    vl_cod_deudor           NUMBER;
    vl_count                NUMBER;

    CURSOR C_CARGA_MATRICULA IS
      SELECT  RUT
              ,ANO_MATRICULA
              ,SEMESTRE
      FROM    CARGA_MATRICULA
      WHERE   CARGADO = 'S';

  BEGIN
    -- borra los pagares cargados
    FOR a IN C_CARGA_MATRICULA LOOP
      SELECT cod_deudor
      INTO vl_cod_deudor
      FROM deudor   
      WHERE  rut = a.rut;

      DELETE matricula 
      WHERE  cod_deudor = vl_cod_deudor
        AND ano_matricula = a.ano_matricula;

    END LOOP;
     
    DELETE CARGA_MATRICULA;
    COMMIT;
  END;
END spx_undo_carga_matricula;
/
