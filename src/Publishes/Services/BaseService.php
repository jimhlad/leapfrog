<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class BaseService
{

    /**
     * Our model class to be injected
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Construct our service instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Find all models
     *
     * @return Illuminate\Support\Collection
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Find a specific model
     *
     * @param  int $id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function find(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * Create the model in the database
     *
     * @param  array $payload
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create(array $payload)
    {
        return $this->model->create($payload);
    }

    /**
     * Update the model in the database
     *
     * @param int $id
     * @param array $payload
     * @return Illuminate\Database\Eloquent\Model
     */
    public function update(int $id, array $payload)
    {
        $item = $this->model->findOrFail($id);
        $item->update($payload);

        return $item;
    }

    /**
     * Delete a specific model
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $item = $this->model->findOrFail($id);
        $item->delete();
    }

}