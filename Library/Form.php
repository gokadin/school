<?php namespace Library;

use Library\Facades\Session;

class Form
{
    public function open($action, $method = 'POST', array $options = null, $includeToken = true)
    {
        $needHiddenMethod = false;
        $str = '<form action="';
        if (!empty($action))
            $str .= \Library\Facades\Router::actionToPath($action);
        $str .= '"';

        if ($method == 'GET' || $method == 'POST')
            $str .= ' method="'.$method.'"';
        else
        {
            $needHiddenMethod = true;
            $str .= ' method="POST"';
        }

        $str .= $this->buildOptionsAndId($options);

        $str .= '>';

        if ($needHiddenMethod)
            $str .= '<input type="hidden" name="_method" value="'.$method.'" />';

        if ($includeToken)
            $str .= '<input type="hidden" name="_token" value="'. Session::generateToken() .'" />';

        return $str;
    }

    public function close()
    {
        return '</form>';
    }

    public function label($for, $name, array $options = null)
    {
        $str = '<label for="'.$for.'"';

        $str .= $this->buildOptionsAndId($options, null);

        $str .= '>'.$name.'</label>';

        return $str;
    }

    public function hidden($name, $value, array $options = null)
    {
        $str = '<input type="hidden" name="'.$name.'" value="'.$value.'"';

        $str .= $this->buildOptionsAndId($options, $name);

        $str .= ' />';

        return $str;
    }

    public function text($name, $default = null, array $options = null)
    {
        $str = '<input type="text" name="'.$name.'"';

        if ($default != null)
            $str .= ' value="'.$default.'"';

        $str .= $this->buildOptionsAndId($options, $name, true);

        $str .= ' />';

        return $str;
    }

    public function error($name, $options = null)
    {
        $str = '<div';

        $str .= $this->buildOptionsAndId($options);

        $str .= '>';

        if (\Library\Facades\Session::hasErrors()
            && isset(\Library\Facades\Session::getErrors()[$name])
            && sizeof(\Library\Facades\Session::getErrors()[$name]) > 0)
        {
            $str .= \Library\Facades\Session::getErrors()[$name][0];
        }

        return $str .= '</div>';
    }

    public function submit($name, $options = null)
    {
        $str = '<input type="submit" value="'.$name.'"';

        $str .= $this->buildOptionsAndId($options, $name);

        $str .= ' />';

        return $str;
    }

    public function button($text, $options = null)
    {
        $str = '<button';

        $str .= $this->buildOptionsAndId($options);

        $str .= '>'.$text.'</button>';

        return $str;
    }

    private function buildOptionsAndId($options, $name = null, $presentError = false)
    {
        if ($options == null)
            return $name == null ? '' : ' id="'.$name.'"';

        $str = '';
        $idIsSet = false;
        $classIsSet = false;
        foreach ($options as $key => $value)
        {
            if ($key == 'id')
                $idIsSet = true;

            if ($key == 'class')
            {
                if ($presentError
                    && $name != null
                    && \Library\Facades\Session::hasErrors()
                    && isset(\Library\Facades\Session::getErrors()[$name])
                    && sizeof(\Library\Facades\Session::getErrors()[$name]) > 0)
                {
                    $value .= ' invalid';
                }

                $classIsSet = true;
            }

            $str .= ' '.$key.'="'.$value.'"';
        }

        if (!$idIsSet && $name != null)
            $str .= ' id="'.$name.'"';

        if (!$classIsSet)
        {
            if ($presentError
                && $name != null
                && \Library\Facades\Session::hasErrors()
                && isset(\Library\Facades\Session::getErrors()[$name])
                && sizeof(\Library\Facades\Session::getErrors()[$name]) > 0)
            {
                $str .= ' class="invalid"';
            }
        }

        return $str;
    }
}