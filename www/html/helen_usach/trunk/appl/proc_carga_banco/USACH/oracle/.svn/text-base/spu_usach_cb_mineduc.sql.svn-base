PROMPT CREATE OR REPLACE PROCEDURE spu_usach_cb_mineduc
CREATE OR REPLACE PROCEDURE spu_usach_cb_mineduc(ve_operacion                 IN VARCHAR2
                                              ,ve_rut                         IN NUMBER   :=NULL
                                              ,ve_tot_solicita                IN NUMBER   :=NULL
                                              ,ve_uni_solicita                IN NUMBER   :=NULL
                                              ,ve_porcen_uni	                IN NUMBER   :=NULL
                                              ,ve_rut_uni_sol                 IN NUMBER   :=NULL
                                              ,ve_rut_uni_ret                 IN NUMBER   :=NULL
                                              ,ve_cod_proceso                 IN NUMBER   :=NULL
                                              ,ve_cod_usuario                 IN NUMBER   :=NULL)

AS
BEGIN

  DECLARE
    vl_cod_ejecucion			NUMBER;
    vl_total_registros    NUMBER;




  BEGIN

            IF (ve_operacion = 'INSERT')THEN
              INSERT INTO usach_cb_mineduc
			                  (cod_usach_cb_mineduc
                        ,ct_rut
                        ,ct_tot_solicita
                        ,ct_uni_solicita
                        ,ct_porcen_uni
                        ,ct_rut_uni_sol
                        ,ct_rut_uni_ret
                        ,ct_ano)
	            VALUES (usach_cb_mineduc_sq.NEXTVAL
                        ,ve_rut
                        ,ve_tot_solicita
                        ,ve_uni_solicita
                        ,ve_porcen_uni
                        ,ve_rut_uni_sol
                        ,ve_rut_uni_ret
                        ,To_Number(To_Char(SYSDATE, 'YYYY'), '9999'));
            ELSIF (ve_operacion = 'EJEC_PROCESO')  THEN

                    spu_ejecucion_proceso ('NEW', NULL, ve_cod_proceso, ve_cod_usuario);

        	          SELECT ejecucion_proceso_sq.CURRVAL
		                INTO vl_cod_ejecucion
		                FROM dual;

                    SELECT Count(*)
                    INTO vl_total_registros
                    FROM usach_cb_mineduc;

                    spu_ejecucion_proceso ('SET_TOTAL', vl_cod_ejecucion, vl_total_registros);


                    spu_ejecucion_proceso ('END', vl_cod_ejecucion, vl_total_registros);

          END IF;
    END;
END spu_usach_cb_mineduc;
/

