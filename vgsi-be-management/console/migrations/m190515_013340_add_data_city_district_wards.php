<?php

use yii\db\Migration;

/**
 * Class m190515_013340_add_data_city_district_wards
 */
class m190515_013340_add_data_city_district_wards extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        //reset dữ liệu

        $dbSchema = dirname((dirname(__DIR__))) . "/db_desgin/city.json";
        if (!file_exists($dbSchema)) {
            echo "***** DB schema not found: " . $dbSchema . " *****\n";
            return false;
        } else {
            echo "DB schema found: " . $dbSchema . ", applying...\n";
        }
        $cached = fopen($dbSchema, 'r');
        // echo 'dsds';die;
        $html = fread($cached, filesize($dbSchema));
        $data = json_decode($html);
        // print_r($data[0]);die;
        $city_array = [];
        $city_id = 1;

        $district_array = [];
        $district_id = 1;

        $wards_array = [];
        $wards_id = 1;
        foreach ($data as $city_data) {
            $city_array[] = [$city_id, $city_data->name];
            $arr_district = $city_data->list;
            foreach ($arr_district as $district_data) {
                $district_array[] = [$district_id, $district_data->name, $city_id];
                $arr_wards = $district_data->list;
                foreach ($arr_wards as $wards_name) {
                    $wards_array[] = [$wards_id, $wards_name, $city_id, $district_id];
                    $wards_id++;
                }
                $district_id++;
            }
            $city_id++;
        }

        Yii::$app->db->createCommand()->batchInsert('city', [
            'id', 'name'
        ], $city_array)->execute();

        Yii::$app->db->createCommand()->batchInsert('district', [
            'id', 'name', 'city_id'
        ], $district_array)->execute();

        Yii::$app->db->createCommand()->batchInsert('wards', [
            'id', 'name', 'city_id', 'district_id'
        ], $wards_array)->execute();


        echo "Schema imported successfully";

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190515_013340_add_data_city_district_wards cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190515_013340_add_data_city_district_wards cannot be reverted.\n";

        return false;
    }
    */
}
