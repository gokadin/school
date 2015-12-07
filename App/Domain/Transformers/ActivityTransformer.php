<?php

namespace App\Domain\Transformers;

class ActivityTransformer extends Transformer
{
    public function transform($activity, array $overrides = [])
    {
        return array_merge([
            'name' => $activity->name(),
            'rate' => $activity->rate(),
            'period' => $activity->period(),
            'location' => $activity->location(),
            'studentCount' => $activity->students()->count()
        ], $overrides);
    }
}