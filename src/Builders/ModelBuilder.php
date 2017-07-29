<?php

namespace JimHlad\LeapFrog\Builders;

class ModelBuilder
{
    /**
     * The template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the Model for the given options
     *
     * @param  array $options
     * @return string
     */
    public function create(array $options)
    {
        $modelTemplate = $this->insert($options['namespace'])->into($this->getCreateModelWrapper(), 'namespace');
        $modelTemplate = $this->insert($options['class'])->into($modelTemplate, 'class');
        $modelTemplate = $this->insert($options['table'])->into($modelTemplate, 'table');
        $modelTemplate = $this->insert($options['fillable'])->into($modelTemplate, 'fillable');
        $modelTemplate = $this->insert($options['hidden'])->into($modelTemplate, 'hidden');

        return $modelTemplate;
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
     * Return the wrapper for creating a Model
     *
     * @return string
     */
    private function getCreateModelWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Model.stub');
    }
}