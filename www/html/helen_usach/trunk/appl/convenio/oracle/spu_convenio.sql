PROMPT CREATE OR REPLACE PROCEDURE spu_convenio
CREATE OR REPLACE PROCEDURE spu_convenio (ve_operacion					VARCHAR2
									                        ,ve_cod_convenio                  NUMBER
									                        ,ve_cod_usuario                   NUMBER := NULL
									                        ,ve_cod_estado_convenio           NUMBER := NULL
									                        ,ve_obs                           VARCHAR2 := NULL
									                        ,ve_cod_deuda                     NUMBER := NULL
									                        ,ve_porc_interes_convenio         NUMBER := NULL
									                        ,ve_porc_pago_inicial		          NUMBER := NULL
									                        ,ve_pago_inicial_sugerido         NUMBER := NULL
									                        ,ve_pago_inicial                  NUMBER := NULL
									                        ,ve_monto_convenio			          NUMBER := NULL
									                        ,ve_monto_convenio_moneda         NUMBER := NULL
									                        ,ve_monto_convenio_act		        NUMBER := NULL
									                        ,ve_fecha_inicio_cobro		        DATE := NULL
									                        ,ve_cant_cuota                    NUMBER := NULL
									                        ,ve_cuota_mensual_moneda          NUMBER := NULL
									                        ,ve_autorizado				   	  VARCHAR2 := 'N'
 )
AS
BEGIN
	declare
		 K_EST_CONV_EMITIDA			  NUMBER;
		 K_EST_CONV_CONFIRMADA		NUMBER;
		 K_EST_CONV_ANULADA		    NUMBER;
		 K_EST_PP_EN_MORA			    NUMBER;
		 K_EST_PP_EN_CONVENIO		  NUMBER;
		 vl_cod_estado_ant			  NUMBER;
		 vc_cod_plan_pago			    NUMBER;
		 vc_cod_estado_plan_pago	NUMBER;

     CURSOR C_PP(vc_cod_plan_pago NUMBER, vc_cod_estado_plan_pago number) IS
		    SELECT  ppc.cod_plan_pago
				        ,pp.cod_estado_plan_pago
		    FROM  plan_pago_convenio ppc, plan_pago pp
		    WHERE ppc.cod_convenio = ve_cod_convenio
		      and pp.cod_plan_pago = ppc.cod_plan_pago;

     CURSOR C_PP2(vc_cod_plan_pago NUMBER) IS
		    SELECT  cod_plan_pago
		    FROM  plan_pago_convenio
		      WHERE cod_convenio = ve_cod_convenio;



     BEGIN

	    K_EST_CONV_EMITIDA	:= 1;
	    K_EST_CONV_CONFIRMADA	:= 2;
	    K_EST_CONV_ANULADA	:= 3;

	    K_EST_PP_EN_MORA := 4;
	    K_EST_PP_EN_CONVENIO := 9;

	          IF (ve_operacion = 'INSERT')THEN
		          INSERT INTO CONVENIO
		             (COD_CONVENIO
		              ,FECHA_CONVENIO
			          ,COD_USUARIO
			          ,COD_ESTADO_CONVENIO
			          ,OBS
			          ,COD_DEUDA
			          ,PORC_INTERES_CONVENIO
			          ,PORC_PAGO_INICIAL
			          ,PAGO_INICIAL_SUGERIDO
			          ,PAGO_INICIAL
			          ,MONTO_CONVENIO
			          ,MONTO_CONVENIO_MONEDA
			          ,MONTO_CONVENIO_ACT
			          ,FECHA_INICIO_COBRO
			          ,CANT_CUOTA
			          ,CUOTA_MENSUAL_MONEDA
			          ,AUTORIZADO
			          )
		          VALUES
			          (convenio_sq.NEXTVAL
			          ,sysdate
			          ,ve_cod_usuario
			          ,ve_cod_estado_convenio
			          ,ve_obs
			          ,ve_cod_deuda
			          ,ve_porc_interes_convenio
			          ,ve_porc_pago_inicial
			          ,ve_pago_inicial_sugerido
			          ,ve_pago_inicial
			          ,ve_monto_convenio
			          ,ve_monto_convenio_moneda
			          ,ve_monto_convenio_act
			          ,ve_fecha_inicio_cobro
			          ,ve_cant_cuota
			          ,ve_cuota_mensual_moneda
			          ,ve_autorizado
			          );
	          ELSIF(ve_operacion = 'UPDATE') then
		          select COD_ESTADO_CONVENIO
              INTO vl_cod_estado_ant
		          from CONVENIO
		          WHERE	COD_CONVENIO = ve_cod_convenio;

		          UPDATE	CONVENIO
		          set COD_ESTADO_CONVENIO = ve_cod_estado_convenio
			          ,OBS = ve_obs
			          ,COD_DEUDA = ve_cod_deuda
			          ,PORC_PAGO_INICIAL = ve_porc_pago_inicial
			          ,PAGO_INICIAL_SUGERIDO = ve_pago_inicial_sugerido
			          ,PAGO_INICIAL = ve_pago_inicial
			          ,MONTO_CONVENIO = ve_monto_CONVENIO
			          ,MONTO_CONVENIO_MONEDA = ve_monto_CONVENIO_moneda
			          ,FECHA_INICIO_COBRO = ve_fecha_inicio_cobro
			          ,MONTO_CONVENIO_ACT = ve_monto_CONVENIO_act
			          ,CANT_CUOTA = ve_cant_cuota
			          ,CUOTA_MENSUAL_MONEDA = ve_cuota_mensual_moneda
			          ,AUTORIZADO = ve_autorizado
		          WHERE	COD_CONVENIO = ve_cod_convenio;

		          IF(vl_cod_estado_ant <> ve_cod_estado_convenio) THEN
			          IF(ve_cod_estado_convenio = K_EST_CONV_CONFIRMADA) then
				          spu_convenio('CONFIRMA', ve_cod_convenio);
			          ELSIF(vl_cod_estado_ant = K_EST_CONV_CONFIRMADA  and ve_cod_estado_convenio = K_EST_CONV_ANULADA) THEN
				            spu_convenio('ANULA', ve_cod_convenio);
		            END IF;
		          END IF;
	            ELSIF(ve_operacion = 'DELETE') THEN
		            delete PLAN_PAGO_CONVENIO
		            WHERE	COD_CONVENIO = ve_cod_convenio;

		            DELETE CONVENIO
		            WHERE	COD_CONVENIO = ve_cod_convenio;
	            ELSIF(ve_operacion = 'CONFIRMA') THEN
		              -- cambia el estado de las cuotas a EN CONVENIO
					SELECT  cod_plan_pago
				    INTO 	vc_cod_plan_pago
				    FROM  plan_pago_convenio
				    WHERE cod_convenio = ve_cod_convenio;
				      
                  FOR a IN C_PP2(vc_cod_plan_pago) LOOP
                     sp_pp_update_estado(vc_cod_plan_pago, K_EST_PP_EN_CONVENIO);
                  END LOOP;
	          ELSIF(ve_operacion = 'ANULA') THEN
		          -- cambia el estado de las cuotas a EN CONVENIO
		          FOR a IN C_PP(vc_cod_plan_pago, vc_cod_estado_plan_pago) LOOP

		            IF(vc_cod_estado_plan_pago = K_EST_PP_EN_CONVENIO) THEN
				          sp_pp_update_estado(vc_cod_plan_pago, K_EST_PP_EN_MORA);
                END IF;

			        END LOOP;
            END IF;
	END;
END;
/

