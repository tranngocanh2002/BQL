<?php

namespace backendQltt\controllers;

use common\models\ManagementUser;
use common\models\rbac\AuthGroup;
use common\models\rbac\AuthItem;
use common\models\RequestMapAuthGroup;
use Yii;
use backendQltt\models\AuthGroupSearch;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * AuthGroupController implements the CRUD actions for AuthGroup model.
 */
class AuthGroupController extends BaseController {
    /**
     * @inheritdoc
     */

    /**
     * Lists all AuthGroup models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new AuthGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuthGroup model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AuthGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new AuthGroup();
        $allRoles = AuthItem::find()->where(['type' => AuthItem::TYPE_ROLE])->asArray()->all();
        if ($model->load(Yii::$app->request->post())) {
            $permission = Yii::$app->request->post('permission');
            if(!empty($permission) && is_array($permission)){
                $permission = array_filter($permission, function($value) {
                    return $value !== "0";
                });

                $model->data_role = json_encode(array_values($permission));
            }
            $model->code = 'CODE'.time();
            if ($model->save()) {
                $model->updatePermissionUser();
                Yii::$app->session->setFlash('message', Yii::t('backend', 'Create Role Successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                throw new HttpException('500', 'System busy');
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'allRoles' => $allRoles,
                'permissionChild' => []
            ]);
        }
    }

    /**
     * Updates an existing AuthGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $allRoles = AuthItem::find()->where(['type' => AuthItem::TYPE_ROLE])->all();
        if ($model->load(Yii::$app->request->post())) {
            $permission = Yii::$app->request->post('permission');
            if(!empty($permission) && is_array($permission)){
                $permission = array_filter($permission, function($value) {
                    return $value !== "0";
                });

                $model->data_role = json_encode(array_values($permission));
            }
            if ($model->save()) {
                $model->updatePermissionUser();
                Yii::$app->session->setFlash('message', Yii::t('backend', 'Update Role Successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $permissionChild = Json::decode($model->data_role);
            return $this->render('update', [
                        'model' => $model,
                        'allRoles' => $allRoles,
                        'permissionChild' => $permissionChild
            ]);
        }
    }

    /**
     * Deletes an existing AuthGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);
        $managementUser = ManagementUser::findOne(['auth_group_id' => $id, 'is_deleted' => ManagementUser::NOT_DELETED, 'building_cluster_id' => $model->building_cluster_id]);
        $requestMapAuthGroup = RequestMapAuthGroup::findOne(['auth_group_id' => $id]);
        if(!empty($managementUser) || !empty($requestMapAuthGroup)){
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Auth Group is being used'));
            return $this->redirect(['index']);
        }
        $model->delete();

        Yii::$app->session->setFlash('message', Yii::t('backend', 'Delete Role Successfully'));
        return $this->redirect(['index']);
    }

    /**
     * Finds the AuthGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = AuthGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionGetByCluster($building_cluster_id)
    {
        if(isset($building_cluster_id)&&is_numeric($building_cluster_id)&&$building_cluster_id>0){
            $list_data = AuthGroup::find()->where(['building_cluster_id' => $building_cluster_id])->all();
            $html='';
            foreach ($list_data as $item){
                $html.='<option value="'.$item->id.'">'.$item->name.'</option>';
            }
            echo $html;
        }
        die;
    }
}
