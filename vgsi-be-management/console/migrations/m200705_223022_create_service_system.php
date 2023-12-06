<?php

use yii\db\Migration;

/**
 * Class m200705_223022_create_service_system
 */
class m200705_223022_create_service_system extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $service_array = [];
        $service_array[] = [
            1,
            'Nước sinh hoạt',
            'Cung cấp tính năng quản lý và tạo phí tự động phần nước sinh hoạt. Tính năng hỗ trợ cấu hình mức phí bậc thang theo căn hộ, hỗ trợ cấu hình mức phú bậc thang theo người dùng.',
            '/uploads/service-default/1561017207-bi-s-1-1710-1-768x511.jpg',
            1,
            1,
            0,
            0,
            '/water',
            'icWater',
            '#ffb314'
        ];

        $service_array[] = [
            2,
            'Gửi xe',
            'Module quản lý và tạo phí tự động cho hệ thống gửi xe.',
            '/uploads/service-default/1561017543-image003-1440079251.jpg',
            1,
            4,
            0,
            0,
            '/moto-packing',
            'icRideParking',
            '#375891'
        ];

        $service_array[] = [
            3,
            'Dịch vụ điện',
            'Ban quan lý quản lý điện và thu phí điện sử dụng trong căn hộ của cư dân',
            '/uploads/service-default/1561017663-120808luoidien.jpg',
            1,
            0,
            0,
            0,
            '/electric',
            'icPower',
            '#3e82f7'
        ];

        $service_array[] = [
            4,
            'Tiện ích',
            'Ban quản lý tòa nhà cung cấp các dịch vụ tiện ích miễn phí chỉ dành cho cư dân trong tòa nhà sử dụng: sân chơi trẻ em, bể bơi ngoài trời,...',
            '/uploads/service-default/1561017834-san-choi.jpg',
            1,
            3,
            1,
            0,
            '/utility-free',
            'icManagement',
            ''
        ];

        $service_array[] = [
            5,
            'Phí quản lý',
            'Cung cấp hệ thống quản lý và tạo phí quản lý dịch vụ. Giúp BQL có thể cấu hình tạo phí dịch vụ tự động theo tháng, quí.',
            '/uploads/service-default/1562050515-ljh1475072430.png',
            1,
            2,
            0,
            0,
            '/apartment-fee',
            'icManagement',
            '#fe5252'
        ];

        $service_array[] = [
            6,
            'Nợ cũ chuyển giao',
            'Giữa các giai doạn chuyển đổi ban quản lý, hoạc hệ thống vận hành quản lý. Sẽ phát sinh các khoản thu cần thu thêm thì sẽ sử dụng loại dịch vụ này',
            '/uploads/service-default/1589270976-img-blog4.png',
            1,
            5,
            0,
            0,
            '/old_debit',
            'icOldDebit',
            '#ffb328'
        ];

        Yii::$app->db->createCommand()->batchInsert('service', [
            'id', 'name', 'description', 'logo', 'status', 'service_type', 'type', 'type_target', 'base_url', 'icon_name', 'color'
        ], $service_array)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200705_223022_create_service_system cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200705_223022_create_service_system cannot be reverted.\n";

        return false;
    }
    */
}
