<?php
namespace Library;

class Page extends ApplicationComponent  {
    protected $contentFile;
    protected $layoutFile;
    protected $vars = array();
    
    public function addVar($var, $value) {
        if (!is_string($var) || is_numeric($var) || empty($var)) {
            throw new \InvalidArgumentException('Invalid variable name');
        }
        
        $this->vars[$var] = $value;
    }
    
    public function getGeneratedPage() {
        if (!file_exists($this->contentFile)) {
            throw new \RuntimeException('Content file does not exist');
        }
        
        $user = $this->app->user();
        
        extract($this->vars);

        ob_start();

        require $this->contentFile;

        $content = ob_get_clean();

        ob_start();
        if (empty($this->layoutFile))
            $this->setLayoutFile('layout.php');
        if (file_exists($this->layoutFile))
            require $this->layoutFile;

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
?>