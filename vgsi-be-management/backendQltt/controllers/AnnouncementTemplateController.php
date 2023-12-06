<?php

namespace backendQltt\controllers;

use Yii;
use common\models\AnnouncementTemplate;
use backendQltt\models\AnnouncementTemplateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backendQltt\models\AnnouncementTemplateForm;
use common\models\User;
use yii\base\Event;
use yii\helpers\VarDumper;

/**
 * AnnouncementTemplateController implements the CRUD actions for AnnouncementTemplate model.
 */
class AnnouncementTemplateController extends BaseController
{

    /**
     * Lists all AnnouncementTemplate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AnnouncementTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AnnouncementTemplate model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AnnouncementTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $event = new Event(); 
        $model_user = new User(); 
        $model = new AnnouncementTemplateForm();

        if ($model->load(Yii::$app->request->post())) {
            $templateTitle = AnnouncementTemplate::find()->where([
                'name' => $model->name,
                'type' => AnnouncementTemplate::TYPE_POST_NEWS,
            ])->all();
            $templateTitleEn = AnnouncementTemplate::find()->where([
                'name_en' => $model->name_en,
                'type' => AnnouncementTemplate::TYPE_POST_NEWS,
            ])->all();

            if (count($templateTitle) > 0) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tiêu đề đã tồn tạo trong hệ thống.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if (count($templateTitleEn) > 0) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tiêu đề (EN) đã tồn tại trong hệ thống.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $newsTemplate = $model->store();
            if ($newsTemplate) {
                $model_user->trigger('afterInsert', $event);
                Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Tạo mẫu tin tức thành công.'));

                return $this->redirect(['index']);
            } 

            Yii::$app->session->setFlash('error', Yii::t('backend', 'Error.'));
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AnnouncementTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $event = new Event(); 
        $model_user = new User(); 
        $model = new AnnouncementTemplateForm();
        $model = $model->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $templateTitle = AnnouncementTemplate::find()->where([
                'name' => $model->name,
                'type' => AnnouncementTemplate::TYPE_POST_NEWS,
            ])->andWhere(['<>', 'id', $id])->all();
            $templateTitleEn = AnnouncementTemplate::find()->where([
                'name_en' => $model->name_en,
                'type' => AnnouncementTemplate::TYPE_POST_NEWS,
            ])->andWhere(['<>', 'id', $id])->all();

            if (count($templateTitle) > 0) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tiêu đề đã tồn tạo trong hệ thống.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if (count($templateTitleEn) > 0) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tiêu đề (EN) đã tồn tại trong hệ thống.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->save()) {
                $model_user->trigger('afterUpdate', $event);
                Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Chỉnh sửa mẫu tin tức thành công.'));
                return $this->redirect(['index']);
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
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AnnouncementTemplate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $event = new Event(); 
        $model_user = new User(); 
        $this->findModel($id)->delete();
        $model_user->trigger('afterDelete', $event);
        Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Xóa mẫu tin tức thành công.'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the AnnouncementTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnnouncementTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AnnouncementTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('frontend', 'The requested page does not exist.'));
    }
}
