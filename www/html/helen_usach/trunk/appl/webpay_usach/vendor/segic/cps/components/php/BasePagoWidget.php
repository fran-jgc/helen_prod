<?php

namespace segic\cps\components\php;

use segic\cps\components\CommonCall;

/**
 * Description of PagoWidget
 *
 * @author gilles
 */
class BasePagoWidget
{

    public $storeId;
    public $fieldId;
    public $options = [];
    public $frameName;
    public $formType = 'frame';
    public $size = 'small';
    public $type;
    public $scriptHook = '';
    // Internal variable
    private $_formId;
    private $_fieldJQuery;
    private $_frameIdentificator;
    private $_selfURL;

    public function init($url = 'https://webpay-server.usach.cl/api')
    {
        if (session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $api = new CommonCall($url);
        $this->_selfURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

        $storeId = filter_input(INPUT_POST, 'storeId');
        $monto = filter_input(INPUT_POST, 'monto');
        $type = filter_input(INPUT_POST, 'type');
        $action = filter_input(INPUT_GET, 'action');

        if (!empty($action)) {

            if ($action == "form") {

                $urlWebPay = filter_input(INPUT_GET, 'url');
                $token = (isset($_SESSION['currentPago']) && isset($_SESSION['currentPago']['token'])) ? $_SESSION['currentPago']['token'] : '';

                $html = '<html>';
                $html .= '<head>';
                $html .= '<meta charset="UTF-8">';
                $html .= '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>';
                $html .= '<title>Webpay</title>';
                $html .= '</head>';
                $html .= '<body style="text-align: center;">';
                $html .= '<form id="WebpayFormInit" name="formto" action="' . $urlWebPay . '" method="post">';
                $html .= '<input type="hidden" name="token_ws" value="' . $token . '">';
                $html .= empty($token) ? '<input name="submitxml1" type="submit" id="submitxml" value="Reintentar Pago WebPay" />' : '';
                $html .= '</form>';
                $html .= '</body>';
                $html .= '<script>';
                $html .= 'jQuery(function($) { $("#WebpayFormInit").submit(); });';
                $html .= '</script>';
                $html .= '</html>';

                echo $html;

                exit();
            }

            if ($action == "final") {

                $transactionId = filter_input(INPUT_GET, 'idTransaction');
                $error = "";
                $result = $api->result($transactionId, $error);

                if (!empty($error) || empty($transactionId)) {
                    header("Location: error.php");
                } else {
                    header("Location: result.php?idTransaction=" . $transactionId);
                }

                die();
            }
        }

        if (!empty($storeId) && !empty($type) && !empty($monto)) {

            $urlFinal = $this->_selfURL . "?action=final";
            $error = "";

            if ($type == CommonCall::TIPO_WEBPAY) {
                $result = $api->initialisationWebPay($monto, $storeId, $urlFinal, $error);
            }
            if ($type == CommonCall::TIPO_SERVIPAG) {
                $result = $api->initialisationServipag($monto, $storeId, $urlFinal, $error);
            }
            if ($type == CommonCall::TIPO_GETNET) {
                $result = $api->initialisationGetNet($monto, $storeId, $urlFinal, $error);
            }

            $_SESSION['currentPago'] = [];
            $_SESSION['currentPago'] = $result;

            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
    }

    public function javascriptForm($action = '#')
    {
        $form = "$('body').append('<div id=\"div" . $this->_formId . "\"></div>');\n"
            . "$('#div" . $this->_formId . "').append('" . '<form id="' . $this->_formId . '" action="' . $action . '" method="post" target="' . $this->_frameIdentificator . '"></form>' . "');\n";

        if ($this->type == CommonCall::TIPO_WEBPAY || $this->type == CommonCall::TIPO_SERVIPAG) {
            $form .= "$('#" . $this->_formId . "').append('<input type=\"hidden\" name=\"" . (($this->type == CommonCall::TIPO_WEBPAY) ? "token_ws" : "xml") . "\">');\n\n";
        }

        return $form;
    }

    public function javascriptClickButton($url = null, $urlError = null)
    {
        $javascript = '';

        if (empty($url)) {
            $url = $this->_selfURL;
        }

        $errorHandle = "$(location).attr('href', '" . $urlError . "')";

        if ($this->formType == 'frame') {
            $javascript .= "$('iframe[name=" . $this->_frameIdentificator . "]').hide();\n";
            $errorHandle = "$.get('" . $urlError . "', null, function(data) { $('iframe[name=" . $this->_frameIdentificator . "]').contents().find('html').html(data); });";
        }

        $javascript .= "$('#" . $this->options['id'] . "').unbind('click').click(function() {\n\n"
            . " // Verif integrity amount field\n"
            . " var inputAmountCount = $('" . $this->_fieldJQuery . "').length;\n"
            . " if (inputAmountCount != 1) { alert('El campo monto total con el identificador \'" . $this->fieldId . "\' es invalido.'); return false; }\n"
            . " var amount = $('" . $this->_fieldJQuery . "').val();\n\n"
            . " $.post('" . $url . "', {type : '" . $this->type . "', storeId : '" . $this->storeId . "', monto : amount})\n"
            . "     .done(function(data) {\n"
            . "         InitResult = data;\n"
            . (($this->formType == 'frame') ? "         $('iframe[name=" . $this->_frameIdentificator . "]').show();\n" : "") . ""
            . "         if (InitResult.success) {\n"
            . "        " . $this->scriptHook . "\n"
            . (($this->type == CommonCall::TIPO_WEBPAY) ? "         $('#" . $this->_formId . "').attr('action', InitResult.url);\n" : "") . ""
            . (($this->type == CommonCall::TIPO_WEBPAY) ? "         $('#" . $this->_formId . " input[name=\"token_ws\"]').val(InitResult.token);\n" : "") . ""
            . (($this->type == CommonCall::TIPO_SERVIPAG) ? "         $('#" . $this->_formId . " input[name=\"xml\"]').val(InitResult.xml);\n" : "") . ""
            . (($this->type == CommonCall::TIPO_WEBPAY || $this->type == CommonCall::TIPO_SERVIPAG) ? "             $('#" . $this->_formId . "').submit();\n" : "") . ""
            . (($this->type == CommonCall::TIPO_GETNET) ? "         window.location.href = InitResult.url;\n" : "") . ""
            . "         } else {\n"
            . "             alert(InitResult.message);\n"
            . "             " . $errorHandle . "\n"
            . "         }\n"
            . "     })\n"
            . "     .fail(function() {\n"
            . "         alert('Error local Webpay ...');\n"
            . "     });\n"
            . " return false;\n"
            . " });\n";

        return $javascript;
    }

    public function generateButton($assets = "vendor/segic/cps/assets")
    {

        if (!array_key_exists('id', $this->options)) {
            $this->options['id'] = uniqid();
        }

        $pxSize = "64";

        if ($this->size == 'small') {
            $pxSize = "64";
        }
        if ($this->size == 'large') {
            $pxSize = "128";
        }

        $style = 'background: url(' . $assets . '/img/' . $this->type . '.png' . ');'
            . 'background-size: ' . $pxSize . 'px ' . $pxSize . 'px;'
            . 'height: ' . $pxSize . 'px; width: ' . $pxSize . 'px';

        $this->options['style'] = array_key_exists('style', $this->options) ? $this->options['style'] . $style : $style;
        $this->_formId = "form_pay_" . $this->options['id'];
        $target = ($this->formType != 'frame') ? $this->formType : "framePay_" . $this->options['id'];
        $this->_frameIdentificator = empty($this->frameName) ? $target : $this->frameName;

        $this->_fieldJQuery = 'input[id="' . $this->fieldId . '"]';

        $frameTag = '';

        if ($this->formType == 'frame') {
            if (empty($this->frameName)) {
                $frameTag .= '<iframe name="' . $this->_frameIdentificator . '" frameborder="0" style="margin: 0px; width: 100%; height: 80vh; display: none;"></iframe>';
            }
        }

        return '<button type="button" id="' . $this->options['id'] . '" class="btn-pago-webpay" style="' . $style . '"></button>' . $frameTag;
    }
}
