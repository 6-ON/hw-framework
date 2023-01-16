<?php
namespace app\core\Form;

use app\core\Model;
class Form
{
    public static function begin(string $action, $method, bool $uploadFile =false)
    {
        $enctype = $uploadFile? 'enctype="multipart/form-data"':'';
        echo sprintf('<form action="%s" method="%s" '. $enctype .'>', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $attr)
    {
        return new Field($model, $attr);
    }
    public function select(Model $model, $attr,$sourceModel,$columnToShow,$FK='')
    {
        return new Select($model, $attr,$sourceModel,$columnToShow, $FK);
    }


}