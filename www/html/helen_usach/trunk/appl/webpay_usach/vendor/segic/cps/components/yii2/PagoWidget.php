<?php

namespace segic\cps\components\yii2;

use Exception;
use yii\base\Widget;
use segic\cps\CpsAsset;
use segic\cps\components\php\BasePagoWidget;

/**
 * Description of PagoWidget
 *
 * @author gilles
 */
class PagoWidget extends Widget {

    public $storeId;
    public $fieldId;
    public $formId;
    public $options = [];
    public $ajaxDefaultAction = 'initpay';
    public $frameName;
    public $formType = 'frame';
    public $size = 'small';
    public $scriptHook = '';
    // Internal variable
    public $_view;
    public $_baseWidget;

    public function init() {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (!isset($this->storeId)) {
            throw new Exception('El parametro storeID del widget Segic CPS no puede ser vacio.');
        }
        if (!isset($this->fieldId)) {
            throw new Exception('El parametro fieldID del widget Segic CPS no puede ser vacio.');
        }

        $this->_baseWidget = new BasePagoWidget();

        $this->_baseWidget->size = $this->size;
        $this->_baseWidget->storeId = $this->storeId;
        $this->_baseWidget->fieldId = $this->fieldId;
        $this->_baseWidget->options = $this->options;
        $this->_baseWidget->formType = $this->formType;
        $this->_baseWidget->frameName = $this->frameName;
        $this->_baseWidget->scriptHook = $this->scriptHook;
    }

    protected function generateButton($type) {

        $this->_view = $this->getView();
        $assets = CpsAsset::register($this->_view);

        $this->_baseWidget->type = $type;
        echo $this->_baseWidget->generateButton($assets->baseUrl);
    }

    protected function javascriptForm($action = '#') {
        return $this->_baseWidget->javascriptForm($action);
    }

    protected function javascriptClickButton($url = null, $urlError = null) {
        return $this->_baseWidget->javascriptClickButton($url, $urlError);
    }

}
