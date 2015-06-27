<?php namespace Applications\Frontend\Modules\Ajax;

error_reporting(0);

use Library\BackController;
use Library\Facades\Request;

class AjaxController extends BackController
{
    public function exists()
    {
        $model = '\\Models\\'.Request::data('modelName');
        if (\Library\Config::get('frameworkTesting') == 'true')
            $model = '\\Tests\\FrameworkTest\\Models\\'.Request::data('modelName');

        echo !$model::exists(Request::data('columnName'), Request::data('value'));
    }
}