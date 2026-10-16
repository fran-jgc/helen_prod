<?php   
ini_set('display_errors', 'ON');    
    
    $user = '171884721';
    $password='QXgQoRdfAesN';
    $frequencyCode ='DAILY';
    
    $wsdl="https://si3.bcentral.cl/sietews/sietews.asmx?wsdl";
        
    $client = new soapclient($wsdl);
    $params = new stdClass;
    $params->user = $user;
    $params->password = $password;
    $params->frequencyCode = $frequencyCode;
    $result = $client->SearchSeries($params)->SearchSeriesResult;
    
        foreach ($result->SeriesInfos->internetSeriesInfo as $serie)
        {
        echo $serie->seriesId . " : " . $serie->spanishTitle . "<br/>";
        }
       
        $seriesIds = array ("F073.UFF.PRE.Z.D");
        $firstDate = "2023-04-10";
        $lastDate = "2023-04-10";
        
    $client = new soapclient($wsdl);
    $params = new stdClass;
    $params->user = $user;
    $params->password = $password;
    $params->firstDate = $firstDate;
    $params->lastDate = $lastDate;
    $params->seriesIds = $seriesIds;

       $result = $client->GetSeries($params)->GetSeriesResult;

        $fameSeries =$result->Series->fameSeries;
        //Cuando se solicita una sola serie, la respuesta no es interpretada como un arreglo
    
        if (is_array($fameSeries ) != 1) $fameSeries = array($fameSeries);
            foreach ($fameSeries as $serieFame)
            {
                echo $serieFame->seriesKey->seriesId . " : " . $serieFame->precision . "<br/>";
        foreach ($serieFame->obs as $obs)
        {
            echo $obs->indexDateString . " : " . $obs->value . "<br/>";
        }
    }

  return $fameSeries->obs->value;

?>