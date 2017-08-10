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

        foreach ($options['relations'] as $relation) {
            $relationMethod = '';

            if ($relation['type'] === 'belongsto') {
                $relationMethod = $this->insert($options['entity'])->into($this->getModelBelongsToPartialWrapper(), 'entity');
            }
            if ($relation['type'] === 'hasone') {
                $relationMethod = $this->insert($options['entity'])->into($this->getModelHasOnePartialWrapper(), 'entity');
            }
            if ($relation['type'] === 'hasmany') {
                $relationMethod = $this->insert($options['entity'])->into($this->getModelHasManyPartialWrapper(), 'entity');
            }
            if ($relation['type'] === 'belongstomany') {
                $relationMethod = $this->insert($options['entity'])->into($this->getModelBelongsToManyPartialWrapper(), 'entity');
            }
            
            $useStatement = $this->insert($relation['model_path'])->into($this->getModelUseStatementPartialWrapper(), 'modelPath');
            $useStatement = $this->insert($relation['model_name'])->into($useStatement, 'modelName');

            $relationMethod = $this->insert($relation['name'])->into($relationMethod, 'relationName');
            $relationMethod = $this->insert($relation['model_name'])->into($relationMethod, 'modelName');

            $modelTemplate = $this->insert($useStatement)->into($modelTemplate, 'UseStatement');
            $modelTemplate = $this->insert($relationMethod)->into($modelTemplate, 'RelationMethod');
        }

        // Cleanup any extraneous placeholder tags
        $modelTemplate = str_replace('{{UseStatement}}', '', $modelTemplate);
        $modelTemplate = str_replace('{{RelationMethod}}', '', $modelTemplate);
        $modelTemplate = preg_replace('/(\s*\n){3,}/', "\n\n", $modelTemplate);

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

    /**
     * Return the wrapper for a belongsTo relation
     *
     * @return string
     */
    private function getModelBelongsToPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ModelBelongsTo.stub');
    }

    /**
     * Return the wrapper for a hasOne relation
     *
     * @return string
     */
    private function getModelHasOnePartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ModelHasOne.stub');
    }

    /**
     * Return the wrapper for a hasMany relation
     *
     * @return string
     */
    private function getModelHasManyPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ModelHasMany.stub');
    }

    /**
     * Return the wrapper for a belongsToMany relation
     *
     * @return string
     */
    private function getModelBelongsToManyPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ModelBelongsToMany.stub');
    }

    /**
     * Return the wrapper for a belongsToMany relation
     *
     * @return string
     */
    private function getModelUseStatementPartialWrapper()
    {
        return file_get_contents(__DIR__ . '/../Stubs/Partials/ModelUseStatement.stub');
    }
}