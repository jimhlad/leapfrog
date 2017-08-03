<?php

namespace JimHlad\LeapFrog\Builders;

class ControllerBuilder
{
    /**
     * The template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the Controller for the given options
     *
     * @param  array $options
     * @return string
     */
    public function create(array $options)
    {
        $controllerTemplate = $this->insert($options['namespace'])->into($this->getCreateControllerWrapper(), 'namespace');
        $controllerTemplate = $this->insert($options['entity'])->into($controllerTemplate, 'entity');
        $controllerTemplate = $this->insert($options['entityCamel'])->into($controllerTemplate, 'entityCamel');
        $controllerTemplate = $this->insert($options['entityCamelPlural'])->into($controllerTemplate, 'entityCamelPlural');
        $controllerTemplate = $this->insert($options['entitySnake'])->into($controllerTemplate, 'entitySnake');
        $controllerTemplate = $this->insert($options['entitySnakePlural'])->into($controllerTemplate, 'entitySnakePlural');
        $controllerTemplate = $this->insert($options['entitySlugPlural'])->into($controllerTemplate, 'entitySlugPlural');
        $controllerTemplate = $this->insert($options['createRequest'])->into($controllerTemplate, 'createRequest');
        $controllerTemplate = $this->insert($options['updateRequest'])->into($controllerTemplate, 'updateRequest');
        $controllerTemplate = $this->insert($options['requestsNamespace'])->into($controllerTemplate, 'requestsNamespace');
        $controllerTemplate = $this->insert($options['servicesNamespace'])->into($controllerTemplate, 'servicesNamespace');
        $controllerTemplate = $this->insert($options['useStatements'])->into($controllerTemplate, 'useStatements');

        return $controllerTemplate;
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
     * Return the wrapper for creating a Controller
     *
     * @return string
     */
    private function getCreateControllerWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Controller.stub');
    }
}