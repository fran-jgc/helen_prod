CREATE OR REPLACE PROCEDURE spu_tipo_deuda
(
   VE_OPERACION            IN       VARCHAR2
 , VE_COD_TIPO_DEUDA       IN       VARCHAR2
 , VE_NOM_TIPO_DEUDA       IN       VARCHAR2 := NULL
 , VE_COD_MONEDA           IN       NUMBER   := NULL
 , VE_COD_FAMILIA_DEUDA    IN       NUMBER   := NULL
 , VE_CANT_CUOTA           IN       NUMBER   := NULL
 , VE_MONTO_NO_COBRABLE    IN       NUMBER   := NULL
 , VE_TASA_INTERES_CREDITO IN       NUMBER   := NULL
 , VE_TASA_INTERES_PENAL   IN       NUMBER   := NULL
 , VE_PERIODO_GRACIA       IN       NUMBER   := NULL
 , VE_CONVENIO_FUTURA      IN       NUMBER   := NULL
 , VE_CONVENIO_NO_EXIGIBLE IN       NUMBER   := NULL
 , VE_CONVENIO_PRONTO_PAGO IN       NUMBER   := NULL
)
AS
BEGIN
	DECLARE
      vl_count_futura                         NUMERIC;
      vl_count_no_exigible                    NUMERIC;
      vl_count_pronto_pago                    NUMERIC;
	BEGIN
	   IF (VE_OPERACION = 'INSERT')  THEN
	      INSERT INTO TIPO_DEUDA
	                  (COD_TIPO_DEUDA
	                 , NOM_TIPO_DEUDA
	                 , COD_MONEDA
	                 , COD_FAMILIA_DEUDA
	                 , CANT_CUOTA
	                 , MONTO_NO_COBRABLE
	                 , TASA_INTERES_CREDITO
	                 , TASA_INTERES_PENAL
	                 , PERIODO_GRACIA
	
	                  )
	           VALUES (
	                   VE_COD_TIPO_DEUDA
	                 , VE_NOM_TIPO_DEUDA
	                 , VE_COD_MONEDA
	                 , VE_COD_FAMILIA_DEUDA
	                 , VE_CANT_CUOTA
	                 , VE_MONTO_NO_COBRABLE
	                 , VE_TASA_INTERES_CREDITO
	                 , VE_TASA_INTERES_PENAL
	                 , VE_PERIODO_GRACIA
	                  );
	        INSERT INTO convenio_bdd( cod_convenio_bdd
	                                  ,cod_tipo_deuda
	                                  ,ano
	                                  ,convenio
	                                  ,con_declaracion)
	        VALUES                  (convenio_sq.NEXTVAL
	                                ,ve_cod_tipo_deuda
	                                ,9998
	                                ,ve_convenio_no_exigible
	                                ,'N');
	        INSERT INTO convenio_bdd( cod_convenio_bdd
	                                  ,cod_tipo_deuda
	                                  ,ano
	                                  ,convenio
	                                  ,con_declaracion)
	        VALUES                  (convenio_sq.NEXTVAL
	                                ,ve_cod_tipo_deuda
	                                ,9999
	                                ,ve_convenio_futura
	                                ,'N');
	        INSERT INTO convenio_bdd( cod_convenio_bdd
	                                  ,cod_tipo_deuda
	                                  ,ano
	                                  ,convenio
	                                  ,con_declaracion)
	        VALUES                  (convenio_sq.NEXTVAL
	                                ,ve_cod_tipo_deuda
	                                ,9997
	                                ,ve_convenio_pronto_pago
	                                ,'N');
	
	
	   ELSIF (VE_OPERACION = 'UPDATE') THEN
	         UPDATE TIPO_DEUDA
	         SET   NOM_TIPO_DEUDA       = VE_NOM_TIPO_DEUDA
	             , COD_MONEDA           = VE_COD_MONEDA
	             , COD_FAMILIA_DEUDA    = VE_COD_FAMILIA_DEUDA
	             , CANT_CUOTA           = VE_CANT_CUOTA
	             , MONTO_NO_COBRABLE    = VE_MONTO_NO_COBRABLE
	             , TASA_INTERES_CREDITO = VE_TASA_INTERES_CREDITO
	             , TASA_INTERES_PENAL   = VE_TASA_INTERES_PENAL
	             , PERIODO_GRACIA       = VE_PERIODO_GRACIA
	         WHERE COD_TIPO_DEUDA       = VE_COD_TIPO_DEUDA;
	
	         SELECT	Count(*)
			 INTO	vl_count_futura --9999) CONVENIO_FUTURA
			 FROM	CONVENIO_BDD
			 WHERE	COD_TIPO_DEUDA = ve_cod_tipo_deuda
			 AND	ANO = 9999; 
	
			 IF(vl_count_futura = 0)THEN
				INSERT INTO convenio_bdd( cod_convenio_bdd
	                                  ,cod_tipo_deuda
	                                  ,ano
	                                  ,convenio
	                                  ,con_declaracion)
				VALUES               (convenio_sq.NEXTVAL
	                                 ,ve_cod_tipo_deuda
	                                 ,9999
	                                 ,ve_convenio_futura
	                                 ,'N');
			 ELSE
				UPDATE	CONVENIO_BDD
				SET		CONVENIO        = ve_convenio_futura
				WHERE	COD_TIPO_DEUDA  = ve_cod_tipo_deuda
				  AND	ANO = 9999;
			 END IF;
			 
			 SELECT	Count(*)
			 INTO	vl_count_no_exigible --9998) CONVENIO_NO_EXIGIBLE
			 FROM	CONVENIO_BDD
			 WHERE	COD_TIPO_DEUDA = ve_cod_tipo_deuda
			 AND	ANO = 9998; 
	
			 IF(vl_count_no_exigible = 0)THEN
				INSERT INTO convenio_bdd( cod_convenio_bdd
	                                  ,cod_tipo_deuda
	                                  ,ano
	                                  ,convenio
	                                  ,con_declaracion)
				VALUES              (convenio_sq.NEXTVAL
	                                ,ve_cod_tipo_deuda
	                                ,9998
	                                ,ve_convenio_no_exigible
	                                ,'N');
			 ELSE
				UPDATE	CONVENIO_BDD
				SET		CONVENIO        = ve_convenio_no_exigible
				WHERE	COD_TIPO_DEUDA  = ve_cod_tipo_deuda
				  AND	ANO  = 9998;
			 END IF;
	
			 SELECT	Count(*)
			 INTO	vl_count_pronto_pago --9997) CONVENIO_PRONTO_PAGO
			 FROM	CONVENIO_BDD
			 WHERE	COD_TIPO_DEUDA = ve_cod_tipo_deuda
			 AND	ANO  = 9997; 
	
			 IF(vl_count_pronto_pago = 0)THEN
				INSERT INTO convenio_bdd( cod_convenio_bdd
	                                  ,cod_tipo_deuda
	                                  ,ano
	                                  ,convenio
	                                  ,con_declaracion)
				VALUES             (convenio_sq.NEXTVAL
	                                ,ve_cod_tipo_deuda
	                                ,9997
	                                ,ve_convenio_pronto_pago
	                                ,'N');
			 ELSE
				UPDATE	CONVENIO_BDD
				SET		CONVENIO       = ve_convenio_pronto_pago
				WHERE	COD_TIPO_DEUDA = ve_cod_tipo_deuda
				  AND	ANO  = 9997;
			 END IF;
	
	   ELSIF (VE_OPERACION = 'DELETE')   THEN
	            DELETE CUENTA_TIPO_DEUDA
	            WHERE  COD_TIPO_DEUDA = VE_COD_TIPO_DEUDA;
	            DELETE TIPO_DEUDA
	            WHERE COD_TIPO_DEUDA = VE_COD_TIPO_DEUDA;
	   END IF;

	END;
END;
/