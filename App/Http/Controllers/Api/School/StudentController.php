<?php

namespace App\Http\Controllers\Api\School;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;

class StudentController extends Controller
{
    public function getNewStudents(UserRepository $userRepository)
    {
        $teacher = $userRepository->getLoggedInUser();
        
        return $userRepository->getNewStudentsOf($teacher);
    }
}