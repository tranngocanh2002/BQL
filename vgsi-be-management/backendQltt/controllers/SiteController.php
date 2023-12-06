<?php

namespace backendQltt\controllers;

use backendQltt\models\CheckOtpTokenForm;
use backendQltt\models\LoginForm;
use backendQltt\models\PasswordResetRequestForm;
use backendQltt\models\ResetPasswordForm;
use common\models\User;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
// use backend\controllers\BaseController;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\base\Event;

/**
 * Site controller
 */
class SiteController extends BaseController
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        parent::behaviors();
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'request-password-reset', 'reset-password-email', 'demo', 'reset-password', 'verify-otp'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionDemo()
    {
        return $this->render('demo');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        // $event = new Event();
        // $model = new User(); // Tạo instance của model
        // $model->trigger('afterLogin', $event);
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = Yii::$app->user->identity;
            $user->logged = User::LOGGED;
            $user->save(false);
            return $this->checkPermission('building-cluster', 'index') ? $this->redirect('/building-cluster') : $this->redirect('/user/profile');
            // return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        $model->email = Yii::$app->getRequest()->getQueryParam('email');
        if (($model->load(Yii::$app->request->post()) || $model->email) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Kiểm tra email của bạn để được hướng dẫn thêm.'));

                return $this->redirect(['/site/verify-otp', 'email' => $model->email]);
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        $event = new Event();
        $model = new User(); // Tạo instance của model
        $model->trigger('afterLogout', $event);
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Resets password.
     *
     * @param $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPasswordEmail($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Đã cập nhật mật khẩu mới'));
            return $this->goHome();
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * @return Response|string
     */
    public function actionVerifyOtp()
    {
        $model = new CheckOtpTokenForm();
        $email = Yii::$app->getRequest()->getQueryParam('email');

        if ($model->load(Yii::$app->request->post()) && $model->validateCode($email)) {
            $user = User::findOne(['email' => $email]);
            $user->generatePasswordResetToken();

            if (!$user->save(false)) {
                Yii::error($user->errors);
                Yii::$app->session->setFlash('error', 'Server error.');
            }

            return $this->redirect(['/site/reset-password', 'token' => $user->password_reset_token]);
        }

        return $this->render('verifyOtp', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Đã cập nhật mật khẩu mới'));
            return $this->goHome();
        }
        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}