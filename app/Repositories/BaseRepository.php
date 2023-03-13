<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

abstract class BaseRepository
{
    protected $model;

    public function __construct($model)
    {
        $this->model = new $model;
    }

    public function insertData(array $data)
    {
        return $this->model->create($data);
    }

    public function userUpdate(array $data)
    {
        return $this->model->find(Auth::id())->update($data);
    }

    public function findUpdate(int $id, array $data)
    {
        return $this->model->find($id)->update($data);
    }

    public function findData(int $id)
    {
        return $this->model->find($id);
    }

    public function getData(int $paginate = null, string $orderBy = "ASC")
    {
        if (is_null($paginate)) {
            return $this->model->orderBy('id', $orderBy)->get();
        } else {
            return $this->model->orderBy('id', $orderBy)->paginate($paginate);
        }
    }

    public function getDistinct(string $col)
    {
        return $this->model->select($col)->distinct()->get();
    }

    public function whereFirst(array $cond)
    {
        return $this->model->where($cond)->first();
    }

    public function whereGet(array $cond)
    {
        return $this->model->where($cond)->get();
    }

    public function whereNotOrLike(string $col, string $cond, $value)
    {
        return $this->model->where($col, $cond, $value)->get();
    }

    public function wherePaginate(array $cond, int $paginate)
    {
        return $this->model->where($cond)->paginate($paginate);
    }

    public function updateData(array $cond, array $data)
    {
        return $this->model->where($cond)->update($data);
    }

    public function whereExists(array $cond)
    {
        return $this->model->where($cond)->exists();
    }

    public function whereGetValue(array $cond, string $value)
    {
        return $this->model->where($cond)->value($value);
    }

    public function whereGetAndOrderBy(array $cond, string $col, string $order = 'ASC')
    {
        return $this->model->where($cond)->orderBy($col, $order)->get();
    }

    public function whereIn(string $column, array $ids)
    {
        return $this->model->whereIn($column, $ids)->get();
    }

    public function firstOrCreate(array $data)
    {
        return $this->model->firstOrCreate($data);
    }

    public function updateOrCreate(array $cond, array $data)
    {
        return $this->model->updateOrCreate($cond, $data);
    }

    public function whereCount(array $cond)
    {
        return $this->model->where($cond)->count();
    }

    public function getFillable()
    {
        return $this->model->getFillable();
    }
}
