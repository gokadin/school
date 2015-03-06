<?php
namespace Applications\School;

class FrontendApplication extends \Library\Application {
    public function __construct() {
        parent::__construct($this);
        
        $this->name = 'School';
    }
    
    public function run() {
        $controller = $this->getController();

        // SETTING RESTRICTIONS

        // SETTING NON DEFAULT LAYOUTS
        
        $controller->execute();
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
}
?>