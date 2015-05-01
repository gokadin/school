<?php namespace Library;

if(Config::get('testing') != 'true')
{
    session_start();
}

class Session
{
    const ERRORS_KEY = 'errors';
    const SHOULD_CLEAR_ERRORS_KEY = 'shouldClearErrors';
    const FLASH_KEY = 'flash';
    const SHOULD_CLEAR_FLASH_KEY = 'shouldClearFlash';
    const CURRENT_CSRF_KEY = 'currentCsrfToken';
    const NEXT_CSRF_KEY = 'nextCsrfToken';
    const SHOULD_CLEAR_CSRF_TOKEN_KEY = 'shouldClearCsrfToken';

    public function __construct()
    {
        $this->rotateCsrf();
        
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

    public function login($id)
    {
        $_SESSION['id'] = $id;
        $_SESSION['authenticated'] = true;
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

    public function setFlash($string)
    {
        $_SESSION[self::FLASH_KEY] = $string;
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
    
    private function shouldClearCsrfToken()
    {
        if (!isset($_SESSION[self::SHOULD_CLEAR_CSRF_TOKEN]))
            return false;

        return $_SESSION[self::SHOULD_CLEAR_CSRF_TOKEN];
    }
    
    private function rotateCsrf()
    {
        if (isset($_SESSION[self::NEXT_CSRF_KEY]))
            $_SESSION[self::CURRENT_CSRF_KEY] = $_SESSION[self::NEXT_CSRF_KEY];
            
        $_SESSION[self::NEXT_CSRF_KEY] = md5(uniqid(rand(), true));
    }
    
    public function currentCsrfToken()
    {
        if (isset($_SESSION[self::CURRENT_CSRF_KEY]))
            return $_SESSION[self::CURRENT_CSRF_KEY];
    }
    
    public function nextCsrfToken()
    {
        if (isset($_SESSION[self::NEXT_CSRF_KEY]))
            return $_SESSION[self::NEXT_CSRF_KEY];
    }
}
