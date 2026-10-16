<?php

use yii\helpers\Html;
use segic\cps\CpsAsset;

CpsAsset::register($this);

$this->title = 'Voucher Pago Online';
?>
<div class="bloqueo-create">

  <div class="card card-primary">
    <div class="card-body">
      <div class="row">

        <div id="invoice-POS">


          <div align="center" id="top">
            <?php if ($result["success"]) : ?>
              <div class="logo"><i style="color: green;" class="far fa-check-circle fa-4x"></i></div>
              <div class="info">
                <h2 style="color: green;">¡Pago exitoso!</h2>
              </div>
            <?php else : ?>
              <div class="logo"><i style="color: red;" class="far fa-grimace fa-4x"></i></div>
              <div class="info">
                <h2 style="color: red;">Pago rechazado</h2>
              </div>
            <?php endif; ?>
            <br>
          </div>

          <div id="mid">
            <div class="info">
              <h3>Número de transacción</h3>
              <p style="text-align: center;"><?= $idTransaction ?></p>
            </div>
            <div class="info">
              <h3>Info contacto USACH</h3>
              <p>
                Dirección : Av Libertador Bernardo O'Higgins, Estación Central</br>
                Correo : <a href="<?= Yii::$app->getModule('cps')->contactoMail ?>"><?= Yii::$app->getModule('cps')->contactoMail ?></a></br>
              </p>
            </div>
          </div>
          <!--End Invoice Mid-->

          <div id="bot">

            <?php if ($result["success"]) : ?>
              <div>
                <table class="table-ticket">
                  <tr class="tabletitle">
                    <td class="item">
                      Item
                    </td>
                    <td class="Rate">
                      Sub Total
                    </td>
                  </tr>

                  <tr style="border: 1px solid white" class="service">
                    <td class="tableitem">
                      <p class="itemtext">Pago carrera</p>
                    </td>
                    <td class="tableitem">
                      <p class="itemtext">$<?php echo number_format($result["monto"], 0, ',', ' '); ?></p>
                    </td>
                  </tr>

                  <tr class="tabletitle">
                    <td class="Rate"> Total </td>
                    <td class="payment"> $<?php echo number_format($result["monto"], 0, ',', ' '); ?> </td>
                  </tr>

                </table>
              </div>
            <?php endif; ?>


            <div id="legalcopy">
              <?php if ($result["success"]) : ?>
                <p class="legal"><strong>Gracias por su pago,</strong> el servicio de finanza procesará su pago en unos minutos.
                </p>
              <?php else : ?>
                <p style="color:red" class="legal"><strong>Error : </strong><?php echo $result["error"] ?>.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <br><br>
    </div>
    <div class="card-footer pull-right">
      <?= Html::a('<i class="fas fa-check-circle"></i> OK', Yii::$app->homeUrl, ['class' => 'btn btn-primary pull-right']) ?>
    </div>
  </div>
</div>