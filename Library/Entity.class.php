<?php
namespace Library;

class Entity implements \ArrayAccess {
    protected $errors = array();
    protected $vars = array();
    
    public function __construct(array $data = array()) {
        if (!empty($data)) {
            foreach ($data as $var => $value) {
                $this->__set($var, $value);
            }
        }
    }
    
    public function __set($var, $value) {
        $this->vars[$var] = $value;
    }
    
    public function __get($var) {
        if (isset($this->vars[$var]))
            return $this->vars[$var];
    }
    
    public function isNew() {
        return !isset($this->vars['id']);
    }
    
    public function errors() {
        return $this->errors;
    }
    
    public function offsetGet($var) {
        return $this->__get($var);
    }
    
    public function offsetSet($var, $value) {
        // change this if need to set a new variable from the view
        if (isset($this->$var))
            $this->__set($var, $value);
    }
    
    public function offsetExists($var) {
        return isset($this->vars[$var]);
    }
    
    public function offsetUnset($var) {
        throw new \Exception('Impossible to delete any value');
    }
}
?>