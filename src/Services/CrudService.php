<?php

namespace JimHlad\LeapFrog\Services;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use JimHlad\LeapFrog\Builders\ModelBuilder;

class CrudService
{

	/**
	 * Current progress as an array
	 *
	 * @var array
	 */
	protected $progress;

	/**
     * The Filesystem class
     *
     * @var Filesystem
     */
    protected $fileSystem;

	/**
	 * Our ModelBuilder class
	 *
	 * @var ModelBuilder
	 */
	protected $modelBuilder;

	/**
	 * Construct our CrudService
	 *
	 * @param Filesystem $fileSystem
	 * @param ModelBuilder $modelBuilder
	 */
	public function __construct
	(
		Filesystem $fileSystem,
		ModelBuilder $modelBuilder
	)
	{
		$this->progress = [];
		$this->fileSystem = $fileSystem;
		$this->modelBuilder = $modelBuilder;
	}

	/**
	 * Generate our CRUD scaffolding based on the provided options
	 * 
	 * @param array $options
	 * @return array
	 */
	public function generate(array $options)
	{
		try {
			if (in_array('migration', $options['files'])) {
				$this->generateMigration($options['entity_name'], $options['fields']);
			}
			if (in_array('model', $options['files'])) {
				$this->generateModel($options['entity_name'], $options['fields'], $options['paths']['models_path']);
			}

    		return $this->progress;
		}
		catch(\Exception $e) {
			$this->progress[] = 'Uh oh, something went wrong:';
			$this->progress[] = $e->getMessage();

			return $this->progress;
		}
	}

	/**
	 * Generate the migration file. Uses Jeffrey Way's awesome generators.
	 * 
	 * @param string $entity_name
	 * @param array $fields
	 */
	protected function generateMigration(string $entity_name, array $fields) 
	{
		$this->progress[] = 'Create migration';

		$validOptions = ['nullable', 'unique', 'index', 'unsigned'];
		$schema = [];
		foreach ($fields as $field) {
			$fieldConfig = $field['name'] . ':' . $field['type'];
			foreach ($field['options'] as $option) {
				if (in_array($option, $validOptions)) {
					$fieldConfig .= ':' . $option;
				}
			}
			$schema[] = $fieldConfig;
		}

		Artisan::call('make:migration:schema', [
    		'name' => 'create_'.strtolower($entity_name) . '_table',
    		'--schema' => implode(', ', $schema),
    		'--model' => false
		]);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate our Model file
	 * 
	 * @param string $entity_name
	 * @param array $fields
	 * @param string $models_path
	 */
	protected function generateModel(string $entity_name, array $fields, string $models_path) 
	{
		$this->progress[] = 'Create model';

		if ($this->fileSystem->exists(base_path($models_path) . $entity_name . '.php')) {
			$this->progress[] = 'Model already exists';
			return;
		}

		$options['namespace'] = $this->getNamespaceFromPath($models_path);
		$options['class'] = $entity_name;
		$options['table'] = strtolower($entity_name);
		$options['fillable'] = '';
		$options['hidden'] = '';

		$modelTemplate = $this->modelBuilder->create($options);

		$this->makeDirectoryIfNecessary($models_path);
		$this->fileSystem->put(base_path($models_path) . $entity_name . '.php', $modelTemplate);

		$this->progress[] = 'Success';
	}

	/**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     */
    protected function makeDirectoryIfNecessary($path)
    {
        if (!$this->fileSystem->isDirectory(base_path($path))) {
            $this->fileSystem->makeDirectory(base_path($path), 0755, true, true);
        }
    }

    /**
     * Get the namespace which corresponds to the given path
     *
     * @param string $path
     * @return string
     */
    protected function getNamespaceFromPath($path)
    {
        $appNamespace = rtrim(Container::getInstance()->getNamespace(), '\\');
      	
        return rtrim(str_replace('/', '\\', str_replace('app', $appNamespace, $path)), '\\');
    }

}