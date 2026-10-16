CREATE OR REPLACE PROCEDURE spu_carga_orsan_usach(ve_operacion 			IN  VARCHAR
												,ve_cod_carga_orsan		IN  NUMBER  := NULL
												,ve_cod_usuario			IN  NUMBER  := NULL 
												,ve_fecha_carga_orsan	IN	DATE	:= NULL
												,ve_total_reg_carga		IN  NUMBER  := NULL
												,ve_total_reg_exitoso	IN  NUMBER  := NULL
												,ve_total_reg_pdte		IN  NUMBER  := NULL
												,ve_cod_origen_pago		IN  NUMBER  := NULL)	

AS
BEGIN
  DECLARE
    K_ESTADO_SOLICITADA           NUMBER:= 1;
    
			
	BEGIN
		if(ve_operacion = 'INSERT') then 
			INSERT INTO CARGA_ORSAN_USACH
					   (COD_CARGA_ORSAN_USACH
					   ,COD_USUARIO
					   ,FECHA_CARGA_ORSAN_USACH
					   ,TOTAL_REG_CARGA
					   ,TOTAL_REG_EXITOSO
					   ,TOTAL_REG_PDTE
					   ,MONTO_REG_CARGA
					   ,MONTO_REG_EXITOSO
					   ,MONTO_REG_PDTE
					   ,FECHA_REGISTRO)
				 VALUES(carga_orsan_usach_sq.NEXTVAL
					    ,ve_cod_usuario
						,sysdate
						,0
						,0
						,0
						,0
						,0
						,0
						,sysdate);
		end if;		
	END;
END;