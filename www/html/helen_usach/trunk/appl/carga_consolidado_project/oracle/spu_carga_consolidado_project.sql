CREATE OR REPLACE PROCEDURE spu_carga_consolidado_project (
   ve_operacion                IN   VARCHAR2
 , ve_string                   IN   VARCHAR2
)
AS
/*
1 RUT del Deudor Numérico 9
2 Dígito Verificador del RUT Caracter 1
3 Apellido Paterno Caracter 20
4 Apellido Materno Caracter 20
5 Nombres Caracter 20
6 Dirección Caracter 40
7 Comuna Caracter 20
8 Ciudad Caracter 20
9 Teléfono Caracter 15
10 Código U. que aportó los datos Numérico 2
11 Tipo de Deudor Numérico 1
12 Deuda Total Consolidada Numérico 7 (3Ent/4Dec)
13 Año de la Consolidación Numérico 4 (aaaa)
14 Situación Determinada Numérico 1
15 Código U.1 que cobra Numérico 2
16 Código U.2 que cobra Numérico 2
17 Año inicio de cobro Numérico 4 (aaaa)
18 Año último crédito Numérico 4 (aaaa)
19 Año última matrícula Numérico 4 (aaaa)
20 1-Código Universidad con Deuda Numérico 2
21 1-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
22 1-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
23 1-Código de error Caracter 1
24 2-Código Universidad con Deuda Numérico 2
25 2-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
26 2-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
27 2-Código de error Caracter 1
28 3-Código Universidad con Deuda Numérico 2
29 3-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
30 3-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
31 3-Código de error Caracter 1
32 4-Código Universidad con Deuda Numérico 2
33 4-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
34 4-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
35 4-Código de error Caracter 1
36 5-Código Universidad con Deuda Numérico 2
37 5-Monto de la Deuda Consolidada Numérico 7 (3Ent/4Dec)
38 5-Porcentaje para recaudación Numérico 6 (3Ent/3Dec)
39 5-Código de error Caracter 1
40 Indicador de Error Caracter 1
41 Tipo de Cuenta Numérico 1
*/
BEGIN
  DECLARE
    vl_rut                        NUMBER(9);
    vl_dig_verif                  VARCHAR2(1);
    vl_apellido_paterno           VARCHAR2(20);
    vl_apellido_materno           VARCHAR2(20);
    vl_nombre                     VARCHAR2(20);
    vl_direccion                  VARCHAR2(40);
    vl_comuna                     VARCHAR2(20);
    vl_ciudad                     VARCHAR2(20);
    vl_telefono                   VARCHAR2(15);
    vl_cod_universidad            NUMBER(2);
    vl_tipo_deudor                NUMBER(1);
    vl_deuda_total                NUMBER(7,4);
    vl_ano_consolidacion          NUMBER(4);
    vl_situacion_determinada      NUMBER(1);
    vl_cod_universidad_cobra1     NUMBER(2);
    vl_cod_universidad_cobra2     NUMBER(2);
    vl_ano_inicio_cobro           NUMBER(4);
    vl_ano_ultimo_credito         NUMBER(4);
    vl_ano_ultima_matricula       NUMBER(4);
    vl_cod_universidad_deuda1     NUMBER(2);
    vl_monto_deuda_consolidada1   NUMBER(7,4);
    vl_porc_recauda1              NUMBER(6,3);
    vl_cod_error1                 VARCHAR2(1);
    vl_cod_universidad_deuda2     NUMBER(2);
    vl_monto_deuda_consolidada2   NUMBER(7,4);
    vl_porc_recauda2              NUMBER(6,3);
    vl_cod_error2                 VARCHAR2(1);
    vl_cod_universidad_deuda3     NUMBER(2);
    vl_monto_deuda_consolidada3   NUMBER(7,4);
    vl_porc_recauda3              NUMBER(6,3);
    vl_cod_error3                 VARCHAR2(1);
    vl_cod_universidad_deuda4     NUMBER(2);
    vl_monto_deuda_consolidada4   NUMBER(7,4);
    vl_porc_recauda4              NUMBER(6,3);
    vl_cod_error4                 VARCHAR2(1);
    vl_cod_universidad_deuda5     NUMBER(2);
    vl_monto_deuda_consolidada5   NUMBER(7,4);
    vl_porc_recauda5              NUMBER(6,3);
    vl_cod_error5                 VARCHAR2(1);
    vl_indicador_error            VARCHAR2(1);
    vl_tipo_cuenta                NUMBER(1);

