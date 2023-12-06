<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

//$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/reset-password', 'code' => $verifyCode->code]);
$resetLink = $linkWeb;
?>
Hello <?= $user->email ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
