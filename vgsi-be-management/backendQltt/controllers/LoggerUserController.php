<?php

namespace backendQltt\controllers;

use Yii;
use common\models\AnnouncementCampaign;
use backendQltt\models\AnnouncementCampaignSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use DateTime;
use frontend\models\ActionLogSearch;
use backendQltt\models\LoggerUser;

/**
 * AnnouncementCampaignController implements the CRUD actions for AnnouncementCampaign model.
 */
class LoggerUserController extends BaseController
{

    /**
     * Lists all AnnouncementCampaign models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new LoggerUser();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // $dataProvider = LoggerUser::find()->orderBy(['id' => SORT_DESC])->all();
        // var_dump($dataProvider->totalCount);die();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider->getModels(),
        ]);
    }

    /**
     * Displays a single AnnouncementCampaign model.
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
     * Creates a new AnnouncementCampaign model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AnnouncementCampaign();

        if ($model->load(Yii::$app->request->post())) {
            $isTrash = Yii::$app->request->get('isTrash');

            if ($isTrash == 'true') {
                $model->status = AnnouncementCampaign::STATUS_UNACTIVE;
            }

            if (!empty($model->send_event_at)) {
                $datetime = DateTime::createFromFormat('H:i d/m/Y', $model->send_event_at);
                $timestamp = $datetime->getTimestamp();
                $model->send_event_at = $timestamp;
            }

            if (!empty($model->targets)) {
                $model->targets = json_encode($model->targets);
            }

            $newsEnCount = AnnouncementCampaign::find()->where([
                'type' => AnnouncementCampaign::TYPE_POST_NEW,
                'title_en' => $model->title_en,

            ])->count();
            
            if ($newsEnCount > 0) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tiêu đề (En) đã tồn tại trên hệ thống.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $newsCount = AnnouncementCampaign::find()->where([
                'type' => AnnouncementCampaign::TYPE_POST_NEW,
                'title' => $model->title,

            ])->count();

            if ($newsCount > 0) {
                Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Tiêu đề  đã tồn tại trên hệ thống.'));

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $model->type = AnnouncementCampaign::TYPE_POST_NEW;
            
            if (gettype($model->is_send_push) === 'array') {
                $model->is_send_push = (int) $model->is_send_push[0];
            }

            if (!$model->save()) {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Error.'));

                return $this->redirect(['/announcement-campaign/create']);
            }

            Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Tạo tin tức thành công.'));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AnnouncementCampaign model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $isTrash = Yii::$app->request->get('isTrash');

            if ($isTrash == 'true') {
                $model->status = AnnouncementCampaign::STATUS_UNACTIVE;
            }

            if (gettype($model->is_send_push) === 'array') {
                $model->is_send_push = (int) $model->is_send_push[0];
            }

            if (!empty($model->send_event_at)) {
                $datetime = DateTime::createFromFormat('H:i d/m/Y', $model->send_event_at);
                $timestamp = $datetime->getTimestamp();
                $model->send_event_at = $timestamp;
            }

            if (!empty($model->targets)) {
                $model->targets = json_encode($model->targets);
            }

            if (!$model->save()) {
                Yii::error($model->errors);
                Yii::$app->session->setFlash('error', Yii::t('backend', 'Error.'));

                return $this->render('update', [
                    'model' => $model,
                ]);
            }

            $message = $model->status == AnnouncementCampaign::STATUS_ACTIVE ? 'Lưu thông tin này vào trạng thái công khai' : 'Cập nhật tin tức thành công.';
            Yii::$app->session->setFlash('success', Yii::t('backendQltt', $message));
            
            if ($isTrash == 'false') {
                return $this->redirect(['index']);
            }

            return $this->redirect(['update', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AnnouncementCampaign model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $news = $this->findModel($id);

        if ($news->status == AnnouncementCampaign::STATUS_ACTIVE) {
            Yii::$app->session->setFlash('error', Yii::t('backendQltt', 'Bạn không thể xóa tin tức đã công khai'));

            return $this->redirect(['index']);
        }

        $news->delete();
        Yii::$app->session->setFlash('success', Yii::t('backendQltt', 'Xóa tin tức thành công'));

        return $this->redirect(['index']);
    }

    /**
     * Finds the AnnouncementCampaign model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AnnouncementCampaign the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LoggerUser::findOne(['id' => $id, 'type' => -1])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('backend', 'The requested page does not exist.'));
    }
}
