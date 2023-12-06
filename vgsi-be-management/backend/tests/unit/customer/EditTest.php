<?php

namespace backend\tests\customer;

use common\fixtures\CustomerFixture;
use common\fixtures\UserFixture;
use common\models\Customer;

class EditTest extends \Codeception\Test\Unit
{

    /**
     * @var \backend\tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'customer' => [
                'class'    => CustomerFixture::className(),
                'dataFile' => codecept_data_dir() . 'customer\customerOld.php'
            ],
            'user'     => [
                'class'    => UserFixture::className(),
                'dataFile' => codecept_data_dir() . 'customer\admin.php'
            ]
        ]);
    }

//    protected function _after()
//    {
//        
//    }

    public function testCorrectData()
    {
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load(['id' => '', 'name' => '', 'phone' => '', 'email' => '', 'domain' => ''], '');
        expect($model->validate())->false();
        expect($model->getErrors('id')[0])->contains('ID cannot be blank.');
        expect($model->getErrors('name')[0])->contains('Name cannot be blank.');
        expect($model->getErrors('email')[0])->contains('Email cannot be blank.');
        expect($model->getErrors('domain')[0])->contains('Domain cannot be blank.');
    }

    function testIncorectIdValidate()
    {
        #1. ID khong ton tai
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'id'     => 199,
            'name'   => 'Phạm Hưởng',
            'domain' => 'domainNew',
            'email'  => 'newEmail@gmail.com',
            'phone'  => '0868605579',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('id')[0])->contains('ID is invalid.');

        #2. ID không đúng định dạng integer
        $model->load([
            'id'     => "1321a",
            'name'   => 'Phạm Hưởng',
            'domain' => 'domainNew',
            'email'  => 'newEmail@gmail.com',
            'phone'  => '0868605579',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('id')[0])->contains('ID must be an integer.');

        #2. ID tai khoan da xoa
        $model = $this->tester->grabFixture('customer', 2);
        $model->setScenario('update');
        $model->load([
            'name'   => 'Phạm Hưởng',
            'domain' => 'domainNew',
            'email'  => 'newEmail@gmail.com',
            'phone'  => '0868605579',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('id')[0])->contains('ID is invalid.');
    }

    public function testInCorrectEmailValidate()
    {

        #2. Nhập email trùng email tài khoản đang hoạt động khac
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'   => 'Phạm Hưởng',
            'domain' => 'domainActive',
            'email'  => 'phamvanhuong.it@gmail.com',
            'phone'  => '0868605579',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('email')[0])->contains('Email is exists.');

        #3. Nhập email trùng email tài khoản đã bị khóa
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'   => 'Phạm Hưởng',
            'domain' => 'domainBan',
            'email'  => 'a@gmail.com',
            'phone'  => '0868605579',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('email')[0])->contains('Email is exists.');


        #4.Nhập email trùng email của tài khoản đã xóa
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'   => 'Phạm Hưởng',
            'domain' => 'domainDeleted',
            'email'  => 'b@gmail.com',
            'phone'  => '0868605579',
                ], '');
        expect($model->validate())->true();

        #5. Nhập email đăng ký chưa tồn tại trong hệ thống
        $model = new Customer();
        $model->setScenario('update');
        $model->load([
            'id'     => 1,
            'name'   => 'Phạm Hưởng',
            'domain' => 'domainNew',
            'email'  => 'correctemail@gmail.com',
            'phone'  => '0868605579',
                ], '');

        expect($model->validate())->true();

        #6. Nhập email không đúng định dạng
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'   => 'Phạm Hưởng',
            'domain' => 'huong03',
            'email'  => 'bsdgibg',
            'phone'  => '0868605579',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('email')[0])->contains('Email is not a valid email address.');
    }

    function testIncorrectDomainValidate()
    {

        #1. Domain nhập trùng domain tài khoản đã xóa
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'domain' => 'domainDeleted',
            "email"  => 'aaaaaaa@gmail.com',
            'name'   => 'huongsad',
                ], '');
        expect($model->validate())->true();

        #2. Domain nhập trùng domain tài khoản đã khóa
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'domain' => 'domainBan',
            "email"  => 'aaaaaaa@gmail.com',
            'name'   => 'huongsad',
                ], '');

        expect($model->validate())->false();
        expect($model->getErrors('domain')[0])->contains('Domain is exists.');


        #3. Domain nhập mới chưa tồn tại trong hệ thống
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'domain'           => 'domainNew',
            "email"            => 'aaaaaaa@gmail.com',
            'name'             => 'huongsad',
            'password'         => '123213',
            'confirm_password' => '123213'
                ], '');
        expect($model->validate())->true();


        #3. Domain không thay đổi
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'domain' => 'domainActive',
            "email"  => 'newEmail@gmail.com',
            'name'   => 'huongsad',
                ], '');

        $model->validate();
        expect($model->validate())->true();
    }

    function testIncorrectPhoneValidate()
    {
        
    }

    function testIncorrectUserCreateValidate()
    {
        #1. ID admin incorrect

        $model          = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'newDomain',
            'email'            => 'newEmail@gmail.com',
            'phone'            => '0868605579',
            'password'         => 'admin123',
            'confirm_password' => 'admin123',
                ], '');
        $model->user_id = 10;
        expect($model->validate())->false();
        expect($model->getErrors('user_id')[0])->contains('Admin ID is invalid.');


        #ID admin tao hop le
        $user           = $this->tester->grabFixture('user', 0);
        $model          = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'newDomain',
            'email'            => 'newEmail@gmail.com',
            'phone'            => '0868605579',
            'password'         => 'admin123',
            'confirm_password' => 'admin123',
                ], '');
        $model->user_id = $user->id;
        expect($model->validate())->true();
    }

    function testIncorrectStatusValidate()
    {
        #ID admin tao hop le
        $user           = $this->tester->grabFixture('user', 0);
        $model          = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'newDomain',
            'email'            => 'newEmail@gmail.com',
            'phone'            => '0868605579',
            'password'         => 'admin123',
            'confirm_password' => 'admin123',
                ], '');
        $model->user_id = $user->id;
        $model->status  = 'sda';
        expect($model->validate())->false();
        expect($model->getErrors('status')[0])->contains('Status must be an integer.');

        #
        $user           = $this->tester->grabFixture('user', 0);
        $model          = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'newDomain',
            'email'            => 'newEmail@gmail.com',
            'phone'            => '0868605579',
            'password'         => 'admin123',
            'confirm_password' => 'admin123',
                ], '');
        $model->user_id = $user->id;
        $model->status  = 4;
        expect($model->validate())->false();
        expect($model->getErrors('status')[0])->contains('Status is invalid.');

        #
        $user           = $this->tester->grabFixture('user', 0);
        $model          = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'newDomain',
            'email'            => 'newEmail@gmail.com',
            'phone'            => '0868605579',
            'password'         => 'admin123',
            'confirm_password' => 'admin123',
                ], '');
        $model->user_id = $user->id;
        $model->status  = 0;
        expect($model->validate())->true();
    }

    function testCorrectUpdateSuccessfuly()
    {
        $user           = $this->tester->grabFixture('user', 0);
        $model          = $this->tester->grabFixture('customer', 0);
        $model->setScenario('update');
        $model->load([
            'name'   => 'Phạm Hưởng',
            'domain' => 'domainActive',
            'email'  => 'newEmail@gmail.com',
            'phone'  => '13123213',
                ], '');
        $model->user_id = $user->id;
        $model->status  = 0;
        expect($model->validate())->true();
        $chk            = $model->update(false);
        expect(($chk !== false))->true();
        $customer       = Customer::findOne(['email' => 'newEmail@gmail.com']);
        //Check password login
        expect($customer->validatePassword('123456'))->true();
    }

}
