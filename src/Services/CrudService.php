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
				$this->generateMigration($options);
			}
			if (in_array('model', $options['files'])) {
				$this->generateModel($options);
			}
			if (in_array('controller', $options['files'])) {
				$this->generateController($options);
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
	 * @param array $options
	 */
	protected function generateMigration(array $options) 
	{
		$this->progress[] = 'Create migration';

		$validOptions = ['nullable', 'unique', 'index', 'unsigned'];
		$schema = [];
		foreach ($options['fields'] as $field) {
			$fieldConfig = $field['name'] . ':' . $field['type'];
			foreach ($field['options'] as $option) {
				if (in_array($option, $validOptions)) {
					$fieldConfig .= ':' . $option;
				}
			}
			$schema[] = $fieldConfig;
		}

		Artisan::call('make:migration:schema', [
    		'name' => 'create_'.strtolower($options['entity_name']) . '_table',
    		'--schema' => implode(', ', $schema),
    		'--model' => false
		]);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate our Model file
	 * 
	 * @param array $options
	 */
	protected function generateModel(array $options) 
	{
		$this->progress[] = 'Create model';

		$modelsPath = $options['paths']['models_path'];
		$entityName = $options['entity_name'];
		$fields = $options ['fields'];

		if ($this->fileSystem->exists(base_path($modelsPath) . $entityName . '.php')) {
			$this->progress[] = 'Model already exists';
			return;
		}

		$config['namespace'] = $this->getNamespaceFromPath($modelsPath);
		$config['class'] = $entityName;
		$config['table'] = strtolower($entityName);
		$config['fillable'] = implode(", \n\t\t", $this->onlyFieldsWithOption($fields, 'fillable'));
		$config['hidden'] = implode(", \n\t\t", $this->onlyFieldsWithOption($fields, 'hidden'));

		$modelTemplate = $this->modelBuilder->create($config);
		$this->makeDirectoryIfNecessary($modelsPath);
		$this->fileSystem->put(base_path($modelsPath) . $entityName . '.php', $modelTemplate);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate our Controller file
	 * 
	 * @todo Re-factor this function into smaller components
	 * 
	 * @param array $options
	 */
	protected function generateController(array $options) 
	{
		$this->progress[] = 'Create controller';

		$controllersPath = $options['paths']['controllers_path'];
		$servicesPath = $options['paths']['services_path'];
		$requestsPath = $options['paths']['requests_path'];
		$entityName = $options['entity_name'];
		$files = $options['files'];

		if ($this->fileSystem->exists(base_path($controllersPath) . $entityName . 'Controller.php')) {
			$this->progress[] = 'Controller already exists';
			return;
		}

		$config['namespace'] = $this->getNamespaceFromPath($controllersPath);
		$config['servicesNamespace'] = $this->getNamespaceFromPath($servicesPath);
		$config['requestsNamespace'] = $this->getNamespaceFromPath($requestsPath);
		$config['entity'] = $entityName;
		$config['entityLower'] = strtolower($entityName);
		$config['entityLowerPlural'] = strtolower(str_plural($entityName));
		$config['createRequest'] = (in_array('createrequest', $files) ? "{$entityName}CreateRequest" : 'Request' );
		$config['updateRequest'] = (in_array('updaterequest', $files) ? "{$entityName}UpdateRequest" : 'Request' );

		$useClasses = [];
		$useClasses[] = $config['servicesNamespace'] . '\\' . $entityName . 'Service';
		if (in_array('createrequest', $files)) {
			$useClasses[] = $config['requestsNamespace'] . '\\' . $config['createRequest'];
		}
		if (in_array('updaterequest', $files)) {
			$useClasses[] = $config['requestsNamespace'] . '\\' . $config['updateRequest'];
		}
		if (count($useClasses) === 1) {
			$config['requestsNamespace'] = 'Illuminate\Http';
			$useClasses[] = 'Illuminate\Http\Request';
		}

		$config['useStatements'] = '';
		foreach ($useClasses as $useClass) {
			$config['useStatements'] .= "use {$useClass}; \n";
		}
		$config['useStatements'] = rtrim($config['useStatements']);
		
		$controllerTemplate = $this->controllerBuilder->create($config);
		$this->makeDirectoryIfNecessary($controllersPath);
		$this->fileSystem->put(base_path($controllersPath) . $entityName . 'Controller.php', $controllerTemplate);

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