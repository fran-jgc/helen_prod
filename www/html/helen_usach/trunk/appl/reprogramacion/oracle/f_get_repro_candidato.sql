PROMPT CREATE OR REPLACE FUNCTION f_get_repro_candidato
CREATE OR REPLACE FUNCTION f_get_repro_candidato
(
 ve_cod_deudor   NUMBER
)
RETURN VARCHAR2 IS vs_candidato VARCHAR2(1);
BEGIN
DECLARE
    vl_count             NUMBER;
    vl_rut               NUMBER;
BEGIN

    SELECT RUT
    INTO vl_rut
    FROM DEUDOR
    WHERE COD_DEUDOR = ve_cod_deudor;

    SELECT count(*)
    INTO vl_count
	  FROM REPRO_2012
    WHERE rut = vl_rut;

    IF(vl_count > 0)THEN
	    vs_candidato := 'S';
    ELSE
    	vs_candidato := 'N';
    END IF;

    RETURN (vs_candidato);
END;
END f_get_repro_candidato;
/

