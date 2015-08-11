<?php

namespace Library\Http;

class ViewFactory
{
    protected $layoutFile;
    protected $sections;
    protected $currentSectionName;

    public function __construct()
    {
        $this->layoutFile = null;
        $this->sections = array();
        $this->currentSectionName = null;
    }

    public function setLayoutFile($fileName)
    {
        $this->layoutFile = $fileName;
    }

    public function hasLayout()
    {
        return !is_null($this->layoutFile);
    }

    public function getLayoutFile()
    {
        return $this->layoutFile;
    }

    public function startSection($name)
    {
        $this->currentSectionName = $name;

        ob_start();
    }

    public function endSection()
    {
        if (is_null($this->currentSectionName))
        {
            return;
        }

        $content = ob_get_clean();

        $this->sections[$this->currentSectionName] = $content;
    }

    public function getSection($name)
    {
        if (isset($this->sections[$name]))
        {
            return $this->sections[$name];
        }
    }
}