<?php

namespace JimHlad\LeapFrog\Services;

use Illuminate\Support\Facades\Artisan;

class CrudService
{

	/**
	 * Current progress as an array
	 *
	 * @var array
	 */
	protected $progress;

	/**
	 * Construct our CrudService
	 */
	public function __construct()
	{
		$this->progress = [];
	}

	/**
	 * Generate our CRUD scaffolding based on the provided options
	 * 
	 * @param  array $options
	 * @return array
	 */
	public function generate(array $options)
	{
		try {
			$this->generateMigration($options['entity_name'], $options['fields']);

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
	 * @param  string $entity_name
	 * @param  array $fields
	 * @return void
	 */
	private function generateMigration(string $entity_name, array $fields) {
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
    		'--schema' => implode(", ", $schema),
    		'--model' => false
		]);

		$this->progress[] = 'Success';
	}

}