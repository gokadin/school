<?php

namespace App\Domain\Transformers;

abstract class Transformer
{
    private $only;

    public function transformCollection(array $items)
    {
        return array_map([$this, 'transform'], $items);
    }

    abstract public function transform($item);

    public function only(array $only)
    {
        $this->only = $only;

        return $this;
    }

    protected function applyModifiers(array $result)
    {
        return $this->applyOnly($result);
    }

    private function applyOnly(array $result)
    {
        foreach ($result as $key => $value)
        {
            if (!in_array($key, $this->only))
            {
                unset($result[$key]);
            }
        }

        return $result;
    }
}