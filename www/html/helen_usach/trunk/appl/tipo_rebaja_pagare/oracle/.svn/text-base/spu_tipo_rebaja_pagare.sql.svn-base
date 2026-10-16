CREATE OR REPLACE PROCEDURE spu_tipo_rebaja_pagare (
   ve_operacion     IN       VARCHAR2
 , ve_cod_tipo_rebaja_pagare       IN       NUMBER   := NULL
 , ve_nom_tipo_rebaja_pagare       IN       VARCHAR2 := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO tipo_rebaja_pagare
                  (cod_tipo_rebaja_pagare
                  ,nom_tipo_rebaja_pagare
                  )
           VALUES (tipo_rebaja_pagare_sq.NEXTVAL
                  ,ve_nom_tipo_rebaja_pagare
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE tipo_rebaja_pagare
         SET nom_tipo_rebaja_pagare = ve_nom_tipo_rebaja_pagare
       WHERE cod_tipo_rebaja_pagare = ve_cod_tipo_rebaja_pagare;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM tipo_rebaja_pagare
            WHERE cod_tipo_rebaja_pagare = ve_cod_tipo_rebaja_pagare;
   END IF;
END;
/