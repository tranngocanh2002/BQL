<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\ManagementUser */
/* @var $request \common\models\Request */
/* @var $linkWeb string */

//$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['auth/reset-password', 'code' => $verifyCode->code]);
?>
<td>
    <p style="font-family: -apple-system, BlincMacSystemFont, 'Helvetica Neue', Helvetica, 'Lucida Grande', tahoma, verdana, arial, sans-serif; font-size: 24px; line-height: 24px; color: #4b4f56; word-break: break-word; text-align: center; font-weight: bold;"><?= $user->buildingCluster->name ?></p>
    <table style="border-collapse: collapse;" border="0" width="100%" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td style="font-size: 11px; font-family: LucidaGrande,tahoma,verdana,arial,sans-serif; background: #ffffff; border: solid 1px #dddfe2; border-radius: 3px; padding: 16px 16px 6px 16px; display: block;">
                <table style="border-collapse: collapse;" border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td width="48"><span style="color: #3b5998; text-decoration: none;"><img class="CToWUd" style="border: 0; background-color: #ebe9e7; border-radius: 50px;" width="48" height="48" <?php if(!empty($user->avatar)){ ?>src="<?= $user->avatar ?>" <?php } ?> /></span></td>
                        <td style="display: block; width: 10px;" width="10">&nbsp;&nbsp;&nbsp;</td>
                        <td width="100%">
                            <table style="border-collapse: collapse;" border="0" cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr>
                                    <td><span class="m_6551581496205127995mb_text" style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;"><span style="color: #4b4f56; text-decoration: none; font-weight: bold;"><?= $request->residentUser->first_name ?> - <?= $request->apartment->name ?> (<?= $request->apartment->parent_path ?>)</span>&nbsp;đ&atilde; đăng trong mục&nbsp;<span style="color: #4b4f56; text-decoration: none; font-weight: bold;"><?= $request->requestCategory->name ?></span></span></td>
                                </tr>
                                <tr>
                                    <td style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 13px; line-height: 18px; color: #90949c; word-break: break-word;"><span class="m_6551581496205127995mb_text" style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 13px; line-height: 18px; color: #90949c; word-break: break-word;"><span style="color: #90949c; text-decoration: none;"><?= date('H:i:s d-m-Y', $request->created_at) ?></span></span></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="line-height: 10px;" height="10">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <table style="border-collapse: collapse;" border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr>
                                    <td>
                                        <table style="border-collapse: collapse; width: 100%;" border="0" cellspacing="0" cellpadding="0">
                                            <tbody>
                                            <tr>
                                                <td style="font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif; font-size: 16px; line-height: 24px; color: #4b4f56; word-break: break-word;">&nbsp;Xin vui long l&ecirc;n kiểm tra nước gi&uacute;p m&igrave;nh nh&agrave; m&igrave;nh nh&eacute;.</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <p style="text-align: left;"><a class="button" style="background-color: #4688F1; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 14px; font-weight: bold; margin: 4px 2px; cursor: pointer; font-family: -apple-system,BlincMacSystemFont,Helvetica Neue,Helvetica,Lucida Grande,tahoma,verdana,arial,sans-serif;" href="<?= $linkWeb ?>" target="_blank" rel="noopener">Xem tr&ecirc;n Luci Management</a></p>
</td>
