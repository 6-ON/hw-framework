<?php

namespace app\core;

abstract class Model
{
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'match';
    public const RULE_UNIQUE = 'unique';

    public array $errors = [];

    public function loadData($data)
    {
        foreach ($data as $item => $value) {
            if (property_exists($this, $item)) {
                $this->{$item} = $value;
            }
        }
    }
    public function changes(Model $other)
    {
        $changes = [];
        foreach ($this as $prop => $value) {
            if (property_exists($other, $prop) && $value && $value != $other->{$prop}) {
                $changes[$prop] = $value;
            }
        }
        return $changes;
    }
    public function validate(): bool
    {
        foreach ($this->rules() as $attr => $rules) {
            $value = $this->{$attr};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($rule)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName == self::RULE_REQUIRED && !$value) {
                    $this->addRuleError($attr, self::RULE_REQUIRED);
                }
                if ($ruleName == self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addRuleError($attr, self::RULE_EMAIL);
                }
                if ($ruleName == self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addRuleError($attr, self::RULE_MIN, $rule);
                }
                if ($ruleName == self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addRuleError($attr, self::RULE_MAX, $rule);
                }
                if ($ruleName == self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addRuleError($attr, self::RULE_MATCH, $rule);
                }
                if ($ruleName == self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $tableName = $className::tableName();
                    $stmt = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $attr  = :attr");
                    $stmt->bindValue(':attr', $value);
                    $stmt->execute();
                    $record = $stmt->fetchObject();
                    if ($record) {
                        $this->addRuleError($attr, self::RULE_UNIQUE, ['field' => $this->getLabel($attr)]);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    abstract public function rules(): array;

    abstract public function labels(): array;


    private function addRuleError(string $attr, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attr][] = $message;
    }

    public function addError(string $attr, string $message)
    {
        $this->errors[$attr][] = $message;
    }

    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => "this Field is Required",
            self::RULE_UNIQUE => "this {field} is already used",
            self::RULE_EMAIL => "this Field must be an email",
            self::RULE_MIN => "the min length of this must be {min}",
            self::RULE_MAX => "the max length of this must be {max}",
            self::RULE_MATCH => "this Field does not match {match}"
        ];
    }

    public function getLabel($attr)
    {
        return $this->labels()[$attr] ?? $attr;
    }

    public function hasError($attr)
    {
        return $this->errors[$attr] ?? false;
    }

    public function getError($attr)
    {
        return $this->errors[$attr][0] ?? false;
    }
}
