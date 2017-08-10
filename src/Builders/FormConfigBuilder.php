<?php

namespace JimHlad\LeapFrog\Builders;

class FormConfigBuilder
{
    /**
     * The template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the FormConfig for the given options
     *
     * @param  array $options
     * @return string
     */
    public function create(array $options)
    {
        $configTemplate = $this->insert($options['entity'])->into($this->getFormConfigWrapper(), 'entity');

        foreach ($options['fields'] as $field) {
            $formField = '';

            if ($field['type'] === 'string') {
                // If we have a comma-separated string in the custom attributes, let's generate a select menu
                if (strlen($field['custom']) > 0 && strpos($field['custom'], ',') !== false) {
                    $formField = $this->insert($field['name'])->into($this->getFormSelectFieldPartialWrapper(), 'fieldName'); 
                    $customOptions = explode(',', $field['custom']);
                    foreach ($customOptions as $option) {
                        $optionName = trim($option);
                        $optionValue = snake_case($optionName);

                        $optionField = $this->insert($optionName)->into($this->getSelectOptionPartialWrapper(), 'optionName');
                        $optionField = $this->insert($optionValue)->into($optionField, 'optionValue');

                        $formField = $this->insert($optionField)->into($formField, 'OptionField');
                    }
                }
                else {
                    $formField = $this->insert($field['name'])->into($this->getFormStringFieldPartialWrapper(), 'fieldName');
                }
            }
            if ($field['type'] === 'text') {
                $formField = $this->insert($field['name'])->into($this->getFormTextFieldPartialWrapper(), 'fieldName');
            }
            if ($field['type'] === 'integer') {
                $formField = $this->insert($field['name'])->into($this->getFormIntegerFieldPartialWrapper(), 'fieldName');
            }
            if ($field['type'] === 'float') {
                $formField = $this->insert($field['name'])->into($this->getFormFloatFieldPartialWrapper(), 'fieldName');
            }
            if ($field['type'] === 'date') {
                $formField = $this->insert($field['name'])->into($this->getFormDateFieldPartialWrapper(), 'fieldName');
            }
            if ($field['type'] === 'dateTime') {
                $formField = $this->insert($field['name'])->into($this->getFormDateTimeFieldPartialWrapper(), 'fieldName');
            }
            if ($field['type'] === 'boolean') {
                $formField = $this->insert($field['name'])->into($this->getFormBooleanFieldPartialWrapper(), 'fieldName');
            }

            $formField = $this->insert(ucwords(str_replace('_', ' ', $field['name'])))->into($formField, 'altName');
            
            $configTemplate = $this->insert($formField)->into($configTemplate, 'FormField');
        }

        foreach ($options['relations'] as $relation) {
            $formField = '';

            if ($relation['type'] === 'belongsto') {
                $formField = $this->insert($relation['name'])->into($this->getFormRelationshipFieldPartialWrapper(), 'relationName');
                $formField = $this->insert(ucwords(str_replace('_', ' ', snake_case($relation['name']))))->into($formField, 'altName');
                $formField = $this->insert($relation['model_name'])->into($formField, 'modelName');
                $formField = $this->insert($relation['model_path'])->into($formField, 'modelPath');

                $configTemplate = $this->insert($formField)->into($configTemplate, 'FormField');
            }
        }

        // Cleanup any extraneous placeholder tags
        $configTemplate = str_replace('{{FormField}}', '', $configTemplate);
        $configTemplate = str_replace('{{OptionField}}', '', $configTemplate);

        return $configTemplate;
    }

    /**
     * Store the given template, to be inserted somewhere.
     *
     * @param  string $template
     * @return $this
     */
    private function insert(string $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get the stored template, and insert into the given wrapper.
     *
     * @param  string $wrapper
     * @param  string $placeholder
     * @return string
     */
    private function into(string $wrapper, string $placeholder)
    {
        return str_replace('{{' . $placeholder . '}}', $this->template, $wrapper);
    }

    /**
     * Return the wrapper for creating a FormConfig
     *
     * @return string
     */
    private function getFormConfigWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/FormConfig.stub');
    }

    /**
     * Return the wrapper for a string field
     *
     * @return string
     */
    private function getFormStringFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormStringField.stub');
    }

    /**
     * Return the wrapper for a text field
     *
     * @return string
     */
    private function getFormTextFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormTextField.stub');
    }

    /**
     * Return the wrapper for an integer field
     *
     * @return string
     */
    private function getFormIntegerFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormIntegerField.stub');
    }

    /**
     * Return the wrapper for a float field
     *
     * @return string
     */
    private function getFormFloatFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormFloatField.stub');
    }

    /**
     * Return the wrapper for a date field
     *
     * @return string
     */
    private function getFormDateFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormDateField.stub');
    }

    /**
     * Return the wrapper for a dateTime field
     *
     * @return string
     */
    private function getFormDateTimeFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormDateTimeField.stub');
    }

    /**
     * Return the wrapper for a boolean field
     *
     * @return string
     */
    private function getFormBooleanFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormBooleanField.stub');
    }

    /**
     * Return the wrapper for a select field
     *
     * @return string
     */
    private function getFormSelectFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormSelectField.stub');
    }

    /**
     * Return the wrapper for a relationship field
     *
     * @return string
     */
    private function getFormRelationshipFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormRelationshipField.stub');
    }

    /**
     * Return the wrapper for a select option field
     *
     * @return string
     */
    private function getSelectOptionPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/FormSelectOptionField.stub');
    }
}