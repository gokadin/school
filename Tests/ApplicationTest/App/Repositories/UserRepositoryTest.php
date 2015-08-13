<?php

namespace ApplicationTest\App\Repositories;

use App\Repositories\UserRepository;
use Library\Facades\Redis;
use Tests\ApplicationTest\BaseTest;

class UserRepositoryTest extends BaseTest
{
    public function tearDown()
    {
        parent::tearDown();

        Redis::getRedis()->flushdb();
    }

    public function testPreRegisterTeacher()
    {
        // Arrange
        $repository = new UserRepository();

        // Act
        $id = $repository->preRegisterTeacher([
            'firstName' => 'fname',
            'lastName' => 'lname',
            'email' => 'an@email.com',
            'subscriptionType' => 1
        ]);
        $tempTeacher = $repository->findTempTeacher($id);

        // Assert
        $this->assertEquals('fname', $tempTeacher->first_name);
        $this->assertEquals('lname', $tempTeacher->last_name);
        $this->assertEquals('an@email.com', $tempTeacher->email);
    }
}