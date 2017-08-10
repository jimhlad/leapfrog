<?php

namespace JimHlad\LeapFrog\Builders;

class ServiceBuilder
{
    /**
     * The template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the Service for the given options
     *
     * @param  array $options
     * @return string
     */
    public function create(array $options)
    {
        $serviceTemplate = $this->insert($options['namespace'])->into($this->getCreateServiceWrapper(), 'namespace');
        $serviceTemplate = $this->insert($options['entity'])->into($serviceTemplate, 'entity');
        $serviceTemplate = $this->insert($options['modelsNamespace'])->into($serviceTemplate, 'modelsNamespace');

        $createMethodTemplate = $this->getServiceCreateMethodPartialWrapper();
        $createMethodTemplate = $this->insert($options['entity'])->into($createMethodTemplate, 'entity');
        $createMethodTemplate = $this->insert($options['modelsNamespace'])->into($createMethodTemplate, 'modelsNamespace');

        $updateMethodTemplate = $this->getServiceUpdateMethodPartialWrapper();
        $updateMethodTemplate = $this->insert($options['entity'])->into($updateMethodTemplate, 'entity');
        $updateMethodTemplate = $this->insert($options['modelsNamespace'])->into($updateMethodTemplate, 'modelsNamespace');

        $needCustomMethods = false;

        foreach ($options['fields'] as $field) {
            $booleanTemplate = '';
            if ($field['type'] === 'boolean') {
                $needCustomMethods = true;

                $booleanTemplate = $this->getServiceSyncBooleanPartialWrapper();
                $booleanTemplate = $this->insert($field['name'])->into($booleanTemplate, 'fieldName');

                $createMethodTemplate = $this->insert($booleanTemplate)->into($createMethodTemplate, 'ServiceSyncBooleanField');
                $updateMethodTemplate = $this->insert($booleanTemplate)->into($updateMethodTemplate, 'ServiceSyncBooleanField');
            }
        }

        foreach ($options['relations'] as $relation) {
            $belongsTemplate = '';
            if ($relation['type'] === 'belongsto') {
                $needCustomMethods = true;

                $relationNameSnakeCase = snake_case($relation['name']);
                $belongsTemplate = $this->getServiceSyncBelongsToPartialWrapper();
                $belongsTemplate = $this->insert($relationNameSnakeCase)->into($belongsTemplate, 'relationNameSnakeCase');

                $createMethodTemplate = $this->insert($belongsTemplate)->into($createMethodTemplate, 'ServiceSyncBelongsToField');
                $updateMethodTemplate = $this->insert($belongsTemplate)->into($updateMethodTemplate, 'ServiceSyncBelongsToField');
            }
        }

        if ($needCustomMethods === true) {
            $serviceTemplate = $this->insert($createMethodTemplate)->into($serviceTemplate, 'ServiceCreateMethod');
            $serviceTemplate = $this->insert($updateMethodTemplate)->into($serviceTemplate, 'ServiceUpdateMethod');
        }

        // Cleanup any extraneous placeholder tags, whitepsace
        $serviceTemplate = str_replace('{{ServiceSyncBooleanField}}', '', $serviceTemplate);
        $serviceTemplate = str_replace('{{ServiceSyncBelongsToField}}', '', $serviceTemplate);
        $serviceTemplate = str_replace('{{ServiceCreateMethod}}', '', $serviceTemplate);
        $serviceTemplate = str_replace('{{ServiceUpdateMethod}}', '', $serviceTemplate);
        $serviceTemplate = preg_replace('/(\s*\n){3,}/', "\n\n", $serviceTemplate);

        return $serviceTemplate;
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
     * Return the wrapper for creating a Service
     *
     * @return string
     */
    private function getCreateServiceWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Service.stub');
    }

    /**
     * Return the wrapper for creating a service create method
     *
     * @return string
     */
    private function getServiceCreateMethodPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ServiceCreateMethod.stub');
    }

    /**
     * Return the wrapper for creating a service update method
     *
     * @return string
     */
    private function getServiceUpdateMethodPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ServiceUpdateMethod.stub');
    }

    /**
     * Return the wrapper for creating boolean sync logic
     *
     * @return string
     */
    private function getServiceSyncBooleanPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ServiceSyncBooleanField.stub');
    }

    /**
     * Return the wrapper for creating boolean sync logic
     *
     * @return string
     */
    private function getServiceSyncBelongsToPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ServiceSyncBelongsToField.stub');
    }
}