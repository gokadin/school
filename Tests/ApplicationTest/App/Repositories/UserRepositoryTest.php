<?php

namespace ApplicationTest\App\Repositories;

use App\Repositories\UserRepository;
use Tests\ApplicationTest\BaseTest;

class UserRepositoryTest extends BaseTest
{
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        $this->setUpDatamapper([
            \App\Domain\Subscriptions\Subscription::class
        ]);

        $this->repository = new UserRepository($this->dm);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->flushRedis(14);
        $this->db->dropAll();
    }

    public function testPreRegisterTeacher()
    {
        // Act
        $id = $this->repository->preRegisterTeacher([
            'firstName' => 'fname',
            'lastName' => 'lname',
            'email' => 'an@email.com',
            'subscriptionType' => 1
        ]);
//        $tempTeacher = $repository->findTempTeacher($id);

        // Assert
        $subscription = $this->db->table('subscriptions')->select()[0];
        $this->assertNotNull($subscription);
        $this->assertEquals(1, $subscription['type']);
//        $this->assertEquals('fname', $tempTeacher->first_name);
//        $this->assertEquals('lname', $tempTeacher->last_name);
//        $this->assertEquals('an@email.com', $tempTeacher->email);
    }
}