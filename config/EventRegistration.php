<?php

namespace Config;

class EventRegistration
{
    protected $eventManager;

    public function __construct($eventManager)
    {
        $this->eventManager = $eventManager;
    }

    public function registerEvents()
    {
//        $this->eventManager->register(StudentRegistered::class, [
//            InitiatePaymentRecord::class
//        ]);
    }
}