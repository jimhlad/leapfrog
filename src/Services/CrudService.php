<?php

namespace JimHlad\LeapFrog\Services;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use JimHlad\LeapFrog\Builders\RouteBuilder;
use JimHlad\LeapFrog\Builders\ModelBuilder;
use JimHlad\LeapFrog\Builders\ControllerBuilder;
use JimHlad\LeapFrog\Builders\ServiceBuilder;
use JimHlad\LeapFrog\Builders\CreateRequestBuilder;
use JimHlad\LeapFrog\Builders\UpdateRequestBuilder;
use JimHlad\LeapFrog\Builders\IndexViewBuilder;
use JimHlad\LeapFrog\Builders\CreateViewBuilder;
use JimHlad\LeapFrog\Builders\EditViewBuilder;
use JimHlad\LeapFrog\Builders\FormConfigBuilder;

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
     * Our Builder classes
     *
     * @var {Type}Builder
     */
    protected $routeBuilder;
	protected $modelBuilder;
	protected $controllerBuilder;
	protected $serviceBuilder;
	protected $createRequestBuilder;
	protected $updateRequestBuilder;
	protected $indexViewBuilder;
	protected $createViewBuilder;
	protected $editViewBuilder;
	protected $formConfigBuilder;

	/**
	 * Construct our CrudService
	 *
	 * @param Filesystem $fileSystem
	 * @param RouteBuilder $routeBuilder
	 * @param ModelBuilder $modelBuilder
	 * @param ControllerBuilder $controllerBuilder
	 * @param ServiceBuilder $serviceBuilder
	 * @param CreateRequestBuilder $createRequestBuilder
	 * @param UpdateRequestBuilder $updateRequestBuilder
	 * @param IndexViewBuilder $indexViewBuilder
	 * @param FormConfigBuilder $formConfigBuilder
	 */
	public function __construct
	(
		Filesystem $fileSystem,
		RouteBuilder $routeBuilder,
		ModelBuilder $modelBuilder,
		ControllerBuilder $controllerBuilder,
		ServiceBuilder $serviceBuilder,
		CreateRequestBuilder $createRequestBuilder,
		UpdateRequestBuilder $updateRequestBuilder,
		IndexViewBuilder $indexViewBuilder,
		CreateViewBuilder $createViewBuilder,
		EditViewBuilder $editViewBuilder,
		FormConfigBuilder $formConfigBuilder
	)
	{
		$this->progress = [];
		$this->fileSystem = $fileSystem;
		$this->routeBuilder = $routeBuilder;
		$this->modelBuilder = $modelBuilder;
		$this->controllerBuilder = $controllerBuilder;
		$this->serviceBuilder = $serviceBuilder;
		$this->createRequestBuilder = $createRequestBuilder;
		$this->updateRequestBuilder = $updateRequestBuilder;
		$this->indexViewBuilder = $indexViewBuilder;
		$this->createViewBuilder = $createViewBuilder;
		$this->editViewBuilder = $editViewBuilder;
		$this->formConfigBuilder = $formConfigBuilder;
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
			if (in_array('route', $options['files'])) {
				$this->generateRoutes($options);
			}
			if (in_array('migration', $options['files'])) {
				$this->generateMigration($options);
			}
			if (in_array('model', $options['files'])) {
				$this->generateModel($options);
			}
			if (in_array('controller', $options['files'])) {
				$this->generateController($options);
			}
			if (in_array('service', $options['files'])) {
				$this->generateService($options);
			}
			if (in_array('createrequest', $options['files'])) {
				$this->generateRequest($options, 'Create');
			}
			if (in_array('updaterequest', $options['files'])) {
				$this->generateRequest($options, 'Update');
			}
			if (in_array('indexview', $options['files'])) {
				$this->generateIndexView($options);
			}
			if (in_array('createview', $options['files'])) {
				$this->generateCreateView($options);
			}
			if (in_array('editview', $options['files'])) {
				$this->generateEditView($options);
			}
			if (in_array('formconfig', $options['files'])) {
				$this->generateFormConfig($options);
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

		$validOptions = ['nullable', 'unique', 'index', 'unsigned', 'foreign'];
		$schema = [];
		foreach ($options['fields'] as $field) {
			$fieldConfig = $field['name'] . ':' . $field['type'];
			foreach ($field['options'] as $option) {
				if (in_array($option, $validOptions)) {
					$fieldConfig .= ':' . $option;
				}
			}
			if (in_array($field['custom'], ['true', 'false']) && in_array($field['type'], ['boolean'])) {
				$fieldConfig .= ':default(' . $field['custom'] . ')';
			}
			$schema[] = $fieldConfig;
		}

		Artisan::call('make:migration:schema', [
    		'name' => 'create_'.snake_case(str_plural($options['entity_name'])) . '_table',
    		'--schema' => implode(', ', $schema),
    		'--model' => false
		]);

		if (config('leapfrog.auto_run_migrations') === true) {
			Artisan::call('migrate');
		}

		$this->progress[] = 'Success';
	}

	/**
	 * Generate the routes
	 * 
	 * @param array $options
	 */
	protected function generateRoutes(array $options) 
	{
		$this->progress[] = 'Create routes';

		$entityName = $options['entity_name'];
		$controllersPath = $options['paths']['controllers_path'];

		if ($this->fileSystem->exists(base_path('routes/web.php'))) {
			$config = [];
			$config['controllersPath'] = '';
			$config = $this->addEntityNameVariations($config, $entityName);
			$controllerNamespace = $this->getNamespaceFromPath($controllersPath);
			if ($controllerNamespace !== 'App\Http\Controllers') {
				$config['controllersPath'] = '\\' . $controllerNamespace . '\\';
			}
			$routeTemplate = $this->routeBuilder->create($config);
			$this->fileSystem->append(base_path('routes/web.php'), $routeTemplate);
		}

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
		$fields = $options['fields'];
		$relations = $options['relations'];

		if ($this->fileSystem->exists(base_path($modelsPath) . $entityName . '.php')) {
			$this->progress[] = 'Model already exists';
			return;
		}

		$fillable = $this->onlyFieldNamesWithOption($fields, 'fillable');
		$hidden = $this->onlyFieldNamesWithOption($fields, 'hidden');

		$config = $this->addEntityNameVariations([], $entityName);
		$config['namespace'] = $this->getNamespaceFromPath($modelsPath);
		$config['class'] = $entityName;
		$config['table'] = snake_case(str_plural($entityName));
		$config['fillable'] = implode(",\n\t\t", $this->wrapFieldNames($fillable));
		$config['hidden'] = implode(",\n\t\t", $this->wrapFieldNames($hidden));
		$config['relations'] = $this->convertRelationModelPathsToNamespaces($relations);

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
		$viewsPath = $options['paths']['views_path'];
		$entityName = $options['entity_name'];
		$files = $options['files'];

		if ($this->fileSystem->exists(base_path($controllersPath) . $entityName . 'Controller.php')) {
			$this->progress[] = 'Controller already exists';
			return;
		}

		$config['namespace'] = $this->getNamespaceFromPath($controllersPath);
		$config['servicesNamespace'] = $this->getNamespaceFromPath($servicesPath);
		$config['requestsNamespace'] = $this->getNamespaceFromPath($requestsPath);
		$config['viewsPath'] = $this->getFullViewsPathFromPath($viewsPath);
		$config['createRequest'] = (in_array('createrequest', $files) ? "{$entityName}CreateRequest" : 'Request' );
		$config['updateRequest'] = (in_array('updaterequest', $files) ? "{$entityName}UpdateRequest" : 'Request' );
		$config = $this->addEntityNameVariations($config, $entityName);

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
	 * Generate our Service file
	 * 
	 * @param array $options
	 */
	protected function generateService(array $options) 
	{
		$this->progress[] = 'Create service';

		$servicesPath = $options['paths']['services_path'];
		$modelsPath = $options['paths']['models_path'];
		$entityName = $options['entity_name'];
		$fields = $options['fields'];
		$relations = $options['relations'];

		if ($this->fileSystem->exists(base_path($servicesPath) . $entityName . 'Service.php')) {
			$this->progress[] = 'Service already exists';
			return;
		}

		$config['namespace'] = $this->getNamespaceFromPath($servicesPath);
		$config['modelsNamespace'] = $this->getNamespaceFromPath($modelsPath);
		$config['entity'] = $entityName;
		$config['fields'] = $fields;
		$config['relations'] = $this->convertRelationModelPathsToNamespaces($relations);

		$serviceTemplate = $this->serviceBuilder->create($config);
		$this->makeDirectoryIfNecessary($servicesPath);
		$this->fileSystem->put(base_path($servicesPath) . $entityName . 'Service.php', $serviceTemplate);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate our Request file
	 * 
	 * @param array $options
	 */
	protected function generateRequest(array $options, $type = 'Create') 
	{
		$this->progress[] = 'Create request';

		$requestsPath = $options['paths']['requests_path'];
		$entityName = $options['entity_name'];
		$fields = $options['fields'];

		if ($this->fileSystem->exists(base_path($requestsPath) . $entityName . "{$type}Request.php")) {
			$this->progress[] = "{$type}Request already exists";
			return;
		}

		$config['namespace'] = $this->getNamespaceFromPath($requestsPath);
		$config['entity'] = $entityName;

		$requiredFields = $this->onlyFieldNamesWithoutOption($fields, 'nullable');
		$uniqueFields = $this->onlyFieldNamesWithOption($fields, 'unique');
		
		$config['rules'] = "";
		foreach ($requiredFields as $field) {
			$config['rules'] .= "'{$field}' => 'required";
			if (in_array($field, $uniqueFields)) {
				$tableName = snake_case(str_plural($entityName));
				if ($type === 'Create') {
					$config['rules'] .= "|unique:" . $tableName;
				}
				if ($type === 'Update') {
					$config['rules'] .= "|unique:" . $tableName . "," . $field . ",'.\$id";
					$config['rules'] .= ",\n\t\t\t";
					continue;
				}
			}
			$config['rules'] .= "',\n\t\t\t";
		}

		$builderFn = strtolower($type) . "RequestBuilder";
		$requestTemplate = $this->$builderFn->create($config);
		$this->makeDirectoryIfNecessary($requestsPath);
		$this->fileSystem->put(base_path($requestsPath) . $entityName . "{$type}Request.php", $requestTemplate);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate our index view file
	 * 
	 * @param array $options
	 */
	protected function generateIndexView(array $options) 
	{
		$this->progress[] = 'Create index view';

		$viewsPath = $options['paths']['views_path'];
		$entityName = $options['entity_name'];
		$fields = $options['fields'];

		if ($this->fileSystem->exists(base_path($viewsPath) . snake_case($entityName) . '/index.blade.php')) {
			$this->progress[] = "Index view already exists";
			return;
		}

		$config = [];
		$config = $this->addEntityNameVariations($config, $entityName);
		$fields = $this->onlyFieldsWithoutOption($fields, 'foreign');
		$config['fieldNames'] = $this->onlyFieldNamesWithOption($fields, 'fillable');

		$viewTemplate = $this->indexViewBuilder->create($config);
		$this->makeDirectoryIfNecessary($viewsPath . snake_case($entityName));
		$this->fileSystem->put(base_path($viewsPath) . snake_case($entityName) . '/index.blade.php', $viewTemplate);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate our create view file
	 * 
	 * @param array $options
	 */
	protected function generateCreateView(array $options) 
	{
		$this->progress[] = 'Create the create view';

		$viewsPath = $options['paths']['views_path'];
		$entityName = $options['entity_name'];

		if ($this->fileSystem->exists(base_path($viewsPath) . snake_case($entityName) . '/create.blade.php')) {
			$this->progress[] = "Create view already exists";
			return;
		}

		$config = [];
		$config = $this->addEntityNameVariations($config, $entityName);

		$viewTemplate = $this->createViewBuilder->create($config);
		$this->makeDirectoryIfNecessary($viewsPath . snake_case($entityName));
		$this->fileSystem->put(base_path($viewsPath) . snake_case($entityName) . '/create.blade.php', $viewTemplate);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate our edit view file
	 * 
	 * @param array $options
	 */
	protected function generateEditView(array $options) 
	{
		$this->progress[] = 'Create the edit view';

		$viewsPath = $options['paths']['views_path'];
		$entityName = $options['entity_name'];

		if ($this->fileSystem->exists(base_path($viewsPath) . snake_case($entityName) . '/edit.blade.php')) {
			$this->progress[] = "Edit view already exists";
			return;
		}

		$config = [];
		$config = $this->addEntityNameVariations($config, $entityName);

		$viewTemplate = $this->editViewBuilder->create($config);
		$this->makeDirectoryIfNecessary($viewsPath . snake_case($entityName));
		$this->fileSystem->put(base_path($viewsPath) . snake_case($entityName) . '/edit.blade.php', $viewTemplate);

		$this->progress[] = 'Success';
	}

	/**
	 * Generate the form config
	 * 
	 * @param array $options
	 */
	protected function generateFormConfig(array $options) 
	{
		$this->progress[] = 'Create form config';

		$entityName = $options['entity_name'];
		$fields = $options['fields'];
		$relations = $options['relations'];

		if ($this->fileSystem->exists(base_path('config/forms/' . snake_case($entityName) . '.php'))) {
			$this->progress[] = "Form config already exists";
			return;
		}

		$config = [];
		$config = $this->addEntityNameVariations($config, $entityName);
		$config['fields'] = $this->onlyFieldsWithoutOption($fields, 'foreign');
		$config['relations'] = $this->convertRelationModelPathsToNamespaces($relations);

		$configTemplate = $this->formConfigBuilder->create($config);
		$this->makeDirectoryIfNecessary('config/forms');
		$this->fileSystem->put(base_path('config/forms/' . snake_case($entityName) . '.php'), $configTemplate);

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
     * Fix model paths so that they correspond to a valid namespace
     *
     * @param array $relations
     * @return array
     */
    protected function convertRelationModelPathsToNamespaces(array $relations)
    {
    	$newRelations = [];
    	$appNamespace = rtrim(Container::getInstance()->getNamespace(), '\\');

    	foreach ($relations as $relation) {
    		$relation['model_path'] = rtrim(str_replace('/', '\\', str_replace('app', $appNamespace, $relation['model_path'])), '\\');
    		$newRelations[] = $relation;
    	}

        return $newRelations;
    }

    /**
     * Get the full view path which corresponds to the given view path
     *
     * @param string $path
     * @return string
     */
    protected function getFullViewsPathFromPath(string $path)
    {
        if ($path === 'resources/views/') {
        	return '';
        }

        $path = str_replace('resources/views/', '', $path);
        
        return str_replace('/', '.', $path);
    }

    /**
     * Get only those names which have a particular option set (e.g. fillable, hidden, etc)
     *
     * @param array $fields
     * @param string $option
     * @param boolean $fieldNamesOnly
     * @param boolean $inverse
     * @return array
     */
    protected function onlyFieldsWithOption(array $fields, $option = '', $fieldNamesOnly = false, $inverse = false)
    {
    	$filteredFields = [];
        foreach ($fields as $field) {
        	if ($inverse) {
        		if (!in_array($option, $field['options'])) {
        			$filteredFields[] = ($fieldNamesOnly ? $field['name'] : $field);
        		}
        	}
        	else {
        		if (in_array($option, $field['options'])) {
        			$filteredFields[] = ($fieldNamesOnly ? $field['name'] : $field);
        		}
        	}
        }

        return $filteredFields;
    }

    /**
     * Get only those fields which do NOT have a particular option set (e.g. fillable, hidden, etc)
     *
     * @param array $fields
     * @param string $option
     * @param boolean $fieldNamesOnly
     * @return array
     */
    protected function onlyFieldsWithoutOption(array $fields, $option = '', $fieldNamesOnly = false)
    {
    	return $this->onlyFieldsWithOption($fields, $option, $fieldNamesOnly, true);
    }

    /**
     * Get only those fields NAMES which have a particular option set (e.g. fillable, hidden, etc)
     *
     * @param array $fields
     * @param string $option
     * @return array
     */
    protected function onlyFieldNamesWithOption(array $fields, $option = '')
    {
    	return $this->onlyFieldsWithOption($fields, $option, true);
    }

    /**
     * Get only those fields NAMES which have DO NOT a particular option set (e.g. fillable, hidden, etc)
     *
     * @param array $fields
     * @param string $option
     * @return array
     */
    protected function onlyFieldNamesWithoutOption(array $fields, $option = '')
    {
    	return $this->onlyFieldsWithoutOption($fields, $option, true);
    }

    /**
     * Given an array of field names, wrap them in quotes
     *
     * @param array $fields
     * @param string $leftQuote
     * @param string $rightQuote
     * @return array
     */
    protected function wrapFieldNames(array $fields, $leftQuote = "'", $rightQuote = "'")
    {
    	$quoted = [];

    	foreach ($fields as $field) {
    		$quoted[] = $leftQuote . $field . $rightQuote;
    	}

    	return $quoted;
    }

    /**
     * Given an array and an entityName, add all the variations of that entityName to the array
     *
     * @param array $config
     * @param string $entityName
     * @return array
     */
    protected function addEntityNameVariations(array $config, string $entityName)
    {
    	$config['entity'] = $entityName;
    	$config['entityLower'] = strtolower($entityName);
    	$config['entityPlural'] = str_plural($entityName);
		$config['entitySnake'] = snake_case($entityName);
		$config['entitySnakePlural'] = snake_case(str_plural($entityName));
		$config['entityCamel'] = camel_case($entityName);
		$config['entityCamelPlural'] = camel_case(str_plural($entityName));
		$config['entitySlugPlural'] = str_slug(snake_case(str_plural($entityName)));
		$config['entityWithSpaces'] = ucwords(str_replace('_', ' ', snake_case($entityName)));
		$config['entityPluralWithSpaces'] = ucwords(str_replace('_', ' ', snake_case(str_plural($entityName))));

		return $config;
    }

}