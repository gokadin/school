<?php namespace Library;

class Page extends ApplicationComponent  {
    protected $contentFile;
    protected $layoutFile;
    protected $vars = array();
    
    public function add($var, $value = null)
    {
        if ($value != null)
        {
            if (!is_string($var))
                return;

            $this->vars[$var] = $value;
            return;
        }

        if (!is_array($var))
            return;

        foreach ($var as $key => $v)
            $this->vars[$key] = $v;
    }
    
    public function getGeneratedPage() {
        if (!file_exists($this->contentFile))
            throw new \RuntimeException('Content file does not exist');

        extract($this->vars);

        ob_start();

        require $this->contentFile;

        $content = ob_get_clean();

        ob_start();

        if (empty($this->layoutFile))
            $this->setLayoutFile('layout.php');

        if (file_exists($this->layoutFile))
            require $this->layoutFile;
        else
            echo $content;

        return ob_get_clean();
    }
    
    public function setContentFile($contentFile)
    {
        $this->contentFile = $contentFile;
    }
    
    public function setLayoutFile($layoutFile)
    {
        $this->layoutFile = $layoutFile;
    }
}
