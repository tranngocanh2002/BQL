<?php

namespace backendQltt\controllers;

use common\helpers\ErrorCode;
use Yii;
use common\models\User;
use backendQltt\models\UserSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backendQltt\models\UserChangePasswordForm;
use backendQltt\models\UserImportForm;
use common\helpers\CUtils;
use yii\helpers\VarDumper;
use backendQltt\models\LogBehavior;
use yii\base\Event;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $userModel = $this->findModel($id);
        $modelResetPassword = $this->findModel($id);
        $modelResetPassword->setScenario('reset-password');

        if ($userModel->load(Yii::$app->request->bodyParams) && $userModel->save()) {
            Yii::$app->session->setFlash('message', Yii::t('backend', 'Update User Successfully'));
            return $this->redirect(['view', 'id' => $userModel->id]);
        }
        return $this->render('view', [
            'model' => $userModel,
            'modelResetPassword' => $modelResetPassword,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->setScenario('create');
        if ($model->load(Yii::$app->request->post())) {
            $password = CUtils::randomString(10);
            $model->password_hash = $password;
            $model->password = $password;
            $model->confirm_password = $password;
            $model->setPassword($model->password_hash);
            if ($model->save()) {
                $model->sendEmailCreatePassword($password, 'Tài khoản truy cập Web QLTT');
                Yii::$app->session->setFlash('message', Yii::t('backendQltt', 'Thêm người dùng thành công'));
                return $this->redirect(['index']);
            }

            Yii::error($model->errors);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($id)
    {
        $model = $this->findModel($id);
        $model->setScenario('reset-password');
        // $event = new Event();
        // $model->trigger('afterResetPassword', $event);
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword(true)) {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'New password saved.'));

            if (Yii::$app->request->post('send_email')) {
                $model->sendEmailResetPasswordByAdmin(Yii::$app->request->post('User')['password']);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }
        Yii::error($model->errors);

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Xóa người dùng thành công.'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
    }

    public function actionProfile($action = null)
    {
        try {
            $model = $this->findModel(Yii::$app->user->id);
            $modelChangePassword = new UserChangePasswordForm();

            if ($model->load(Yii::$app->request->post()) || $modelChangePassword->load(Yii::$app->request->post())) {
                if ($action == 'update-profile' && $model->validate()) {
                    if (empty($model->avatar)) {
                        unset($model->avatar);
                    }
                    $model->update();
                } elseif ($action == 'change-password' && $modelChangePassword->validate()) {

                    $modelChangePassword->changePassword();
                    return $this->asJson([
                        'status' => true,
                        'message' => 'success',
                        'data' => null,
                    ]);
                } else {
                    Yii::error($model->errors);
                    Yii::error($modelChangePassword->errors);
                }

                Yii::$app->session->setFlash('success', Yii::t('backend', 'Cập nhật thành công.'));
                return $this->redirect(['profile']);
            }
        } catch (\Throwable $th) {
            Yii::$app->session->setFlash('error', $th->getMessage());

            if ($action == 'change-password') {
                return $this->asJson([
                    'status' => false,
                    'message' => $th->getMessage(),
                    'data' => null,
                ]);
            }

            return $this->redirect(['profile']);
        }

        return $this->render('profile', [
            'model' => $model,
            'modelChangePassword' => $modelChangePassword
        ]);
    }

    /**
     * Active or inactive user
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionInactive($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status === User::STATUS_ACTIVE ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
        if ($model->status == User::STATUS_INACTIVE) {
            $model->logged = User::NOT_LOGGED;
        }

        if (!$model->save(false)) {
            Yii::error($model->errors);
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Update Error'));
        } else {
            Yii::$app->session->setFlash('message', Yii::t('backend', $model->status === User::STATUS_ACTIVE ? 'Activate successfully' : 'Deactivate successfully'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Updates an existing ManagementUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('message', Yii::t('backendQltt', 'Chỉnh sửa người dùng thành công'));
            } else {
                Yii::error($model->errors);
                $errors = $model->errors;
                foreach ($errors as $attribute => $errorMessages) {
                    foreach ($errorMessages as $errorMessage) {
                        Yii::$app->session->setFlash('error', Yii::t('backendQltt', $errorMessage));
                    }
                }

                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Import user from excel file
     */
    public function actionImportFile()
    {
        try {
            $model = new UserImportForm();
            $event = new Event();
            $model->trigger('afterImportFile', $event);
            if (Yii::$app->request->isPost && isset($_FILES['file'])) {
                $model->file = $_FILES['file'];
                if ($model->upload()) {
                    return $this->asJson($model->import());
                }
            }
            return $this->asJson([
                'status' => false,
                'message' => Yii::t('backend', "Invalid data"),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                'errors' => $model->getErrors()
            ]);
        } catch (\Exception $e) {
            return $this->asJson([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ]);
        }

    }

    public function actionExportFile()
    {
        try {
            $model = new UserImportForm();
            $event = new Event();
            $model->trigger('afterExportFile', $event);
            $filePath = $model->export();
            if (file_exists($filePath)) {
                return Yii::$app->response->sendFile($filePath);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Export fail'));
                return $this->redirect(['index']);
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', Yii::t('backend', $e->getMessage()));
            return $this->redirect(['index']);
        }
    }

    public function actionExportTemplate()
    {
        try {
            $model = new UserImportForm();
            $filePath = $model->exportTemplate();

            if (file_exists($filePath)) {
                return Yii::$app->response->sendFile($filePath);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Export fail'));
                return $this->redirect(['index']);
            }
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', Yii::t('backend', $e->getMessage()));
            return $this->redirect(['index']);
        }
    }
}