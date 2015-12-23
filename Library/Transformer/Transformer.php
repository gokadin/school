<?php

namespace Library\Transformer;

class Transformer
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    private $definitions = [];

    public function __construct($config)
    {
        $this->registerDefinitions($config);
    }

    /**
     * @param $config string
     */
    private function registerDefinitions($config)
    {
        $this->definitions = $config;
    }

    /**
     * @param $class string
     * @return $this Transformer
     * @throws TransformerException
     */
    public function of($class)
    {
        if (!isset($this->definitions[$class]))
        {
            throw new TransformerException('Transformation for class '.$class.' is not defined.');
        }

        $this->class = $class;

        return $this;
    }

    /**
     * @param array|object
     * @return array|null
     */
    public function transform($items)
    {
        $result = is_array($items)
            ? $this->transformCollection($items)
            : $this->transformSingle($items);

        $this->clear();

        return json_encode($result);
    }

    /**
     * @param array $items
     * @return array
     */
    private function transformCollection(array $items)
    {
        return array_map([$this, 'transformSingle'], $items);
    }

    /**
     * @param $item
     * @return array
     */
    private function transformSingle($item)
    {
        $result = [];

        foreach ($this->definitions[$this->class] as $key => $closure)
        {
            if (sizeof($this->only) > 0 && !in_array($key, $this->only))
            {
                continue;
            }

            $result[$key] = $closure($item);
        }

        return $result;
    }

    /**
     * @param array $only
     * @return $this Transformer
     */
    public function only(array $only)
    {
        $this->only = $only;

        return $this;
    }

    /**
     * Clear the transformer for the next call
     */
    private function clear()
    {
        $this->class = null;
        $this->only = [];
    }
}