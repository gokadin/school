<?php namespace Library;

if(Config::get('testing') != 'true' && Config::get('frameworkTesting') != 'true')
{
    session_start();
}

class Session
{
    const ERRORS_KEY = 'errors';
    const SHOULD_CLEAR_ERRORS_KEY = 'shouldClearErrors';
    const FLASH_KEY = 'flash';
    const FLASH_TYPE_KEY = 'flashType';
    const FLASH_DURATION_KEY = 'flashDuration';
    const SHOULD_CLEAR_FLASH_KEY = 'shouldClearFlash';

    public function __construct()
    {
        if (!$this->shouldClearErrors())
            $_SESSION[self::SHOULD_CLEAR_ERRORS_KEY] = true;
        else
            $this->clearErrors();

        if (!$this->shouldClearFlash())
            $_SESSION[self::SHOULD_CLEAR_FLASH_KEY] = true;
        else
            $this->clearFlash();
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

    public function login($id, $type)
    {
        $_SESSION['id'] = $id;
        $_SESSION['type'] = $type;
        $_SESSION['authenticated'] = true;
        $_SESSION['token'] = $this->generateToken();
    }

    public function generateToken()
    {
        return md5('G2s92!dK2!185fr0?Se0');
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

    public function setFlash($string, $type = "success", $duration = 0)
    {
        if ($type != "success" || $type != "error")
            $type = "success";
            
        if (!is_int($duration) || $duration < 0)
            $duration = 0;
        
        $_SESSION[self::FLASH_KEY] = $string;
        $_SESSION[self::FLASH_TYPE_KEY] = $type;
        $_SESSION[self::FLASH_DURATION_KEY] = $duration;
        $_SESSION[self::SHOULD_CLEAR_FLASH_KEY] = false;
    }

    public function getErrors()
    {
        if (isset($_SESSION[self::ERRORS_KEY]))
            return $_SESSION[self::ERRORS_KEY];

        return null;
    }

    public function getFlash()
    {
        if (isset($_SESSION[self::FLASH_KEY]))
            return $_SESSION[self::FLASH_KEY];

        return null;
    }
    
    public function getFlashType()
    {
        if (isset($_SESSION[self::FLASH_TYPE_KEY]))
            return $_SESSION[self::FLASH_TYPE_KEY];

        return null;
    }
    
    public function getFlashDuration()
    {
        if (isset($_SESSION[self::FLASH_DURATION_KEY]))
            return $_SESSION[self::FLASH_DURATION_KEY];

        return null;
    }

    public function hasErrors()
    {
        return isset($_SESSION[self::ERRORS_KEY]);
    }

    public function hasFlash()
    {
        return isset($_SESSION[self::FLASH_KEY]);
    }

    public function clearErrors()
    {
        $this->remove(self::ERRORS_KEY);
    }

    public function clearFlash()
    {
        $this->remove(self::FLASH_KEY);
        $this->remove(self::FLASH_TYPE_KEY);
        $this->remove(self::FLASH_DURATION_KEY);
    }

    protected function shouldClearErrors()
    {
        if (!isset($_SESSION[self::SHOULD_CLEAR_ERRORS_KEY]))
            return false;

        return $_SESSION[self::SHOULD_CLEAR_ERRORS_KEY];
    }

    protected function shouldClearFlash()
    {
        if (!isset($_SESSION[self::SHOULD_CLEAR_FLASH_KEY]))
            return false;

        return $_SESSION[self::SHOULD_CLEAR_FLASH_KEY];
    }
}
