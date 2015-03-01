<?php
namespace Applications\Backend;

class BackendApplication extends \Library\Application {
    public function __construct() {
        parent::__construct($this);
        
        $this->name = 'Backend';
    }
    
    public function run() {
        $controller = $this->getController();

        // SETTING RESTRICTIONS
        //if ($this->user->authenticated != true || $this->user->type != 1)
            //$this->httpResponse->redirect('/');
        
        // SETTING NON DEFAULT LAYOUTS
        
        
        $controller->execute();
        
        $this->httpResponse->setPage($controller->page());
        $this->httpResponse->send();
    }
}
?>