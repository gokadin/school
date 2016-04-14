<?php

namespace Tests\ApplicationTest;

use Library\DataMapper\Database\SchemaTool;
use Library\DataMapper\DataMapper;
use Library\Database\Database;
use Library\Testing\FakerTestTrait;
use Tests\TestCase;
use App\Domain\Setting\TeacherSettings;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\Teacher;
use App\Domain\Common\Address;
use App\Domain\School\School;

abstract class BaseTest extends TestCase
{
    use FakerTestTrait;

    protected $app;
    protected $database;

    /**
     * @var DataMapper
     */
    protected $dm;

    /**
     * @var Teacher
     */
    protected $teacher;

    public function setUp()
    {
        parent::setUp();

        date_default_timezone_set('America/Montreal');

        $this->setUpFaker();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function setUpDatabase()
    {
        $this->database = new Database([
            'driver' => 'mysql',
            'mysql' => [
                'host' => env('DATABASE_HOST'),
                'database' => env('DATABASE_NAME'),
                'username' => env('DATABASE_USERNAME'),
                'password' => env('DATABASE_PASSWORD')
            ]
        ]);
    }

    public function setUpDatamapper(array $classes)
    {
        $dmConfig = [
            'mappingDriver' => 'annotation',
            'databaseDriver' => 'mysql',
            'mysql' => [
                'host' => env('DATABASE_HOST'),
                'database' => env('DATABASE_NAME'),
                'username' => env('DATABASE_USERNAME'),
                'password' => env('DATABASE_PASSWORD')
            ],
            'classes' => $classes
        ];

        $this->dm = new DataMapper($dmConfig);

        $schemaTool = new SchemaTool($dmConfig);
        $schemaTool->create();

        $this->addTearDownCallback(function() use ($schemaTool) {
            $schemaTool->drop();
            $this->dao = null;
        });
    }

    protected function setUpTeacher()
    {
        $subscription = new Subscription(1);
        $this->dm->persist($subscription);
        $teacherAddress = new Address();
        $this->dm->persist($teacherAddress);
        $schoolAddress = new Address();
        $this->dm->persist($schoolAddress);
        $school = new School($this->faker->word, $schoolAddress);
        $this->dm->persist($school);
        $teacherSettings = new TeacherSettings('[]');
        $this->dm->persist($teacherSettings);

        $this->teacher = new Teacher(
            $this->faker->firstName,
            $this->faker->lastName,
            $this->faker->email,
            $this->faker->password,
            $subscription,
            $teacherAddress,
            $school,
            $teacherSettings
        );
        $this->dm->persist($this->teacher);

        $this->dm->flush();
    }
}