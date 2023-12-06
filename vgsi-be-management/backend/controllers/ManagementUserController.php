<?php

namespace backend\controllers;

use common\models\ManagementUser;
use common\models\ManagementUserAccessToken;
use common\models\ManagementUserDeviceToken;
use Yii;
use backend\models\ManagementUserSearch;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * ManagementUserController implements the CRUD actions for ManagementUser model.
 */
class ManagementUserController extends BaseController
{
    /**
     * @inheritdoc
     */

    /**
     * Lists all ManagementUser models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ManagementUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ManagementUser model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ManagementUser model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ManagementUser();
        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword(time());
            if ($model->save()) {
                Yii::$app->session->setFlash('message', Yii::t('backend', 'Create Successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Create Error'));
                return $this->render('create', [
                    'model' => $model
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ManagementUser model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('message', Yii::t('backend', 'Update Successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ManagementUser model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_deleted = ManagementUser::DELETED;
        $model->email = $model->email . '_' . time();
        if(!$model->save()){
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Delete Error'));
        }else{
            Yii::$app->session->setFlash('message', Yii::t('backend', 'Delete Successfully'));
        }
        return $this->redirect(['index']);
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
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->resetPassword()) {
                Yii::$app->session->setFlash('success', Yii::t('backend', 'New password saved.'));
                //logout tất cả token cũ
                ManagementUserAccessToken::deleteAll(['management_user_id' => $model->id]);
                ManagementUserDeviceToken::deleteAll(['management_user_id' => $model->id]);
                return $this->redirect(['view', 'id' => $model->id]);
            }
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Reset password customer failed'));
        }
        unset($model->password);
        return $this->render('reset_password', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ManagementUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ManagementUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ManagementUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
