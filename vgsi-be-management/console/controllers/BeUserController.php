<?php

namespace console\controllers;

use common\models\Apartment;
use common\models\BuildingArea;
use common\models\BuildingCluster;
use common\models\ManagementUser;
use common\models\ServiceMapManagement;
use common\models\ServicePaymentFee;
use common\models\User;
use Exception;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

class BeUserController extends Controller
{
    public function actionUpdate($username, $pass){
        $user = User::findOne(['username' => $username]);
        if(!empty($user)){
            $user->setPassword($pass);
            if(!$user->save()){
                echo "Error";
            }else{
                echo "Success";
            }
        }
    }
    public function actionUpdateParentPath()
    {
        $buildingAreas = BuildingArea::find()->where(['is_deleted' => BuildingArea::NOT_DELETED])->all();
        foreach ($buildingAreas as $buildingArea){
            $apartments = Apartment::find()->where(['building_area_id' => $buildingArea->id, 'is_deleted' => Apartment::NOT_DELETED])->all();
            foreach ($apartments as $apartment){
                $apartment->parent_path = $buildingArea->parent_path . $buildingArea->name .'/';
                $apartment->save();
            }
        }
    }

    public function actionCreateBuildingCluster($name, $domain)
    {
        $cluster = new BuildingCluster();
        $cluster->name = $name;
        $cluster->domain = $domain;
        $cluster->status = 1;
        if ($cluster->save()) {
            echo "Cluster created! => id: $cluster->id \n";
            return 0;
        } else {
            Yii::error($cluster->getErrors());
            VarDumper::dump($cluster->getErrors());
            throw new Exception("Cannot create User!");
        }
    }

    public function actionCreateManagementUser($email, $password, $cluster_id)
    {
        $user = new ManagementUser();
        $user->email = $email;
        $user->setPassword($password);
        $user->status = 1;
        $user->auth_key = '';
        $user->building_cluster_id = $cluster_id;
        $user->auth_group_id = 0;
        if ($user->save()) {
            echo "User created!\n";
            return 0;
        } else {
            Yii::error($user->getErrors());
            VarDumper::dump($user->getErrors());
            throw new Exception("Cannot create User!");
        }
    }

    public function actionUpdateManagementUser($email, $password, $building_cluster_id)
    {
        $user = ManagementUser::findByEmailAndClusterId($email, $building_cluster_id);
        if ($user) {
            $user->setPassword($password);
            $user->update();
            echo "Change pass user success!\n";
        } else {
            echo "User not found!";
        }
    }
}