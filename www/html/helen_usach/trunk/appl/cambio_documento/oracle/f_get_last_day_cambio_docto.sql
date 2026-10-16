PROMPT CREATE OR REPLACE FUNCTION f_get_last_day_cambio_docto
CREATE OR REPLACE FUNCTION f_get_last_day_cambio_docto(ve_fecha   VARCHAR2)
   RETURN DATE
AS
BEGIN
  DECLARE	vl_last_fecha_mes   DATE;
           vl_fecha_posterior DATE;
           vl_fecha_docto     DATE;
  
  BEGIN
    vl_fecha_docto:= Trunc(To_Date(ve_fecha,'dd/mm/yyyy'));

    SELECT  Trunc(Last_Day(SYSDATE))
    INTO    vl_last_fecha_mes
    FROM    dual;

    IF(vl_last_fecha_mes = ve_fecha)THEN 
      SELECT  add_months(Last_Day(To_Date(ve_fecha,'dd/mm/yyyy')),1)
      INTO    vl_fecha_posterior 
      FROM    dual;
    ELSE
      SELECT  add_months(To_Date(ve_fecha,'dd/mm/yyyy'),1) 
      INTO    vl_fecha_posterior      
      FROM    dual;
    END IF;      

    RETURN vl_fecha_posterior;
  END;
END;
/