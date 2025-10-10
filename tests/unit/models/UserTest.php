<?php

namespace tests\unit\models;

use app\auth\UserIdentity;

class UserTest extends \Codeception\Test\Unit
{
    public function testFindUserById()
    {
        verify($user = UserIdentity::findIdentity(100))->notEmpty();
        verify($user->user->username)->equals('admin');

        verify(UserIdentity::findIdentity(999))->empty();
    }

    public function testFindUserByAccessToken()
    {
        verify($user = UserIdentity::findIdentityByAccessToken('100-token'))->notEmpty();
        verify($user->user->username)->equals('admin');

        verify(UserIdentity::findIdentityByAccessToken('non-existing'))->empty();
    }

    public function testFindUserByUsername()
    {
        verify($user = UserIdentity::findByUsername('admin'))->notEmpty();
        verify(UserIdentity::findByUsername('not-admin'))->empty();
    }

    /**
     * @depends testFindUserByUsername
     */
    public function testValidateUser()
    {
        $user = UserIdentity::findByUsername('admin');
        verify($user->validateAuthKey('test100key'))->notEmpty();
        verify($user->validateAuthKey('test102key'))->empty();

        verify($user->validatePassword('admin'))->notEmpty();
        verify($user->validatePassword('123456'))->empty();        
    }

}
