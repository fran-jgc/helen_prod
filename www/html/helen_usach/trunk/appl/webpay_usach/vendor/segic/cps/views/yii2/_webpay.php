<style>.menu { margin: 0px; width:100%; height: 80vh; }</style>
<?php
if (!empty($token)) {
    $this->registerJs("$('#WebpayFormInit').submit(); localStorage.setItem('pago', 'submited');", yii\web\View::POS_READY, 'autosubmitAction');
}
?>
<form id="WebpayFormInit" name="formto" action="<?= $urlWebPay; ?>" method="post">
    <input type="hidden" name="token_ws" value="<?= $token ?>">
    <?php if (empty($token)): ?>
        <input name="submitxml1" type="submit" id="submitxml" value="Reintentar Pago WebPay" />
    <?php endif; ?>
</form>

