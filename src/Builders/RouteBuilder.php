<?php

namespace JimHlad\LeapFrog\Builders;

class RouteBuilder
{
    /**
     * The template to be inserted.
     *
     * @var string
     */
    private $template;

    /**
     * Create the Route for the given options
     *
     * @param  array $options
     * @return string
     */
    public function create(array $options)
    {
        $routeTemplate = $this->insert($options['entity'])->into($this->getCreateRouteWrapper(), 'entity');
        $routeTemplate = $this->insert($options['entitySlugPlural'])->into($routeTemplate, 'entitySlugPlural');
        $routeTemplate = $this->insert($options['controllersPath'])->into($routeTemplate, 'controllersPath');

        return $routeTemplate;
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
     * Return the wrapper for creating a Route
     *
     * @return string
     */
    private function getCreateRouteWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Route.stub');
    }
}