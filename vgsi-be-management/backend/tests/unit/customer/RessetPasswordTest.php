<?php

namespace backend\tests\customer;

use common\fixtures\CustomerFixture;
use common\fixtures\UserFixture;
use common\models\Customer;

class RessetPasswordTest extends \Codeception\Test\Unit
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
        $model->setScenario('reset-password');
        $model->load(['id' => '', 'password' => '', 'confirm_password' => ''], '');
        expect($model->validate())->false();
        expect($model->getErrors('id')[0])->contains('ID cannot be blank.');
        expect($model->getErrors('password')[0])->contains('Password cannot be blank.');
        expect($model->getErrors('confirm_password')[0])->contains('Confirm Password cannot be blank.');
    }

    function testIncorectIdValidate()
    {
        #1. ID khong ton tai
        $model = $this->tester->grabFixture('customer', 0);
        $model->setScenario('reset-password');
        $model->load([
            'id'               => 123123,
            'password'         => '123123123',
            'confirm_password' => '123123123',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('id')[0])->contains('ID is invalid.');

        #2. ID không đúng định dạng integer
        $model->load([
            'id'               => "1321a",
            'password'         => '123123123',
            'confirm_password' => '123123123',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('id')[0])->contains('ID must be an integer.');

        #2. ID tai khoan da xoa
        $model = $this->tester->grabFixture('customer', 2);
        $model->setScenario('reset-password');
        $model->load([
            'password'         => '123123123',
            'confirm_password' => '123123123',
                ], '');
        expect($model->validate())->false();
        expect($model->getErrors('id')[0])->contains('ID is invalid.');

        #Tai khoan đã bị khóa
//        $model = $this->tester->grabFixture('customer', 1);
//        $model->setScenario('reset-password');
//        $model->load([
//            'password'         => '123123123',
//            'confirm_password' => '123123123',
//                ], '');
//        expect($model->validate())->false();
//        expect($model->getErrors('id')[0])->contains('ID is invalid.');
    }

//    function testCorrectUpdateSuccessfuly()
//    {
//        $user           = $this->tester->grabFixture('user', 0);
//        $model          = $this->tester->grabFixture('customer', 0);
//        $model->setScenario('reset-password');
//        $model->load([
//            'name'   => 'Phạm Hưởng',
//            'domain' => 'domainActive',
//            'email'  => 'newEmail@gmail.com',
//            'phone'  => '13123213',
//                ], '');
//        $model->user_id = $user->id;
//        $model->status  = 0;
//        expect($model->validate())->true();
//        $chk            = $model->reset - password(false);
//        expect(($chk !== false))->true();
//        $customer       = Customer::findOne(['email' => 'newEmail@gmail.com']);
//
//        expect($customer->validatePassword('123456'))->true();
//    }
}
