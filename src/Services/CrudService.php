<?php

namespace JimHlad\LeapFrog\Services;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use JimHlad\LeapFrog\Builders\ModelBuilder;
use JimHlad\LeapFrog\Builders\ControllerBuilder;

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
	 * Our ControllerBuilder class
	 *
	 * @var ControllerBuilder
	 */
	protected $controllerBuilder;

	/**
	 * Construct our CrudService
	 *
	 * @param Filesystem $fileSystem
	 * @param ModelBuilder $modelBuilder
	 */
	public function __construct
	(
		Filesystem $fileSystem,
		ModelBuilder $modelBuilder,
		ControllerBuilder $controllerBuilder
	)
	{
		$this->progress = [];
		$this->fileSystem = $fileSystem;
		$this->modelBuilder = $modelBuilder;
		$this->controllerBuilder = $controllerBuilder;
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
			if (in_array('controller', $options['files'])) {
				$this->generateController($options['entity_name'], 
										  $options['files'], 
										  $options['paths']['controllers_path'], 
										  $options['paths']['requests_path'],
										  $options['paths']['services_path']);
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
		$options['fillable'] = implode(', ', $this->onlyFieldsWithOption($fields, 'fillable'));
		$options['hidden'] = implode(', ', $this->onlyFieldsWithOption($fields, 'hidden'));

		$modelTemplate = $this->modelBuilder->create($options);

		$this->makeDirectoryIfNecessary($models_path);
		$this->fileSystem->put(base_path($models_path) . $entity_name . '.php', $modelTemplate);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate our Controller file
	 * 
	 * @param string $entity_name
	 * @param array $files
	 * @param string $controllers_path
	 */
	protected function generateController(
		string $entity_name, 
		array $files, 
		string $controllers_path, 
		string $requests_path,
		string $services_path
	) 
	{
		$this->progress[] = 'Create controller';

		if ($this->fileSystem->exists(base_path($controllers_path) . $entity_name . 'Controller.php')) {
			$this->progress[] = 'Controller already exists';
			return;
		}

		$options['namespace'] = $this->getNamespaceFromPath($controllers_path);
		$options['entity'] = $entity_name;
		$options['entityLower'] = strtolower($entity_name);
		$options['entityLowerPlural'] = strtolower(str_plural($entity_name));
		$options['createRequest'] = (in_array('createrequest', $files) ? "{$entity_name}CreateRequest" : 'Request' );
		$options['updateRequest'] = (in_array('updaterequest', $files) ? "{$entity_name}UpdateRequest" : 'Request' );
		$options['serviceNamespace'] = $this->getNamespaceFromPath($services_path . $entity_name . 'Service');
		$options['createRequestNamespace'] = 
				(in_array('createrequest', $files)  ? 
				$this->getNamespaceFromPath($requests_path . $options['createRequest']) : 'Illuminate\Http\Request' );
		$options['updateRequestNamespace'] = 
				(in_array('updaterequest', $files)  ? 
				$this->getNamespaceFromPath($requests_path . $options['updateRequest']) : 'Illuminate\Http\Request' );

		$controllerTemplate = $this->controllerBuilder->create($options);

		$this->makeDirectoryIfNecessary($controllers_path);
		$this->fileSystem->put(base_path($controllers_path) . $entity_name . 'Controller.php', $controllerTemplate);

		$this->progress[] = 'Success';
	}

	/**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     */
    protected function makeDirectoryIfNecessary(string $path)
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
    protected function getNamespaceFromPath(string $path)
    {
        $appNamespace = rtrim(Container::getInstance()->getNamespace(), '\\');

        return rtrim(str_replace('/', '\\', str_replace('app', $appNamespace, $path)), '\\');
    }

    /**
     * Get only those fields names which have a particular option set (e.g. fillable, hidden, etc)
     *
     * @param array $fields
     * @param string $option
     * @return array
     */
    protected function onlyFieldsWithOption(array $fields, $option = '')
    {
    	$filteredFields = [];
        foreach ($fields as $field) {
        	if (in_array($option, $field['options'])) {
        		$filteredFields[] = "'" . $field['name'] . "'";
        	}
        }

        return $filteredFields;
    }

}