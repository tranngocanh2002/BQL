<?php
use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

$info_system = Yii::$app->params['info_system'];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <table style="width: 618px; margin-left: auto; margin-right: auto;">
        <tbody>
        <tr>
            <td style="width: 612px;"><img src="<?= $info_system['logo_path'] ?>" alt="" width="82" height="58" /></td>
        </tr>
        <tr>
            <?= $content ?>
        </tr>
        <tr>
            <td style="width: 612px; border-top: 1px solid #ddd; padding: 10px 0px 0px 0px;"><span style="color: #4688f1;"><strong><?= $info_system['name'] ?></strong></span></td>
        </tr>
        <tr>
            <td style="width: 612px;">
                <p><?= $info_system['address'] ?><br />Tel: <?= $info_system['tel'] ?> - Hotline: <?= $info_system['hotline'] ?><br />Email:&nbsp;<a href="mailto:<?= $info_system['email'] ?>"><?= $info_system['email'] ?></a> - Website:&nbsp;<a href="<?= $info_system['website'] ?>" target="_blank" rel="noopener"><?= $info_system['website'] ?></a></p>
                <p>&nbsp;</p>
            </td>
        </tr>
        </tbody>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p style="padding-left: 120px;">&nbsp;</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
