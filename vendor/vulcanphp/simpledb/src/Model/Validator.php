<?php

namespace VulcanPhp\SimpleDb\Model;

trait Validator
{
    protected array $errors = [], $rules = [];

    public function setRules(array $rules): self
    {
        $this->rules = $rules;
        return $this;
    }

    public function getRules($attribute)
    {
        return $this->rules()[$attribute] ?? null;
    }

    public function rules(): array
    {
        return (array) $this->rules ?? [];
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute} ?? '';
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) $ruleName = $rule[0];

                if ($ruleName === 'required' && empty($value)) {
                    $this->addErrorForRule($attribute, 'required');
                } elseif ($ruleName === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, 'email');
                } elseif ($ruleName === 'min' && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attribute, 'min', $rule);
                } elseif ($ruleName === 'max' && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attribute, 'max', $rule);
                } elseif ($ruleName === 'match' && $value != $this->{$rule['match']}) {
                    $this->addErrorForRule($attribute, 'match', $rule);
                } elseif ($ruleName === 'unique' && !$this->hasError($attribute)) {
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $primaryKey = $rule['class']::primaryKey();
                    $condition  = isset($this->{$primaryKey}) ? " AND $primaryKey != :primaryValue" : '';
                    $statement  = prepare("SELECT $primaryKey FROM {$rule['class']::tableName()} WHERE $uniqueAttr = :attr $condition");
                    $statement->bindValue(":attr", $value);
                    if (isset($this->{$primaryKey})) {
                        $statement->bindValue(":primaryValue", $this->{$primaryKey});
                    }
                    if ($statement->execute() && $statement->fetchObject() !== false) {
                        $this->addErrorForRule($attribute, 'unique', ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }

        return empty($this->errors);
    }

    protected function addErrorForRule(string $attribute, string $rule, array $params = []): self
    {
        $messages = $this->errorMessage()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $messages = str_ireplace('{' . $key . '}', ucwords(str_replace(['_', '-'], ' ', $value)), $messages);
        }
        $this->errors[$attribute][] = $messages;

        return $this;
    }

    public function addError(string $attribute, string $message): self
    {
        $this->errors[$attribute][] = $message;

        return $this;
    }

    public function errorMessage(): array
    {
        return [
            'required' => 'This field is required',
            'email'    => 'This field must be a valid email address',
            'min'      => 'Min length of this field must be {min}',
            'max'      => 'Max length of this field must be {max}',
            'match'    => 'This field must be the same with {match}',
            'unique'   => 'Record with this {field} already exists',
        ];
    }

    public function hasError($attribute): bool
    {
        return array_key_exists($attribute, $this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function firstError($attribute = null): string
    {
        if ($attribute === null) {
            $attribute = $this->errorField();
        }

        return $this->errors[$attribute][0] ?? '';
    }

    public function errorField(): ?string
    {
        return array_keys($this->errors)[0] ?? null;
    }
}
