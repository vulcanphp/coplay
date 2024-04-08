<?php

namespace VulcanPhp\InputMaster\Input;

use VulcanPhp\InputMaster\Exceptions\InvalidArgumentException;

class InputFile implements IInputItem
{
    public $index, $name, $filename, $size, $type, $errors, $tmpName, $info;

    public function __construct(string $index)
    {
        $this->index = $index;
        $this->errors = 0;
        // Make the name human friendly, by replace _ with space
        $this->name = ucfirst(str_replace('_', ' ', strtolower($this->index)));
    }

    public static function createFromArray(array $values): self
    {
        if (isset($values['index']) === false) {
            throw new InvalidArgumentException('Index key is required');
        }
        /* Easy way of ensuring that all indexes-are set and not filling the screen with isset() */
        $values += [
            'tmp_name' => null,
            'type'     => null,
            'size'     => null,
            'name'     => null,
            'error'    => null,
        ];
        return (new InputFile($values['index']))
            ->setSize((int) $values['size'])
            ->setError((int) $values['error'])
            ->setType($values['type'])
            ->setTmpName($values['tmp_name'])
            ->setFilename($values['name'])
            ->setInfo($values['tmp_name']);
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function setIndex(string $index): IInputItem
    {
        $this->index = $index;
        return $this;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function setSize(int $size): InputFile
    {
        $this->size = $size;
        return $this;
    }

    public function getMime(): string
    {
        return $this->getType();
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): InputFile
    {
        $this->type = $type;
        return $this;
    }

    public function getExtension(): string
    {
        return pathinfo($this->getFilename(), PATHINFO_EXTENSION);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): IInputItem
    {
        $this->name = $name;
        return $this;
    }

    public function setFilename(string $name): InputFile
    {
        $this->filename = $name;
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function move(string $destination): bool
    {
        return !file_exists($destination) ? (bool) move_uploaded_file($this->tmpName, $destination) : false;
    }

    public function getContents(): string
    {
        return file_get_contents($this->tmpName);
    }

    public function getInfo(?string $key = null)
    {
        return $key !== null ? ($this->info[$key] ?? null) : $this->info;
    }

    public function setInfo(string $temp_name): InputFile
    {
        $this->info = pathinfo($temp_name);
        return $this;
    }

    public function hasError(): bool
    {
        return ($this->getError() !== 0);
    }

    public function getError(): ?int
    {
        return $this->errors;
    }

    public function setError(?int $error): InputFile
    {
        $this->errors = (int) $error;
        return $this;
    }

    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    public function setTmpName(string $name): InputFile
    {
        $this->tmpName = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTmpName();
    }

    public function getValue(): string
    {
        return $this->getFilename();
    }

    public function setValue($value): IInputItem
    {
        $this->filename = $value;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'tmp_name' => $this->tmpName,
            'type'     => $this->type,
            'size'     => $this->size,
            'name'     => $this->name,
            'error'    => $this->errors,
            'filename' => $this->filename,
        ];
    }
}
