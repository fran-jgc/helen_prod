<?php

namespace segic\cps\components;

class CommonCall
{
    private $_serverUrl;
    private $_options;

    const TIPO_WEBPAY = 'webpay';
    const TIPO_SERVIPAG = 'servipag';
    const TIPO_GETNET = 'getnet';

    function __construct($serverURL, $options = [])
    {
        $this->_serverUrl = $serverURL;
        $this->_options = $options;
    }

    /**
     * Llamadas XML con parametros para el pago
     * @param $data
     * @param $url
     * @return mixed
     */
    private function xmlCall($data, $url)
    {

        $result = [];
        $dom = new \SimpleXMLElement('<cps></cps>');
        foreach ($data as $key => $value) {
            $dom->addChild($key, $value);
        }
        $xml = $dom->asXML();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_VERBOSE, false);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/xml', 'Content-Length: ' . strlen($xml)]);

        $result["data"] = curl_exec($curl);
        $result["error"] = curl_error($curl);
        curl_close($curl);

        return $result;
    }

    /**
     * Save current pay in DB
     * @param type $model
     * @param type $result
     * @param type $userID
     */
    public function saveSessionAsDB($model, $result)
    {

        $currentPago = new $model();
        $currentPago->num_trans = $result['idTransaction'];
        $currentPago->user_id = $result['user'];
        $currentPago->monto = $result['monto'];
        $currentPago->fec_trans = date('Y-m-d H:i:s');
        $currentPago->ip_cliente = (!empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : ((!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']));
        $currentPago->est_pago = 'PD';

        $currentPago->save();
    }

    /**
     * 
     * @param string $monto
     * @param string $storeId
     * @param string $urlFinal
     * @param string $error
     * @return array
     */
    public function initialisationWebPay($monto, $storeId, $urlFinal, &$error)
    {

        $result = [];
        $result['tipo'] = 'TR';
        $params = ['storeId' => $storeId, 'monto' => $monto, 'urlFinal' => $urlFinal];
        $url = $this->_serverUrl . ($this->_options["slugInitWebpay"] ?? '/webpay/initialisation');
        $resultCall = $this->xmlCall($params, $url);

        if (empty($resultCall["error"]) && !empty($resultCall["data"]) && $xmlResult = simplexml_load_string($resultCall["data"])) {

            if (!isset($xmlResult->idTransaction)) {

                $result['success'] = false;
                $result['message'] = isset($xmlResult->message) ? (string) $xmlResult->message : "Error Webpay";
                $error = $this->internalError($xmlResult);
            } else {

                $result['success'] = true;
                $result['url'] = (string) $xmlResult->url;
                $result['token'] = (string) $xmlResult->token;
                $result['monto'] = (string) $xmlResult->monto;
                $result['idTransaction'] = (string) $xmlResult->idTransaction;
            }
        } else {

            $result['success'] = false;
            $result['message'] = 'Error durante el inicio del pago Webpay';
            $error = $resultCall["error"];
        }

        return $result;
    }

    /**
     * 
     * @param string $monto
     * @param string $storeId
     * @param string $urlFinal
     * @param string $error
     * @return array
     */
    public function initialisationServipag($monto, $storeId, $urlFinal, &$error)
    {

        $result = [];
        $result['tipo'] = 'SP';
        $params = ['storeId' => $storeId, 'monto' => $monto, 'urlFinal' => $urlFinal];
        $url = $this->_serverUrl . ($this->_options["slugInitServipag"] ?? '/servipag/initialisation');
        $resultCall = $this->xmlCall($params, $url);

        if (empty($resultCall["error"]) && !empty($resultCall["data"]) && $xmlResult = simplexml_load_string($resultCall["data"])) {

            if (!isset($xmlResult->idTransaction)) {

                $result['success'] = false;
                $result['message'] = isset($xmlResult->message) ? (string) $xmlResult->message : "Error Servipag";
                $error = $this->internalError($xmlResult);
            } else {

                $result['success'] = true;
                $result['xml'] = (string) $xmlResult->Servipag->asXML();
                $result['idTransaction'] = (string) $xmlResult->idTransaction;
                $result['monto'] = (string) $xmlResult->monto;
            }
        } else {

            $result['success'] = false;
            $result['message'] = 'Error durante el inicio del pago Servipag';
            $error = $resultCall["error"];
        }

        return $result;
    }

    /**
     * 
     * @param string $monto
     * @param string $storeId
     * @param string $urlFinal
     * @param string $error
     * @return array
     */
    public function initialisationGetNet($monto, $storeId, $urlFinal, &$error)
    {
        $result = [];
        $result['tipo'] = 'GN';
        $params = ['storeId' => $storeId, 'monto' => $monto, 'urlFinal' => $urlFinal];
        $url = $this->_serverUrl . ($this->_options["slugInitGetnet"] ?? '/getnet/initialisation');
        $resultCall = $this->xmlCall($params, $url);

        if (empty($resultCall["error"]) && !empty($resultCall["data"]) && $xmlResult = simplexml_load_string($resultCall["data"])) {

            if (!isset($xmlResult->idTransaction)) {

                $result['success'] = false;
                $result['message'] = isset($xmlResult->message) ? (string) $xmlResult->message : "Error GetNet";
                $error = $this->internalError($xmlResult);
            } else {

                $result['success'] = true;
                $result['url'] = (string) $xmlResult->url;
                $result['monto'] = (string) $xmlResult->monto;
                $result['idTransaction'] = (string) $xmlResult->idTransaction;
            }
        } else {

            $result['success'] = false;
            $result['message'] = 'Error durante el inicio del pago GetNet';
            $error = $resultCall["error"];
        }

        return $result;
    }

    /**
     * 
     * @param type $transactionId
     * @param type $error
     * @return type
     */
    public function result($transactionId, &$error)
    {
        $url = $this->_serverUrl . ($this->_options["slugResult"] ?? '/default/get-transaction-detail');
        $resultCall = $this->xmlCall(['numTransaction' => $transactionId], $url);

        if (empty($resultCall["error"]) && !empty($resultCall["data"])) {

            try {
                return json_decode($resultCall["data"], true);
            } catch (\Exception $ex) {
                $error = $ex->getMessage();
            }
        } else {
            $error = $resultCall["error"];
        }
    }

    /**
     * 
     * @param type $xmlResult
     * @return string
     */
    private function internalError($xmlResult)
    {

        if (isset($xmlResult->internalMessage) && !empty($xmlResult->internalMessage)) {
            return (string) $xmlResult->internalMessage;
        } elseif (isset($xmlResult->message) && !empty($xmlResult->message)) {
            return (string) $xmlResult->message;
        } else {
            return "Error desconocido";
        }
    }
}
