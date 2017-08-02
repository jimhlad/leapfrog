<?php

namespace JimHlad\LeapFrog\Builders;

class CreateViewBuilder
{
    /**
     * The template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the CreateView for the given options
     *
     * @param  array $options
     * @return string
     */
    public function create(array $options)
    {
        $viewTemplate = $this->insert($options['entity'])->into($this->getCreateViewWrapper(), 'entity');
        $viewTemplate = $this->insert($options['entityPlural'])->into($viewTemplate, 'entityPlural');
        $viewTemplate = $this->insert($options['entityCamel'])->into($viewTemplate, 'entityCamel');
        $viewTemplate = $this->insert($options['entityCamelPlural'])->into($viewTemplate, 'entityCamelPlural');
        $viewTemplate = $this->insert($options['entitySnake'])->into($viewTemplate, 'entitySnake');
        $viewTemplate = $this->insert($options['entitySnakePlural'])->into($viewTemplate, 'entitySnakePlural');

        return $viewTemplate;
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
     * Return the wrapper for creating a CreateView
     *
     * @return string
     */
    private function getCreateViewWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/CreateView.stub');
    }
}