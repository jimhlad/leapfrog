<?php

namespace JimHlad\LeapFrog\Builders;

class CreateRequestBuilder
{
    /**
     * The template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the CreateRequest for the given options
     *
     * @param  array $options
     * @return string
     */
    public function create(array $options)
    {
        $requestTemplate = $this->insert($options['namespace'])->into($this->getCreateCreateRequestWrapper(), 'namespace');
        $requestTemplate = $this->insert($options['entity'])->into($requestTemplate, 'entity');
        $requestTemplate = $this->insert($options['rules'])->into($requestTemplate, 'rules');

        return $requestTemplate;
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
     * Return the wrapper for creating a CreateRequest
     *
     * @return string
     */
    private function getCreateCreateRequestWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/CreateRequest.stub');
    }
}