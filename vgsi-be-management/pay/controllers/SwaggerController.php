<?php
namespace pay\controllers;

use Exception;
use Yii;
use yii\base\InvalidParamException;
use yii\base\UserException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use frontend\models\ResetPasswordForm;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Swager controller
 */
class SwaggerController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        Yii::$app->params['format'] = 'html';
        if (YII_ENV != "dev"){
            return [];
        }
        return [
            'doc' => [
                'class' => 'light\swagger\SwaggerAction',
                'restUrl' => \yii\helpers\Url::to(['/swagger/api'], true),
            ],
            //The resultUrl action.
            'api' => [
                'class' => 'light\swagger\SwaggerApiAction',
                //The scan directories, you should use real path there.
                'scanDir' => [
                    Yii::getAlias('@pay/controllers'),
                    Yii::getAlias('@common/models'),
                    Yii::getAlias('@pay/models'),
                ],
                'api_key' => 'luci1109',
                'apiKeyParam' => "dockey"
            ],
        ];
    }
}
