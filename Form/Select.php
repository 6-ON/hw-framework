<?php

namespace app\core\Form;

use app\core\Model;

class Select
{

    public const SELECTED = 'selected';
    public Model $model;


    public array $data;


    public string $attr;
    public string $dataModel;
    public array $options;


    public function __construct(Model $model, string $attr, string $dataModel,string $columnToShow,$FK='')
    {
        $this->model = $model;
        $this->attr = $attr;
        $this->data = $dataModel::getAll();
        $this->dataModel =$dataModel;
        $this->loadOptions($columnToShow,$FK);

    }


    public function loadOptions($columnToShow,$FK='')
    {

        foreach ($this->data as $row) {
            $rowPK = $row->{$this->dataModel::primaryKey()};
            $rowLabel = $row->{$columnToShow};
            $optFormat = (property_exists($this->model,$FK) && $rowPK === $this->model->{$FK})?
            '<option value="%u" selected >%s</option>':
            '<option value="%u">%s</option>';
            
            $opt =sprintf($optFormat,$rowPK,$rowLabel);
            $this->options[] = $opt;
        }
    }

    function __toString()
    {
        return sprintf(
            '<select class="form-select col" name="%s">
            ' . implode(PHP_EOL,$this->options) . '
            </select>',
            $this->attr

        );
    }
}
