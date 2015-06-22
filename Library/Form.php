<?php namespace Library;

use Library\Facades\Session;

class Form
{
    public function open($name, $action, $method = 'POST', array $options = null, $includeToken = true)
    {
        $needHiddenMethod = false;
        $str = '<form id="'.$name.'" action="@path('.$action.')';

        if ($method == 'GET' || $method == 'POST')
            $str .= ' method="'.$method.'"';
        else
            $needHiddenMethod = true;

        if ($options != null)
            foreach ($options as $key => $value)
                $str .= ' '.$key.'="'.$value.'"';

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

        if ($options != null)
            foreach ($options as $key => $value)
                $str .= ' '.$key.'="'.$value.'"';

        $str .= '>'.$name.'</label>';

        return $str;
    }

    public function text($name, $default = null, array $options = null)
    {
        $str = '<input type="text" name="'.$name.'" id="'.$name.'"';

        if ($default != null)
            foreach ($options as $key => $value)
                $str .= ' '.$key.'="'.$value.'"';

        if ($options != null)
            foreach ($options as $key => $value)
                $str .= ' '.$key.'="'.$value.'"';

        $str .= ' />';

        return $str;
    }

    public function submit($name, $options = null)
    {
        $str = '<input type="submit" value="'.$name.'"';

        if ($options != null)
            foreach ($options as $key => $value)
                $str .= ' '.$key.'="'.$value.'"';

        $str .= ' />';

        return $str;
    }
}