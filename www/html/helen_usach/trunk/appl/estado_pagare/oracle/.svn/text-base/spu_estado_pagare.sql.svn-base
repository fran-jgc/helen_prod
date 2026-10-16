CREATE OR REPLACE PROCEDURE spu_estado_pagare (
   ve_operacion               IN       VARCHAR
 , ve_cod_estado_pagare       IN       NUMERIC
 , ve_nom_estado_pagare       IN       VARCHAR := NULL
)
AS
BEGIN
   IF (ve_operacion = 'INSERT')
   THEN
      INSERT INTO estado_pagare
                  (cod_estado_pagare
                 , nom_estado_pagare
                  )
           VALUES (estado_pagare_sq.NEXTVAL
                 , ve_nom_estado_pagare
                  );
   ELSIF (ve_operacion = 'UPDATE')
   THEN
      UPDATE estado_pagare
         SET nom_estado_pagare = ve_nom_estado_pagare
       WHERE cod_estado_pagare = ve_cod_estado_pagare;
   ELSIF (ve_operacion = 'DELETE')
   THEN
      DELETE FROM estado_pagare
            WHERE cod_estado_pagare = ve_cod_estado_pagare;
   END IF;
END;
/