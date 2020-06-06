<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryContract
{
    /**
     * Name of the Model with absolute namespace
     *
     * @var string
     */
    protected $modelName;

    /**
     * Stores the model object.
     *
     * @var Eloquent
     */
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Instantiate Model
     *
     * @throws \Exception
     */
    public function setModel()
    {
        //check if the class exists
        if (class_exists($this->modelName)) {
            $this->model = new $this->modelName();

            //check object is a instanceof Illuminate\Database\Eloquent\Model
            if (!$this->model instanceof Model) {
                throw new \Exception("{$this->modelName} must be an instance of Illuminate\Database\Eloquent\Model");
            }
        } else {
            throw new \Exception('No model name defined');
        }
    }

    /**
     * Get Model instance
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Find a resource by id
     *
     * @param $id
     * @param null $relation
     * @return Model|null
     */
    public function findOne($id, $relation = null)
    {
        return $this->findOneBy(['id' => $id], $relation);
    }

    /**
     * @param $id
     * @param null $relation
     * @param array|null $orderBy
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|Collection|Model|Model[]|mixed
     */
    public function findOrFail($id, $relation = null, array $orderBy = null)
    {
        return $this->prepareModelForRelationAndOrder($relation, $orderBy)->findOrFail($id);
    }

    /**
     * Find resource
     *
     * @param $field
     * @param $value
     * @return \Illuminate\Support\Collection|null|static
     */
    public function findBy(array $searchCriteria = [], $relation = null, array $orderBy = null)
    {
        $model = $this->prepareModelForRelationAndOrder($relation, $orderBy);
        $limit = !empty($searchCriteria['per_page']) ? (int)$searchCriteria['per_page'] : 15; // it's needed for pagination

        $queryBuilder = $model->where(function ($query) use ($searchCriteria) {

            $this->applySearchCriteriaInQueryBuilder($query, $searchCriteria);
        });
        if (!empty($searchCriteria['per_page'])) {
            return $queryBuilder->paginate($limit);
        }
        return $queryBuilder->get();
    }

    /**
     * Search All resources by any values of a key
     *
     * @param string $key
     * @param array $values
     * @return Collection
     */
    public function findIn($key, array $values, $relation = null, array $orderBy = null)
    {
        return $this->prepareModelForRelationAndOrder($relation, $orderBy)->whereIn($key, $values)->get();
    }


    /**
     * @param null $perPage
     * @param null $relation
     * @param array|null $orderBy
     * @return Contracts\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection|Model[]
     */
    public function findAll($perPage = null, $relation = null, array $orderBy = null)
    {
        $model = $this->prepareModelForRelationAndOrder($relation, $orderBy);
        if ($perPage) {
            return $model->paginate($perPage);
        }

        return $model->get();
    }
    /**
     * Find resource
     *
     * @param array $params
     * @param array $fields Which fields to select
     * @return \Illuminate\Support\Collection|null|static
     */
    public function findByProperties(array $params, array $fields = ['*'])
    {
        $query = $this->model->query();

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get($fields);
    }

    /**
     * Find resource
     *
     * @param array $params
     * @param array $fields Which fields to select
     * @return Model|null|static
     */
    public function findOneByProperties(array $params, array $fields = ['*'])
    {
        $query = $this->model->query();

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first($fields);
    }

    /**
     * Find a resource by criteria
     *
     * @param array $criteria
     * @param null $relation
     * @return Model|null
     */
    public function findOneBy(array $criteria, $relation = null)
    {
        return $this->prepareModelForRelationAndOrder($relation)->where($criteria)->first();
    }

    /**
     * Find resources by ids
     *
     * @param array $ids
     * @return \Illuminate\Support\Collection|null|static
     */
    public function findByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    /**
     * Retrieve all resources
     *
     * @return \Illuminate\Support\Collection|null|static
     */
    public function getAll()
    {
        return $this->model->get();
    }

    /**
     * Save a resource
     *
     * @param array $data
     * @return Model
     */
    public function save(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Save resources
     *
     * @param array|Collection $resources
     * @return mixed
     */
    public function saveMany($resources)
    {
        DB::transaction(function () use ($resources) {
            foreach ($resources as $resource) {
                $this->save($resource);
            }
        });
    }

    /**
     * Update resource
     *
     * @param $resource
     * @param $data
     * @return \Illuminate\Support\Collection|null|static
     */
    public function update($resource, $data = [])
    {
        if (is_array($data) && count($data) > 0) {
            $resource->fill($data);
        }

        $this->save($resource);

        return $resource;
    }

    /**
     * Delete resource
     *
     * @param $resource
     * @return \Illuminate\Support\Collection|null|static
     */
    public function delete($resource)
    {
        $resource->delete();

        return $resource;
    }



    /**
     * Creates a new model from properties
     *
     * @param array $properties
     * @return mixed
     */
    public function create(array $properties)
    {
        if (is_array($properties) && count($properties)) {
            return $this->model->create($properties);
        }

        return null;
    }

    /**
     * @param $relation
     * @param array|null $orderBy [[Column], [Direction]]
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    private function prepareModelForRelationAndOrder($relation, array $orderBy = null)
    {
        $model = $this->model;
        if ($relation) {
            $model = $model->with($relation);
        }
        if ($orderBy) {
            $model = $model->orderBy($orderBy['column'], $orderBy['direction']);
        }
        return $model;
    }
}
