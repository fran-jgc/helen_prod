CREATE OR REPLACE PROCEDURE spp_cuota_csr (
   VE_COD_EJECUCION_PROCESO   NUMBER
)
AS
/******************************************************************************
   NAME:       spp_cuota_csr
   DESCRIPTION Se corre en junio o cuando se hayan digitado todas las declaraciones


   REVISION:
   Ver        Date        Author           Description
   ---------  ----------  ---------------  ------------------------------------
   1.0        05/03/2010   SC              Creación

   NOTES:

******************************************************************************/
BEGIN
  DECLARE
    K_TIPO_DEUDA_CSR                        VARCHAR2(3) := 'CSR';
    K_ESTADO_DEUDA_EXIGIBLE                 NUMBER:= 2;
    K_BITACORA_CALCULO_CUOTA_CSR            NUMBER:= 20;
    K_FONDO_SOLIDARIO                       NUMBER:=1;
    K_ESTADO_COMP_PENDIENTE                 NUMBER:= 1;
    K_TIPO_COMP_TRASPASO                    NUMBER:= 3;
    K_ORIGEN_CALCULO_CUOTA_CSR              NUMBER:=20;

    vl_cod_usuario                          NUMBER;
    vl_count_deudas                         NUMBER;
    vl_count                                NUMBER;
    vl_i                                    NUMBER;
    vl_count_plan_pago                      NUMBER;
    vl_cod_comprobante_new                  NUMBER;
    vl_cod_deuda_err                        NUMBER;
    vl_nom_comprobante                      VARCHAR2(100);

	CURSOR C_DEUDA_CSR IS	SELECT	d.cod_deuda
									,d.cod_deudor
							FROM	deuda d
							WHERE	d.cod_tipo_deuda = K_TIPO_DEUDA_CSR
							  AND	d.cod_estado_deuda = K_ESTADO_DEUDA_EXIGIBLE;
	              
    BEGIN
		-- usuario que ejecuta el proceso
		SELECT	cod_usuario
		INTO	vl_cod_usuario
		FROM	ejecucion_proceso
		WHERE	cod_ejecucion_proceso = ve_cod_ejecucion_proceso;
		
		SELECT  Count(*)
		INTO	vl_count_deudas
		FROM    deuda d
		WHERE   d.cod_tipo_deuda = K_TIPO_DEUDA_CSR
		  AND	d.cod_estado_deuda = K_ESTADO_DEUDA_EXIGIBLE;
		
		vl_count := vl_count_deudas;
		spu_ejecucion_proceso ('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count);

	    vl_i :=1;
	    vl_count_plan_pago :=0;
	    vl_cod_comprobante_new := NULL;

		------------------------------------------------------------------------------------------
		-- MAIN
		------------------------------------------------------------------------------------------
		-- crear comprobante
		vl_nom_comprobante := 'Calculo Cuota CSR: ' || To_Char(SYSDATE,'dd/mm/yyyy');
		spu_comprobante ('INSERT', NULL, SYSDATE, vl_cod_usuario, vl_nom_comprobante, K_FONDO_SOLIDARIO, K_ESTADO_COMP_PENDIENTE, K_TIPO_COMP_TRASPASO, K_ORIGEN_CALCULO_CUOTA_CSR, vl_cod_usuario, SYSDATE);
		SELECT  comprobante_sq.CURRVAL
	    INTO    vl_cod_comprobante_new
	    FROM    dual;
		
	    FOR A IN C_DEUDA_CSR LOOP
			vl_cod_deuda_err := a.cod_deuda;
			-- verifica si el proceso ha sido cancelado
			IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S') THEN
			  sp_comp_simplifica (vl_cod_comprobante_new);
			  RETURN;
			END IF;
			
			-- Saber si existe plan de pago para el año actual
			SELECT  Count(*)
			INTO	vl_count_plan_pago
			FROM    plan_pago
			WHERE   ano_cobro = To_Number(To_Char(SYSDATE,'yyyy'))
			  AND	cod_deuda = a.cod_deuda;
			  
			IF (vl_count_plan_pago = 1) THEN
				sp_deuda_cuota_csr (a.cod_deuda, To_Number(To_Char(SYSDATE,'yyyy')), vl_cod_usuario, vl_cod_comprobante_new, 'N');
			END IF; 
	    
	    	spu_bitacora_deudor ('Calculo cuota CSR, deuda : ' || To_Char(a.cod_deuda), K_BITACORA_CALCULO_CUOTA_CSR, a.cod_deudor, vl_cod_usuario);
        	spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, vl_i);

		    vl_i := vl_i + 1;
	    END LOOP;
		------------------------------------------------------------------------------------------
		-- END MAIN
		------------------------------------------------------------------------------------------
		sp_comp_simplifica (vl_cod_comprobante_new);
		vl_i := vl_i - 1;
		spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
	EXCEPTION
        WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'Deuda: ' || To_Char(vl_cod_deuda_err));
		spu_ejecucion_proceso ('END', ve_cod_ejecucion_proceso, vl_i);
   END;
END spp_cuota_csr;
/