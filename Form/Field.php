<?php

namespace app\core\Form;

use app\core\Model;

class Field
{
    public const TYPE_TEXT = 'text';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';
    public const TYPE_FILE = 'file';

    public $decimal = '';

    public Model $model;
    public string $attr;
    public string $type;

    /**
     * @param Model $model
     * @param string $attr
     */
    public function __construct(Model $model, string $attr)
    {
        $this->model = $model;
        $this->attr = $attr;
        $this->type = self::TYPE_TEXT;
    }

    public function passwordField(): Field
    {
        $this->type = self::TYPE_PASSWORD;
        return $this;
    }

    public function emailField(): Field
    {
        $this->type = self::TYPE_EMAIL;
        return $this;
    }
    public function numberField(): Field
    {
        $this->type = self::TYPE_NUMBER;
        return $this;
    }
    public function isDecimal() : Field
    {
        $this->decimal ='step="0.01"';
        return $this;
    }
    public function fileField(): Field
    {
        $this->type = self::TYPE_FILE;
        return $this;
    }


    public function __toString()
    {
        return sprintf('
           <div class="mb-3">
              <label class="form-label">%s</label>
               <input type="%s" name="%s" value="%s" class="form-control %s" '. $this->decimal .'>
               <div class="invalid-feedback">%s</div>
           </div>
           ', $this->model->getLabel($this->attr),
            $this->type,
            $this->attr,
            $this->model->{$this->attr},
            $this->model->hasError($this->attr) ? 'is-invalid' : '',
            $this->model->getError($this->attr)
        );
    }

}