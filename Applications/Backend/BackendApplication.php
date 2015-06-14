<?php namespace Applications\Backend;

use Library\Application;

class BackendApplication extends Application
{
    public function run()
    {
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