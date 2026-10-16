create global temporary table TEMP_CANDIDATO_CONDONACION
  (RUT				  NUMBER(10,0)
  ,DIG_VERIF          VARCHAR2(1)
  ,COD_TIPO_DEUDA     VARCHAR2(20) 
  ,ANO_INICIO         NUMBER       
  ,MONTO_INICIO_COBRO NUMBER(14,4)  
  ,NOM_ESTADO_DEUDA   VARCHAR2(100) 
  ,NOM_CARTERA        VARCHAR2(100) 
  ,NOM_UNIVERSIDAD    VARCHAR2(100) 
  ,COD_DEUDA          NUMBER(10,0)  
  ,COD_DEUDOR         NUMBER(10,0)  
  ,COD_PLAN_PAGO      NUMBER(10,0)  
  ,CANT_PP            NUMBER       
  ,CANT_CUOTA         NUMBER(10,0)  
  ,ES_CAND_CONDONA    VARCHAR2(1)   
  ,SALDO_99           NUMBER        
  ,EMAIL              VARCHAR2(100) 
  ,ANO_ULTIMO_PP      NUMBER        
)
on commit delete ROWS;