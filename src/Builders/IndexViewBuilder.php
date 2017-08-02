<?php

namespace JimHlad\LeapFrog\Builders;

class IndexViewBuilder
{
    /**
     * The template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the IndexView for the given options
     *
     * @param  array $options
     * @return string
     */
    public function create(array $options)
    {
        $viewTemplate = $this->insert($options['entity'])->into($this->getIndexViewWrapper(), 'entity');
        $viewTemplate = $this->insert($options['entityPlural'])->into($viewTemplate, 'entityPlural');
        $viewTemplate = $this->insert($options['entityCamel'])->into($viewTemplate, 'entityCamel');
        $viewTemplate = $this->insert($options['entityCamelPlural'])->into($viewTemplate, 'entityCamelPlural');
        $viewTemplate = $this->insert($options['entitySnake'])->into($viewTemplate, 'entitySnake');
        $viewTemplate = $this->insert($options['entitySnakePlural'])->into($viewTemplate, 'entitySnakePlural');

        foreach ($options['fieldNames'] as $field) {
            $tableHeaderField = $this->insert(ucfirst($field))->into($this->getTableHeaderFieldPartialWrapper(), 'field');
            $tableColumnField = $this->insert($field)->into($this->getTableColumnFieldPartialWrapper(), 'field');
            $tableColumnField = $this->insert($options['entityCamel'])->into($tableColumnField, 'entityCamel');

            $viewTemplate = $this->insert($tableHeaderField)->into($viewTemplate, 'TableHeaderField');
            $viewTemplate = $this->insert($tableColumnField)->into($viewTemplate, 'TableColumnField');
        }

        // Cleanup any extraneous placeholder tags
        $viewTemplate = str_replace('{{TableHeaderField}}', '', $viewTemplate);
        $viewTemplate = str_replace('{{TableColumnField}}', '', $viewTemplate);

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
     * Return the wrapper for creating a IndexView
     *
     * @return string
     */
    private function getIndexViewWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/IndexView.stub');
    }

    /**
     * Return the wrapper for a table header field
     *
     * @return string
     */
    private function getTableHeaderFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/TableHeaderField.stub');
    }

    /**
     * Return the wrapper for a table column field
     *
     * @return string
     */
    private function getTableColumnFieldPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/TableColumnField.stub');
    }
}