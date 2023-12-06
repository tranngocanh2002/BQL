<?php

namespace backend\tests\customer;

use common\fixtures\CustomerFixture;
use common\fixtures\UserFixture;
use common\models\Customer;

class CreateTest extends \Codeception\Test\Unit
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
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([], '');
        expect($model->validate())->false();
        expect($model->getErrors('name')[0])->contains('Name cannot be blank.');
        expect($model->getErrors('email')[0])->contains('Email cannot be blank.');
        expect($model->getErrors('domain')[0])->contains('Domain cannot be blank.');
        expect($model->getErrors('password')[0])->contains('Password cannot be blank.');
        expect($model->getErrors('confirm_password')[0])->contains('Confirm Password cannot be blank.');
    }

    /**
     * #2. Nhập email trùng email tài khoản đang hoạt động
     * #3. Nhập email trùng email tài khoản đã bị khóa
     * #4.Nhập email trùng email của tài khoản đã xóa
     * 
     */
    public function testInCorrectEmailValidate()
    {

        #2. Nhập email trùng email tài khoản đang hoạt động
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'     => 'Phạm Hưởng',
            'domain'   => 'huong03',
            'email'    => 'phamvanhuong.hd@gmail.com',
            'phone'    => '0868605579',
            'password' => '123456'
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('email')[0])->contains('Email is exists.');

        #3. Nhập email trùng email tài khoản đã bị khóa
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'     => 'Phạm Hưởng',
            'domain'   => 'huong03',
            'email'    => 'a@gmail.com',
            'phone'    => '0868605579',
            'password' => '123456'
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('email')[0])->contains('Email is exists.');


        #4.Nhập email trùng email của tài khoản đã xóa
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'huong03',
            'email'            => 'b@gmail.com',
            'phone'            => '0868605579',
            'password'         => '123456',
            'confirm_password' => '123456',
                ], '');
        expect($model->validate())->true();

        #5. Nhập email đăng ký chưa tồn tại trong hệ thống
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'huong03',
            'email'            => 'correctemail@gmail.com',
            'phone'            => '0868605579',
            'password'         => '123456',
            'confirm_password' => '123456'
                ], '');
        expect($model->validate())->true();

        #6. Nhập email không đúng định dạng
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'     => 'Phạm Hưởng',
            'domain'   => 'huong03',
            'email'    => 'bsdgibg',
            'phone'    => '0868605579',
            'password' => '123456'
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('email')[0])->contains('Email is not a valid email address.');
    }

    function testIncorrectDomainValidate()
    {

        #1. Domain nhập trùng domain tài khoản đã xóa
        $model = new Customer();
        $model->setScenario('insert');
        $model->load(['domain' => 'domainDeleted', "email" => 'aaaaaaa@gmail.com', 'name' => 'huongsad', 'password' => '123213', 'confirm_password' => '123213',], '');
        expect($model->validate())->true();

        #2. Domain nhập trùng domain tài khoản đã khóa
        $model = new Customer();
        $model->setScenario('insert');
        $model->load(['domain' => 'domainBan', "email" => 'aaaaaaa@gmail.com', 'name' => 'huongsad', 'password' => '123213'], '');

        expect($model->validate())->false();
        expect($model->getErrors('domain')[0])->contains('Domain is exists.');


        #3. Domain nhập trùng domain tài khoản đang hoạt động
        $model = new Customer();
        $model->setScenario('insert');
        $model->load(['domain' => 'domainActive', "email" => 'aaaaaaa@gmail.com', 'name' => 'huongsad', 'password' => '123213'], '');

        $model->validate();
        $model->setScenario('insert');
        expect($model->validate())->false();
        expect($model->getErrors('domain')[0])->contains('Domain is exists.');

        #3. Domain nhập mới chưa tồn tại trong hệ thống
        $model = new Customer();
        $model->setScenario('insert');
        $model->load(['domain' => 'domainNew', "email" => 'aaaaaaa@gmail.com', 'name' => 'huongsad', 'password' => '123213', 'confirm_password' => '123213'], '');
        expect($model->validate())->true();
    }

    function testIncorrectPhoneValidate()
    {
        
    }

    function testIncorrectPasswordValidate()
    {
        #1. Password có len <3
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'     => 'Phạm Hưởng',
            'domain'   => 'newDomain',
            'email'    => 'newEmail@gmail.com',
            'phone'    => '0868605579',
            'password' => 'ad'
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('password')[0])->contains('Password should contain at least 3 characters.');


        #2. Password có len >20
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'     => 'Phạm Hưởng',
            'domain'   => 'newDomain',
            'email'    => 'newEmail@gmail.com',
            'phone'    => '0868605579',
            'password' => 'ASDFGHJKL:ERTYUIOXCVNMERTYU$%^&*$%^&*()'
                ], '');

        expect($model->validate())->false();
        expect($model->getErrors('password')[0])->contains('Password should contain at most 20 characters.');

        #3. Password confirm in correct
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'newDomain',
            'email'            => 'newEmail@gmail.com',
            'phone'            => '0868605579',
            'password'         => 'admin123',
            'confirm_password' => 'admin1_=',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('password')[0])->contains('Password must be equal to "Confirm Password".');

        #3. Password confirm correct
        $model = new Customer();
        $model->setScenario('insert');
        $model->load([
            'name'             => 'Phạm Hưởng',
            'domain'           => 'newDomain',
            'email'            => 'newEmail@gmail.com',
            'phone'            => '0868605579',
            'password'         => 'admin123',
            'confirm_password' => 'admin123',
                ], '');
        expect($model->validate())->true();
    }

    function testIncorrectUserCreateValidate()
    {
        #1. ID admin incorrect
        $model          = new Customer();
        $model->setScenario('insert');
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
        $model          = new Customer();
        $model->setScenario('insert');
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
        $model          = new Customer();
        $model->setScenario('insert');
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


        $user           = $this->tester->grabFixture('user', 0);
        $model          = new Customer();
        $model->setScenario('insert');
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


        $user           = $this->tester->grabFixture('user', 0);
        $model          = new Customer();
        $model->setScenario('insert');
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

    function testCorrectCreateSuccessfuly()
    {
        $user           = $this->tester->grabFixture('user', 0);
        $model          = new Customer();
        $model->setScenario('insert');
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
        $model->setPassword($model->password);
        expect(($customerInfo   = $model->save(false)))->true();

        $customer = Customer::findOne(['email' => 'newEmail@gmail.com']);
        //Check password login
        expect($customer->validatePassword('admin123'))->true();
    }

}
