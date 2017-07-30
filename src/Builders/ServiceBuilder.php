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
}