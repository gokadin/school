<?php

function configureEnvironment()
{
    $envFile = __DIR__.'/../../.env';

    $content = fopen($envFile, 'r');
    if ($content) {
        while (($line = fgets($content)) !== false) {
                if (preg_match('/[a-zA-Z0-9_-]+\=[a-zA-Z0-9_-]+[\n]?$/', $line) != 1)
            {
                continue;
            }

            $line = trim(str_replace(PHP_EOL, '', $line));

            putenv($line);
        }

        fclose($content);
    }
}

function env($name)
{
    $value = getenv($name);

    switch (strtolower($value))
    {
        case 'true':
            return true;
        case 'false':
            return false;
    }

    return $value;
}