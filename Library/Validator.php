<?php namespace Library;

class Validator
{
    public function make(array $data, array $rules, $withErrors = true)
    {
        $errors = array();
        $isValid = true;

        foreach ($rules as $field => $constraints)
        {
            $customError = null;
            if (is_array($constraints))
            {
                $customError = $constraints[1];
                $constraints= $constraints[0];
            }
            $value = $data[$field];
            $constraints = explode('|', $constraints);
            foreach ($constraints as $constraint)
            {
                $result = false;
                $functionName = '';
                $args = array();
                $temp = explode(':', $constraint);
                if (sizeof($temp) == 1)
                    $functionName = $constraint;
                else
                {
                    $functionName = $temp[0];
                    $args = explode(',', $temp[1]);
                }

                switch (sizeof($args))
                {
                    case 0:
                        $result = $this->$functionName($value);
                        break;
                    case 1:
                        $result = $this->$functionName($value, $args[0]);
                        break;
                    case 2:
                        $result = $this->$functionName($value, $args[0], $args[1]);
                        break;
                    case 3:
                        $result = $this->$functionName($value, $args[0], $args[1], $args[2]);
                        break;
                    case 4:
                        $result = $this->$functionName($value, $args[0], $args[1], $args[2], $args[3]);
                        break;
                    case 5:
                        $result = $this->$functionName($value, $args[0], $args[1], $args[2], $args[3], $args[4]);
                        break;
                }

                if ($result)
                    continue;

                if ($isValid)
                    $isValid = false;

                if ($withErrors)
                    $errors[] = $this->buildErrorString($field, $functionName, $args, $customError);
            }
        }

        return $isValid;
    }

    public function required($value)
    {
        if ($value == null || trim($value) === '')
            return false;

        return true;
    }

    public function numeric($value)
    {
        return is_numeric($value);
    }

    public function min($value, $min)
    {
        // ...
    }

    private function buildErrorString($field, $functionName, array $args, $customError)
    {
        if ($customError != null)
            return $this->buildCustomErrorString($field, $args, $customError);

        switch ($functionName)
        {
            case 'required':
                return $field.' is required';
            case 'numeric':
                return $field.' must be numeric';
        }
    }

    /**
     * Example: '{field} is required'
     * Example: '{field} should be bigger than {0}'
     * Example: '{field} should be between {0} and {1}'
     */
    private function buildCustomErrorString($field, array $args, $customError)
    {
        $result = str_replace('{field}', $field, $customError);

        if (sizeof($args) == 0)
            return $result;

        for ($i = 0; $i < sizeof($args); $i++)
            $result = str_replace('{'.$i.'}', $args[$i], $result);

        return $result;
    }
}