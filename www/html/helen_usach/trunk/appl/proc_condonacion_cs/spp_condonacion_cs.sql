CREATE OR REPLACE PROCEDURE spp_condonacion_cs( ve_cod_ejecucion_proceso   NUMBER)
AS
/******************************************************************************
  spp_condonacion_cs
  Busca todos los deudores que cumplen condiciones para accedeer a la condonación CS por el cumplimiento de los años plazo
  para cada deudor crea un registro en la tabla condonacion_cs, el cual debe ser procesado posteriormente

  Se debe correr despues del vencimiento de la cuota en mayo, es decir desde 1 de junio en adelante

******************************************************************************/
BEGIN
  DECLARE
    K_DEUDA_EXIGIBLE          NUMBER :=2;
    K_COND_CS_EMITIDA         NUMBER:=1;
    K_COMP_VENCIDA            NUMBER:=2;
    K_COMP_FUTURA             NUMBER:=4;

    vl_count_registros        NUMBER;
    vl_count				  NUMBER;
    i                         NUMBER:=0;
    vl_saldo_vencida          NUMBER;
    vl_saldo_futura           NUMBER;
    vl_cant_pp                NUMBER;
    vl_saldo_99			      NUMBER;

    CURSOR c_deuda IS
      SELECT  cod_deuda
              ,cant_cuota
      FROM    deuda
      WHERE cod_tipo_deuda = 'CS'
        AND cod_estado_deuda = K_DEUDA_EXIGIBLE
        AND cod_deuda NOT IN (select cod_deuda from cruzado where cruzado.cod_deuda = deuda.COD_DEUDA);/*no se agregan los cruzados*/


    BEGIN
      -- calcula el total de registros
      SELECT  Count(*)
      INTO vl_count_registros 
      FROM    deuda
      WHERE cod_tipo_deuda = 'CS'
        AND cod_estado_deuda = K_DEUDA_EXIGIBLE;
      spu_ejecucion_proceso('SET_TOTAL', ve_cod_ejecucion_proceso, vl_count_registros);

      -- recorre todos los candidadtos
      FOR a IN c_deuda LOOP
        -- verifica si el proceso ha sido cancelado
        IF (f_ep_cancela_proceso(ve_cod_ejecucion_proceso) = 'S') THEN
          COMMIT;
          RETURN;
        END IF;
		
        select count(*)
		INTO vl_count
		from plan_pago
		where cod_deuda = a.cod_deuda
		and ano_cobro = 9999;
	  	
		--SI NO TIENE CUOTA 99, SALTARSE AL SIGUIENTE REGISTRO
		IF (vl_count = 0) THEN
		  i := i + 1; 
          CONTINUE;
        END IF;
        
        --ve si tiene saldo 99 a condonar
		select f_pp_saldo_plan_pago(cod_plan_pago, sysdate)
	  	INTO vl_saldo_99
		from plan_pago
		where cod_deuda = a.cod_deuda
		  and ano_cobro = 9999;
	
		--SI NO TIENE SALDO, SALTARSE AL SIGUIENTE REGISTRO
		if (vl_saldo_99 <= 0) THEN
			i := i + 1; 
			CONTINUE;
	  	END IF;
  
        --CUENTA LOS PLANES DE PAGOS EN ESTADO PAGADA, DISTINTO DE SUSPENDIDOS
        SELECT Count(*)
        INTO vl_cant_pp
        FROM plan_pago
        WHERE cod_deuda  = a.cod_deuda
        AND cod_estado_plan_pago <> 6 --suspendida
        AND ano_cobro <> 9999
        AND cod_estado_plan_pago = 3;

        -- cumplieron la cantidad de cuotas 12 o 15 
        IF (vl_cant_pp = a.cant_cuota) THEN 
        
        	SELECT	Count(*)
			INTO	vl_count
			FROM	condonacion_cs
			WHERE	cod_deuda = a.cod_deuda;
        
        	IF (vl_count = 0) THEN
		          vl_saldo_vencida := f_deuda_saldo_componente(a.cod_deuda, K_COMP_VENCIDA, SYSDATE);
		          -- no tienen saldo moroso
		          IF (vl_saldo_vencida = 0) THEN
		            vl_saldo_futura := f_deuda_saldo_componente(a.cod_deuda, K_COMP_FUTURA, SYSDATE);
		            -- tiene saldo futuro que condonar
		            IF (vl_saldo_futura > 0) THEN
		              INSERT INTO condonacion_cs
		                (cod_condonacion_cs
		                ,fecha_condonacion_cs
		                ,cod_estado_condonacion_cs
		                ,cod_deuda
		                ,obs)
		              VALUES
		                (condonacion_cs_sq.NEXTVAL
		                ,SYSDATE
		                ,K_COND_CS_EMITIDA
		                ,a.cod_deuda
		                ,null);
		                
		               i := i + 1; 
		               spu_ejecucion_proceso('SET_ACTUAL', ve_cod_ejecucion_proceso, i); 
		            END IF;
		          END IF;
	        END IF;     
        END IF;
        COMMIT;
        
      END LOOP;

    EXCEPTION
      WHEN OTHERS THEN
        -- Si ocurre un error de la BD al grabar un registro lo registra en el LOG. del sistema
        spu_error_proceso('INSERT', NULL, ve_cod_ejecucion_proceso, 'E', SQLCODE, SUBSTR(SQLERRM,1,100), 'spp_condonacion_cs:...');
        COMMIT;
      END;
END spp_condonacion_cs;
/

