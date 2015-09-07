<?php

namespace Library\Sentry;

use Library\Database\DataMapper\DataMapper;

class Sentry
{
    protected $user;

    public function __construct(DataMapper $dm)
    {
        $user = null;

        if ($this->loggedIn())
        {
            $userClass = '';
            switch ($_SESSION['type'])
            {
                case 'Teacher':
                    $userClass = 'Teacher';
                    break;
            }

            $class = 'App\\Domain\\Users\\'.$userClass;
            $this->user = $dm->findOrFail($class, $_SESSION['id']);
            if (is_null($this->user))
            {
                $this->logout();
                echo 'FATAL ERROR: Could not authenticate user. Please try reloading the page.';
                exit();
            }
        }
    }

    public function attempt($class, array $conditions)
    {
        if (sizeof($conditions) == 0)
        {
            return false;
        }

        $query = null;
        $i = 0;
        foreach ($conditions as $key => $value)
        {
            if ($i == 0)
            {
                $query = $class::where($key, '=', $value);
                if ($query == null)
                {
                    return false;
                }
                $i++;
                continue;
            }

            $query->where($key, '=', $value);
            $i++;
        }

        $result = $query->get();
        if (!is_null($result) && $result->count() > 0)
        {
            $this->user = $result->first();
            $this->login($this->user->id, $this->user->modelName());
            return $this->user;
        }

        return false;
    }

    public function login($id, $type)
    {
        $type = ucfirst($type);
        $modelName = '\\Models\\'.$type;
        $this->user = $modelName::find($id);

        if (is_null($this->user))
        {
            return;
        }

        $_SESSION['id'] = $id;
        $_SESSION['type'] = $type;
        $_SESSION['authenticated'] = true;
    }

    public function loggedIn()
    {
        return isset($_SESSION['id']) &&
        isset($_SESSION['authenticated']) &&
        isset($_SESSION['type']) &&
        $_SESSION['authenticated'] === true;
    }

    public function logout()
    {
        session_destroy();
    }

    public function user()
    {
        return $this->user;
    }

    public function type()
    {
        return isset($_SESSION['type']) ? $_SESSION['type'] : '';
    }

    public function is($type)
    {
        return isset($_SESSION['type']) && $_SESSION['type'] == $type;
    }

    public function id()
    {
        return $this->loggedIn() ? $this->user->id : 0;
    }
}