BEGIN
  IF (ve_operacion = 'INSERT') THEN
    vl_rut                      := To_Number(SubStr(ve_string, 1 , 9));
    vl_dig_verif                := SubStr(ve_string, 10 , 1);
    vl_apellido_paterno         := SubStr(ve_string, 11 , 20);
    vl_apellido_materno         := SubStr(ve_string, 31 , 20);
    vl_nombre                   := SubStr(ve_string, 51 , 20);
    vl_direccion                := SubStr(ve_string, 71 , 40);
    vl_comuna                   := SubStr(ve_string, 111 , 20);
    vl_ciudad                   := SubStr(ve_string, 131 , 20);
    vl_telefono                 := SubStr(ve_string, 151 , 15);
    vl_cod_universidad          := To_Number(SubStr(ve_string, 166 , 2));
    vl_tipo_deudor              := To_Number(SubStr(ve_string, 168 , 1));
    vl_deuda_total              := To_Number(SubStr(ve_string, 169 , 3)||'.'||SubStr(ve_string, 172 , 4));
    vl_ano_consolidacion        := To_Number(SubStr(ve_string, 176 , 4));
    vl_situacion_determinada    := To_Number(SubStr(ve_string, 180 , 1));
    vl_cod_universidad_cobra1   := To_Number(SubStr(ve_string, 181 , 2));
    vl_cod_universidad_cobra2   := To_Number(SubStr(ve_string, 183 , 2));
    vl_ano_inicio_cobro         := To_Number(SubStr(ve_string, 185 , 4));
    vl_ano_ultimo_credito       := To_Number(SubStr(ve_string, 189 , 4));
    vl_ano_ultima_matricula     := To_Number(SubStr(ve_string, 193 , 4));
    vl_cod_universidad_deuda1   := To_Number(SubStr(ve_string, 197 , 2));
    vl_monto_deuda_consolidada1 := To_Number(SubStr(ve_string, 199 , 3)||'.'||SubStr(ve_string, 202 , 4));
    vl_porc_recauda1            := To_Number(SubStr(ve_string, 206 , 3)||'.'||SubStr(ve_string, 209 , 3));
    vl_cod_error1               := SubStr(ve_string, 212 , 1);
    vl_cod_universidad_deuda2   := To_Number(SubStr(ve_string, 213 , 2));
    vl_monto_deuda_consolidada2 := To_Number(SubStr(ve_string, 215 , 3)||'.'||SubStr(ve_string, 218 , 4));
    vl_porc_recauda2            := To_Number(SubStr(ve_string, 222 , 3)||'.'||SubStr(ve_string, 225 , 3));
    vl_cod_error2               := SubStr(ve_string, 228 , 1);
    vl_cod_universidad_deuda3   := To_Number(SubStr(ve_string, 229 , 2));
    vl_monto_deuda_consolidada3 := To_Number(SubStr(ve_string, 231 , 3)||'.'||SubStr(ve_string, 234 , 4));
    vl_porc_recauda3            := To_Number(SubStr(ve_string, 238 , 3)||'.'||SubStr(ve_string, 241 , 3));
    vl_cod_error3               := SubStr(ve_string, 244 , 1);
    vl_cod_universidad_deuda4   := To_Number(SubStr(ve_string, 245 , 2));
    vl_monto_deuda_consolidada4 := To_Number(SubStr(ve_string, 247 , 3)||'.'||SubStr(ve_string, 250 , 4));
    vl_porc_recauda4            := To_Number(SubStr(ve_string, 254 , 3)||'.'||SubStr(ve_string, 257 , 3));
    vl_cod_error4               := SubStr(ve_string, 260 , 1);
    vl_cod_universidad_deuda5   := To_Number(SubStr(ve_string, 261 , 2));
    vl_monto_deuda_consolidada5 := To_Number(SubStr(ve_string, 263 , 3)||'.'||SubStr(ve_string, 266 , 4));
    vl_porc_recauda5            := To_Number(SubStr(ve_string, 270 , 3)||'.'||SubStr(ve_string, 273 , 3));
    vl_cod_error5               := SubStr(ve_string, 276 , 1);
    vl_indicador_error          := SubStr(ve_string, 277 , 1);
    vl_tipo_cuenta              := To_Number(SubStr(ve_string, 278 , 1));

    INSERT INTO carga_consolidado_project
              (cod_carga_consolidado_project
              , rut
              , dig_verif
              , apellido_paterno
              , apellido_materno
              , nombre
              , direccion
              , comuna
              , ciudad
              , telefono
              , cod_universidad
              , tipo_deudor
              , deuda_total
              , ano_consolidacion
              , situacion_determinada
              , cod_universidad_cobra1
              , cod_universidad_cobra2
              , ano_inicio_cobro
              , ano_ultimo_credito
              , ano_ultima_matricula
              , cod_universidad_deuda1
              , monto_deuda_consolidada1
              , porc_recauda1
              , cod_error1
              , cod_universidad_deuda2
              , monto_deuda_consolidada2
              , porc_recauda2
              , cod_error2
              , cod_universidad_deuda3
              , monto_deuda_consolidada3
              , porc_recauda3
              , cod_error3
              , cod_universidad_deuda4
              , monto_deuda_consolidada4
              , porc_recauda4
              , cod_error4
              , cod_universidad_deuda5
              , monto_deuda_consolidada5
              , porc_recauda5
              , cod_error5
              , indicador_error
              , tipo_cuenta
              )
    VALUES(   carga_consolidado_project_sq.NEXTVAL
              ,vl_rut
              ,vl_dig_verif
              ,vl_apellido_paterno
              ,vl_apellido_materno
              ,vl_nombre
              ,vl_direccion
              ,vl_comuna
              ,vl_ciudad
              ,vl_telefono
              ,vl_cod_universidad
              ,vl_tipo_deudor
              ,vl_deuda_total
              ,vl_ano_consolidacion
              ,vl_situacion_determinada
              ,vl_cod_universidad_cobra1
              ,vl_cod_universidad_cobra2
              ,vl_ano_inicio_cobro
              ,vl_ano_ultimo_credito
              ,vl_ano_ultima_matricula
              ,vl_cod_universidad_deuda1
              ,vl_monto_deuda_consolidada1
              ,vl_porc_recauda1
              ,vl_cod_error1
              ,vl_cod_universidad_deuda2
              ,vl_monto_deuda_consolidada2
              ,vl_porc_recauda2
              ,vl_cod_error2
              ,vl_cod_universidad_deuda3
              ,vl_monto_deuda_consolidada3
              ,vl_porc_recauda3
              ,vl_cod_error3
              ,vl_cod_universidad_deuda4
              ,vl_monto_deuda_consolidada4
              ,vl_porc_recauda4
              ,vl_cod_error4
              ,vl_cod_universidad_deuda5
              ,vl_monto_deuda_consolidada5
              ,vl_porc_recauda5
              ,vl_cod_error5
              ,vl_indicador_error
              ,vl_tipo_cuenta);
  END IF;
END;
END;