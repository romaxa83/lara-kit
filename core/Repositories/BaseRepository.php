<?php

namespace Core\Repositories;

use App\Traits\Filterable;
use Core\Models\BaseModel;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 *  payload - массив , где ключ - поле в модели, а значение - значение в бд
 *  ->getByFields([
 *      ['id' => 1],
 *      ['name' => 'some name],
 *  ])
 *
 */
abstract class BaseRepository
{
    public function __construct()
    {}

    abstract protected function modelClass(): string;

    protected function eloquentBuilder(): Builder
    {
        return $this->modelClass()::query();
    }

    protected function queryBuilder(): QueryBuilder
    {
        return DB::table($this->modelClass()::TABLE);
    }

    public function getBy(
        $field,
        $value,
        array $relations = [],
        $withException = false,
        $exceptionMessage = 'Model not found',
        $withoutId = null,
    ): null|BaseModel|Model
    {
        $result = $this->eloquentBuilder()
            ->with($relations)
            ->when($withoutId, fn(Builder $b): Builder => $b->whereNot('id', $withoutId))
            ->where($field, $value)
            ->first()
        ;

        if ($withException && null === $result) {
            throw new DomainException($exceptionMessage, Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    public function getByFields(
        array $payload = [],
        array $relation = [],
              $withException = false,
              $exceptionMessage = 'Model not found'
    ): null|BaseModel|Model
    {
        $query = $this->eloquentBuilder()
            ->with($relation);

        foreach ($payload as $field => $value) {
            $query->where($field, $value);
        }

        $result = $query->first();

        if ($withException && null === $result) {
            throw new DomainException($exceptionMessage, Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    public function getByFieldsObj(
        array $payload = [],
        array $select = ['*'],
        bool $withException = false,
        string $exceptionMessage = 'Model not found'
    ): ?object
    {
        $query = $this->eloquentBuilder()->select($select);

        foreach ($payload as $field => $value) {
            $query->where($field, $value);
        }

        $result = $query->toBase()->first();

        if ($withException && null === $result) {
            throw new DomainException($exceptionMessage, Response::HTTP_NOT_FOUND);
        }

        return $result;
    }

    public function getAll(
        array $relation = [],
        array $filters = [],
              $onlyActive = false,
        string|array $sort = 'id'
    ) {
        $query = $this->eloquentBuilder()
            ->filter($filters)
            ->with($relation);

        if ($onlyActive) {
            $query->active();
        }

        if(is_array($sort)){
            foreach ($sort as $field => $type) {
                $query->orderBy($field, $type);
            }
        } else {
            $query->latest($sort);
        }

        return $query->get();
    }

    public function getAllObj(
        array $select = ['*'],
        array $relation = [],
        array $filters = [],
        string|array $sort = 'id'
    ) {
        $query = $this->eloquentBuilder()
            ->select($select)
            ->filter($filters)
            ->with($relation);

        if(is_array($sort)){
            foreach ($sort as $field => $type) {
                $query->orderBy($field, $type);
            }
        } else {
            $query->latest($sort);
        }

        return $query->toBase()->get();
    }

    public function getAllByFields(
        array $payload = [],
        array $relation = [],
    ): Collection
    {
        $query = $this->eloquentBuilder()
            ->with($relation);

        foreach ($payload as $field => $value) {
            if(is_array($value)){
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }

    public function getAllBy(
        string $field = 'id',
        array $data = [],
        array $relation = [],
    ): Collection
    {
        return $this->eloquentBuilder()
            ->with($relation)
            ->whereIn($field, $data)
            ->get()
            ;
    }

    public function getModelsBuilder(
        array $select = ['*'],
        array $relation = [],
        array $filters = [],
        string|array $sort = 'id'
    ): Builder
    {
        $query = $this->eloquentBuilder()
            ->select($select)
            ->with($relation);

        if($this->checkFilterTrait()){
            $query->filter($filters);
        }

        if(!isset($filters['sort'])){
            if(is_array($sort)){
                foreach ($sort as $field => $type) {
                    $query->orderBy($field, $type);
                }
            } else {
                $query->latest($sort);
            }
        }

        return $query;
    }

    public function getPagination(
        array $select = ['*'],
        array $relation = [],
        array $filters = [],
        string|array $sort = 'id'
    ): LengthAwarePaginator
    {
        return $this->getModelsBuilder(
            $select,
            $relation,
            $filters,
            $sort
        )->paginate(
            perPage: $this->getPerPage($filters),
            page: $this->getPage($filters)
        );
    }

    public function getCollection(
        array $relation = [],
        array $filters = [],
        string|array $sort = 'id'
    ): Collection
    {
        return $this->getModelsBuilder(
            $relation,
            $filters,
            $sort
        )->get();
    }

    public function getList(
        array $relation = [],
        array $filters = [],
        string|array $sort = 'id'
    ): Collection
    {
        $query = $this->eloquentBuilder()
            ->with($relation)
        ;

        if($this->checkFilterTrait()){
            $query->filter($filters);
        }

        if(is_array($sort)){
            foreach ($sort as $field => $type) {
                $query->orderBy($field, $type);
            }
        } else {
            $query->latest($sort);
        }

        return $query->get();
    }

    public function countBy(array $payload = []): int
    {
        $query = $this->eloquentBuilder();

        foreach ($payload as $field => $value) {
            $query->where($field, $value);
        }

        return $query->count();
    }

    public function count(): int
    {
        return $this->eloquentBuilder()->count();
    }

    public function existBy(
        array $payload = [],
        array $payloadWithout = []
    ): bool
    {
        $query = $this->eloquentBuilder();

        foreach ($payload as $field => $value) {
            $query->where($field, $value);
        }
        foreach ($payloadWithout as $field => $value) {
            $query->whereNot($field, $value);
        }

        return $query->exists();
    }

    public function getPerPage($filters): int
    {
        if(isset($filters['per_page'])){
            return $filters['per_page'];
        }

        return BaseModel::DEFAULT_PER_PAGE;
    }

    public function getPage($filters): int
    {
        if(isset($filters['page'])){
            return $filters['page'];
        }

        return 1;
    }

    private function checkFilterTrait(): bool
    {
        return array_key_exists(Filterable::class, class_uses($this->modelClass()));
    }
}

