<?php

class ClaveUnicaClient
{
    const AUTHORIZE_URL =
        'https://accounts.claveunica.gob.cl/openid/authorize/';

    const TOKEN_URL =
        'https://accounts.claveunica.gob.cl/openid/token/';

    const USERINFO_URL =
        'https://accounts.claveunica.gob.cl/openid/userinfo/';

    private $clientId;
    private $clientSecret;
    private $redirectUri;

    public function __construct()
    {
        $this->clientId =
            isset($_SERVER['CLAVEUNICA_CLIENT_ID'])
            ? $_SERVER['CLAVEUNICA_CLIENT_ID']
            : '';

        $this->clientSecret =
            isset($_SERVER['CLAVEUNICA_CLIENT_SECRET'])
            ? $_SERVER['CLAVEUNICA_CLIENT_SECRET']
            : '';

        $this->redirectUri =
            isset($_SERVER['CLAVEUNICA_REDIRECT_URI'])
            ? $_SERVER['CLAVEUNICA_REDIRECT_URI']
            : '';

        if (
            $this->clientId === '' ||
            $this->clientSecret === '' ||
            $this->redirectUri === ''
        ) {
            throw new RuntimeException(
                'La configuracion de ClaveUnica esta incompleta.'
            );
        }
    }

    public function getAuthorizationUrl($state)
    {
        $parameters = array(
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'scope' => 'openid run name',
            'redirect_uri' => $this->redirectUri,
            'state' => $state
        );

        return self::AUTHORIZE_URL
            . '?'
            . http_build_query(
                $parameters,
                '',
                '&',
                PHP_QUERY_RFC3986
            );
    }

    public function exchangeCode($code, $state)
    {
        $response = $this->request(
            self::TOKEN_URL,
            array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code',
                'code' => $code,
                'state' => $state
            )
        );

        if (empty($response['access_token'])) {
            throw new RuntimeException(
                'ClaveUnica no entrego un token de acceso.'
            );
        }

        return $response['access_token'];
    }

    public function getUserInfo($accessToken)
    {
        return $this->request(
            self::USERINFO_URL,
            null,
            array(
                'Authorization: Bearer ' . $accessToken
            )
        );
    }

    private function request(
        $url,
        $postData = null,
        $headers = array()
    ) {
        $curl = curl_init($url);

        $headers[] = 'Accept: application/json';

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
                CURLOPT_HTTPHEADER => $headers
            )
        );

        if ($postData !== null) {
            $headers[] =
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt(
                $curl,
                CURLOPT_POSTFIELDS,
                http_build_query(
                    $postData,
                    '',
                    '&',
                    PHP_QUERY_RFC3986
                )
            );
        }

        $body = curl_exec($curl);

        if ($body === false) {
            $message = curl_error($curl);
            curl_close($curl);

            throw new RuntimeException(
                'No fue posible conectar con ClaveUnica: '
                . $message
            );
        }

        $status = curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

        curl_close($curl);

        $data = json_decode($body, true);

        if (
            $status < 200 ||
            $status >= 300 ||
            !is_array($data)
        ) {
            throw new RuntimeException(
                'ClaveUnica respondio con un error HTTP '
                . $status
                . '.'
            );
        }

        return $data;
    }
}