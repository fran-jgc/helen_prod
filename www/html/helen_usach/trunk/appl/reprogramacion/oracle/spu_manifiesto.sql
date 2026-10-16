CREATE OR REPLACE PROCEDURE spu_manifiesto(ve_operacion					VARCHAR2
											,ve_cod_manifiesto			NUMBER := NULL
											,ve_cod_reprogramacion		NUMBER := NULL
											,ve_id_manifiesto			VARCHAR2 := NULL
											,ve_fecha_manifiesto 		VARCHAR2 := NULL
											,ve_fecha_recep_manifiesto 	DATE := NULL
											,ve_estado_manifiesto		NUMBER := NULL)
AS
BEGIN
                IF (ve_operacion = 'INSERT')THEN
                INSERT INTO MANIFIESTO(COD_MANIFIESTO
                                       ,COD_REPROGRAMACION
                                       ,ID_MANIFIESTO
                                       ,FECHA_MANIFIESTO
                                       ,FECHA_RECEP_MANIFIESTO
                                       ,ESTADO_MANIFIESTO)
       							VALUES(manifiesto_sq.NEXTVAL
                                       ,ve_cod_reprogramacion
                                       ,ve_id_manifiesto
                                       ,to_date(ve_fecha_manifiesto)
                                       ,to_date(ve_fecha_recep_manifiesto)
                                       ,ve_estado_manifiesto);
                ELSIF (ve_operacion = 'UPDATE')THEN
					UPDATE	MANIFIESTO
                    SET		FECHA_RECEP_MANIFIESTO = ve_fecha_recep_manifiesto
                    WHERE	COD_REPROGRAMACION = ve_cod_reprogramacion;
                ELSIF (ve_operacion = 'DELETE') THEN
                    DELETE FROM MANIFIESTO
        WHERE COD_MANIFIESTO = ve_cod_manifiesto;
    END IF;
END;