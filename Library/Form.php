<?php namespace Library;

class Form
{
    public function label($for, $name, array $options = null)
    {
        $str = '<label for="'.$for.'"';

        if ($options != null)
            foreach ($options as $key => $value)
                $str .= ' '.$key.'="'.$value.'"';

        $str .= '>'.$name.'</label>';

        return $str;
    }

    public function text($name, $default = null, $options = null)
    {
        $str = '<input type="text" name="'.$name.'" id="'.$name.'"';

        if ($default != null)
            $str .= ' value="'.$default.'"';

        if ($options != null)
            foreach ($options as $key => $value)
                $str .= ' '.$key.'="'.$value.'"';

        $str .= ' />';

        return $str;
    }
}