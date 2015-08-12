<?php

namespace ApplicationTest\App\Repositories;

use App\Repositories\UserRepository;
use Tests\ApplicationTest\BaseTest;

class UserRepositoryTest extends BaseTest
{
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

        // Assert
        $this->assertEquals('fname', $repository->findTempTeacher($id)['first_name']);
        $this->assertEquals('lname', $repository->findTempTeacher($id)['last_name']);
        $this->assertEquals('an@email.com', $repository->findTempTeacher($id)['email']);
    }
}