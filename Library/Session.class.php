<?php
namespace Library;

session_start();

class Session {
    public function set($var, $value)
    {
        $_SESSION[$var] = $value;
    }
    
    public function get($var)
    {
        if ($this->exists($var)) 
            return $_SESSION[$var];
    }

    public function exists($var)
    {
        return isset($_SESSION[$var]);
    }
    
    public function logout()
    {
        session_destroy();
    }
}
?>