<?php

namespace backendQltt\controllers;

use backend\models\AuthItemSearch;
use common\helpers\ErrorCode;
use common\models\rbac\AuthGroup;
use common\models\rbac\AuthItem;
use common\models\rbac\AuthItemChild;
use Yii;
use common\models\UserRole;
use backend\models\UserRoleSearch;
use yii\helpers\Json;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthItemController extends BaseController
{
    /**
     * @inheritdoc
     */

    /**
     * Lists all UserRole models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserRole model.
     * @param string $name
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AuthItem();
        $allPermission = AuthItem::find()->where(['type' => AuthItem::TYPE_PERMISSION])->all();
        if ($model->load(Yii::$app->request->post())) {
            $model->type = AuthItem::TYPE_ROLE;
            $model->tag = strtoupper($model->tag);
            $permission_web = Yii::$app->request->post('permission_web');
            if(!empty($permission_web) && is_array($permission_web)){
                $model->data_web = json_encode($permission_web);
            }
            $permission = Yii::$app->request->post('permission');
            if ($model->save()) {
                if(!empty($permission)){
                    foreach ($permission as $per){
                        $authItemChild = new AuthItemChild();
                        $authItemChild->parent = $model->name;
                        $authItemChild->child = $per;
                        $authItemChild->save();
                    }
                }
                Yii::$app->session->setFlash('message', Yii::t('backend', 'Create Role Successfully'));
                return $this->redirect(['view', 'id' => $model->name]);
            } else {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Create Role Error'));
                return $this->render('create', [
                    'model' => $model,
                    'allPermission' => $allPermission,
                    'permissionChild' => []
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'allPermission' => $allPermission,
                'permissionChild' => []
            ]);
        }
    }

    /**
     * Updates an existing UserRole model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $name
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $allPermission = AuthItem::find()->where(['type' => AuthItem::TYPE_PERMISSION])->all();
        if ($model->load(Yii::$app->request->post())) {
            $model->tag = strtoupper($model->tag);
            $permission_web = Yii::$app->request->post('permission_web');
            if(!empty($permission_web) && is_array($permission_web)){
                $model->data_web = json_encode($permission_web);
            }
            $permission = Yii::$app->request->post('permission');
            if ($model->save()) {
                AuthItemChild::deleteAll(['parent' => $model->name]);
                if(!empty($permission)){
                    foreach ($permission as $per){
                        $authItemChild = AuthItemChild::findOne(['parent' => $model->name, 'child' => $per]);
                        if(!empty($authItemChild)){
                            continue;
                        }
                        $authItemChild = new AuthItemChild();
                        $authItemChild->parent = $model->name;
                        $authItemChild->child = $per;
                        $authItemChild->save();
                    }
                }
                Yii::$app->session->setFlash('message', Yii::t('backend', 'Update Role Successfully'));
                return $this->redirect(['view', 'id' => $model->name]);
            }
        } else {
            $permissionChild = [];
            $authItemChilds = AuthItemChild::find()->select(['child'])->where(['parent' => $id])->all();
            foreach ($authItemChilds as $authItemChild) {
                $permissionChild[] = $authItemChild->child;
            }
            return $this->render('update', [
                'model' => $model,
                'allPermission' => $allPermission,
                'permissionChild' => $permissionChild
            ]);
        }
    }

    /**
     * Deletes an existing UserRole model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $name
     * @return mixed
     */
    public function actionDelete($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = $this->findModel($id);
            if($model->delete()){
                $authGroups = AuthGroup::find()->where(['LIKE', 'data_role', $id])->all();
                foreach ($authGroups as $authGroup){
                    if(!empty($authGroup->data_role)){
                        $data_role = json_decode($authGroup->data_role, true);
                        unset($data_role[$id]);
                        $authGroup->data_role = json_encode($data_role);
                        if(!$authGroup->save()){
                            Yii::error($authGroup->errors);
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('error', Yii::t('backend', 'Delete Role Error'));
                            return $this->redirect(['index']);
                        }
                    }
                }
                $transaction->commit();
                Yii::$app->session->setFlash('success', Yii::t('backend', 'Delete Role Successfully'));
                return $this->redirect(['index']);
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Delete Role Error'));
                return $this->redirect(['index']);
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::error($ex->getMessage());
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Delete Role Error'));
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the UserRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $name
     * @return UserRole the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($name)
    {
        if (($model = AuthItem::findOne(['name' => $name])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
