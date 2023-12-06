<?php

namespace backendQltt\controllers;

use Yii;
use common\models\UserRole;
use backendQltt\models\UserRoleSearch;
use common\models\User;
use yii\helpers\Json;
use yii\helpers\VarDumper;
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
        $allController = $this->getControllersQltt();

        if ($model->load(Yii::$app->request->post())) {
            $permission = Yii::$app->request->post('permission');

            if(!empty($permission) && is_array($permission)){
                foreach ($permission as &$subArray) {
                    $subArray = array_values(array_diff($subArray, ['0']));
                }                

                $permission = $model->genPemission(array_filter($permission));
                $model->permission = json_encode($permission);
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('message', Yii::t('backendQltt', 'Thêm mới nhóm quyền thành công'));
                return $this->redirect(['index']);
            } else {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Thêm mới nhóm quyền thất bại'));
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
        $allController = $this->getControllersQltt();
        if ($model->load(Yii::$app->request->post())) {
            $permission = Yii::$app->request->post('permission');

            if(!empty($permission) && is_array($permission)){
                foreach ($permission as &$subArray) {
                    $subArray = array_values(array_diff($subArray, ['0']));
                }                

                $permission = $model->genPemission(array_filter($permission));
                $model->permission = json_encode($permission);
            }

            if ($model->save()) {

                Yii::$app->session->setFlash('message', Yii::t('backendQltt', 'Chỉnh sửa nhóm quyền thành công '));
                return $this->redirect(['index']);
            }
        } else {

            $permission = Json::decode($model->permission);

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
        $users = User::find()->where([
            'role_id' => $id,
        ])->all();

        if (!empty($users)) {
            Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Nhóm quyền này đang được gán cho người dùng'));
            return $this->redirect(['index']);
        }

        $this->findModel($id)->delete();

        Yii::$app->session->setFlash('message', Yii::t('backendQltt', 'Xóa nhóm quyền thành công'));
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
