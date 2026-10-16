PROMPT CREATE TABLE temp_credito_asig
CREATE GLOBAL TEMPORARY TABLE temp_credito_asig (
  folio          NUMBER NULL,
  ano            NUMBER NULL,
  rut_deudor     VARCHAR2(10) NULL,
  monto_asignado NUMBER NULL
)
  ON COMMIT DELETE ROWS
/