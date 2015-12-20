<?php

namespace App\Domain\Transformers;

class ActivityTransformer extends Transformer
{
    public function transform($activity)
    {
        return $this->applyModifiers([
            'id' => $activity->getId(),
            'name' => $activity->name(),
            'rate' => $activity->rate(),
            'period' => $activity->period(),
            'location' => $activity->location(),
            'studentCount' => $activity->students()->count()
        ]);
    }
}