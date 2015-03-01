<?php
namespace Library;

session_start();

class User extends ApplicationComponent implements \ArrayAccess {
    public function __set($var, $value) {
        $_SESSION[$var] = $value;
    }
    
    public function __get($var) {
        if ($this->exists($var)) 
            return $_SESSION[$var];
    }

    public function __isset($var) {
        return isset($_SESSION[$var]);
    }
    
    public function exists($var) {
        return isset($_SESSION[$var]);
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function offsetGet($var) {
        return $this->__get($var);
    }
    
    public function offsetSet($var, $value) {
        return $this->__set($var, $value);
    }
    
    public function offsetExists($var) {
        return $this->exists($var);
    }
    
    public function offsetUnset($var) {
        throw new \Exception('cannot unset');
    }
}
?>