<?php namespace Library;

session_start();

class Session
{
    const ERRORS_KEY = 'errors';
    const SHOULD_CLEAR_ERRORS_KEY = 'shouldClearErrors';

    public function __construct()
    {
        if (!$this->shouldClearErrors())
            $_SESSION[self::SHOULD_CLEAR_ERRORS_KEY] = true;
        else
            $this->clearErrors();
    }

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

    public function remove($key)
    {
        if (isset($_SESSION[$key]))
            unset($_SESSION[$key]);
    }
    
    public function logout()
    {
        session_destroy();
    }

    public function setErrors($errors)
    {
        $_SESSION[self::ERRORS_KEY] = $errors;
        $_SESSION[self::SHOULD_CLEAR_ERRORS_KEY] = false;
    }

    public function getErrors()
    {
        if (isset($_SESSION[self::ERRORS_KEY]))
            return $_SESSION[self::ERRORS_KEY];

        return null;
    }

    public function hasErrors()
    {
        return isset($_SESSION[self::ERRORS_KEY]);
    }

    public function clearErrors()
    {
        $this->remove(self::ERRORS_KEY);
    }

    public function shouldClearErrors()
    {
        if (!isset($_SESSION[self::SHOULD_CLEAR_ERRORS_KEY]))
            return false;

        return $_SESSION[self::SHOULD_CLEAR_ERRORS_KEY];
    }
}
