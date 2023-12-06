<?php

namespace backend\controllers;

use Yii;
use common\models\UserRole;
use backend\models\UserRoleSearch;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * UserRoleController implements the CRUD actions for UserRole model.
 */
class UserRoleController extends BaseController {
    /**
     * @inheritdoc
     */

    /**
     * Lists all UserRole models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserRoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserRole model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new UserRole();
        $allController = $this->getControllers();
        //print_R($allController);die;
        if ($model->load(Yii::$app->request->post())) {
            $model->permission = json_encode(Yii::$app->request->post('permission'));
            if ($model->save()) {
                Yii::$app->session->setFlash('message', Yii::t('backend', 'Create Role Successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Create Role Error'));
                return $this->render('create', [
                    'model' => $model,
                    'allController' => $allController
                ]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'allController' => $allController
            ]);
        }
    }

    /**
     * Updates an existing UserRole model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $allController = $this->getControllers();
        if ($model->load(Yii::$app->request->post())) {
            $model->permission = json_encode(Yii::$app->request->post('permission'));
            if ($model->save()) {

                Yii::$app->session->setFlash('message', Yii::t('backend', 'Update Role Successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {

            $permission = Json::decode($model->permission);
            //print_R($permission);die;
            return $this->render('update', [
                        'model' => $model,
                        'allController' => $allController,
                        'permission' => $permission
            ]);
        }
    }

    /**
     * Deletes an existing UserRole model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('message', Yii::t('backend', 'Delete Role Successfully'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the UserRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserRole the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = UserRole::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
