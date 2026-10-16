<?php
require_once 'pest-master/Pest.php';

class api_integra extends Pest {
    public $user = '';
    public $pass = '';
    public $scope = ''; // separados por espacios

    function __construct($url, $u, $p, $s) {
        parent::__construct($url);
        $this->user = $u;
        $this->pass = $p;
        $this->scope = $s;

        $this->throw_exceptions = false;

    }
    function have_error() {
        if ($this->last_response['meta']['http_code'] != 200) {
            $err = array('http_code' => $this->last_response['meta']['http_code']
                        ,'body' => $this->last_response['body']
                        );
            var_dump($this->last_response['meta']['http_code']);
            var_dump($err);
            return true;
        }
        return false;
    }
    function get_header_token() {
        $dataToSend = array(
            'username' => $this->user,
            'password' => $this->pass,
            'scope' => $this->scope
        );
        
        $headers = array(
            "accept" => "application/json",
            "Content-Type" => "application/x-www-form-urlencoded"
        );
        
        $token = $this->post('/token', $dataToSend, $headers);
        
        if ($this->have_error())
            return false;

        $token = json_decode($token, true);
        $token= $token['access_token'];
            
        $headers = array("accept" => "application/json"
                        ,"Content-Type" => "application/json"
                        ,"Authorization" => "Bearer $token"
        );

        return $headers;
    }        
	function build_result_api($sql, $with_columns_types=false) {
        $sql = rawurlencode($sql);
        $headers = $this->get_header_token();
        $result = json_decode($this->get("/judicial/sql/$sql", array("with_columns_types" => $with_columns_types), $headers), true);
		if ($this->have_error())
			return false;
        return $result;
	}
    function get_fields($result) {
        $columns = $result["columns"];
        $types = $result["types"];
        $fields = [];
        for ($i=0; $i < count($columns); $i++) {
            $f = new StdClass();
            $f->name = $columns[$i];

            if (stripos('decimal.Decimal', $types[$i]) !== false || stripos('int', $types[$i]) !== false)
                $f->numeric = 1;
            elseif (stripos('str', $types[$i]) !== false)
                $f->numeric = 0;
            else
                $f->numeric = 0;

            $fields[] = $f;
        }
        return $fields;
    }
    function send_mail($subject, $from, $to, $body, $fname_attach="") {
        $headers = $this->get_header_token();
        
        $dataSend = array (
            "subject" => utf8_encode($subject),
            "sender" => utf8_encode($from),
            "to" => array(utf8_encode($to)),
            "html" => utf8_encode($body),
            "body_images" => array("logo" => "/static_files/img/logo_usach.jpg")
        );       

        if ($fname_attach != "") {
            $fsize = filesize($fname_attach);
            $f = fopen($fname_attach, "rb");
            $fcontent = fread($f, $fsize);
            $b64 = base64_encode($fcontent);
            $dataSend['attachments'] = array("declaracion.pdf" => $b64);
        }

        $data = json_encode($dataSend);
        if ($data===false) {
            var_dump($dataSend);
            return false;
        }
          
        $result = $this->post("https://i-service.prod.integrasystem.cl/mail/", $data, $headers);
        if ($this->have_error())
            return false;
        $result = json_decode($result, true);
        return $result;
    }
}