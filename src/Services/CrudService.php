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
	 * @return boolean
	 */
	public function generate(array $options)
	{
		try {
			$this->progress[] = 'Create migration';

			Artisan::call('make:migration', [
        		'name' => 'create_'.strtolower($options['entity_name']).'_table'
    		]);

    		$this->progress[] = 'Success';

    		return implode("<br>", $this->progress);
		}
		catch(\Exception $e) {
			$this->progress[] = 'Uh oh, something went wrong:';
			$this->progress[] = $e->getMessage();

			return implode("<br>", $this->progress);
		}
	}

}