<?php

namespace Withfile;

/**
 * Working with file for livewire
 */
trait WithFile
{
    public function initializeWithFile()
    {
        $this->listeners[] = 'processingFileUpload';
    }

    public function processingFileUpload($attribute, $file)
    {
        if (isset($this->fileable) && in_array($attribute, $this->fileable)) {
            $old_value = $this->{$attribute};
            $value = [
                "name" => $file['name'],
                "type" => $file['type'],
                "extension" => $file['extension'],
                "content" => $file['content'],
            ];
            $this->{$attribute} = $value;

            if (method_exists($this, 'updated' . ucfirst($attribute))) {
                $this->{'updated' . ucfirst($attribute)}($value, $old_value);
            }
            $this->updated($attribute, $value);
        }
    }

    public function raw($field)
    {
        if (isset($field['content']))
            return base64_decode(last(explode(',', $field['content'])));
        return null;
    }
}
