<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait ApiResponser
{
    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection], $code);
        }

        $transformer = $collection->first()->transformer;

        $collection = $this->filterData($collection, $transformer);
        $collection = $this->sortData($collection, $transformer);
        $collection = $this->paginate($collection);
        $collection = $this->transformData($collection, $transformer);

        return $this->successResponse($collection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {
        $transformer = $instance->transformer;

        $instance = $this->transformData($instance, $transformer);

        return $this->successResponse($instance, $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function filterData(Collection $collection, $transformer)
    {
        foreach (request()?->query() as $query => $value) {
            $attribute = $transformer::originalAttribute($query);

            if (isset($attribute, $value)) {
//                return $collection->where($attribute, $value);
                return $collection->filter(function ($item) use ($attribute, $value) {
                    return Str::contains($item[$attribute], $value);
                });
                // TODO : db 필터링으로 변경 (대량 데이터일 경우 db에서 필터링하는 것이 성능상 유리) 및 검색기준 추가 / 검색엔진 or caching 구현
            }
        }

        return $collection;
    }

    protected function sortData(Collection $collection, $transformer)
    {
        if (request()?->has('sort_by')) {
            $attribute = request()?->get('sort_by');

            if (Str::startsWith($attribute, '-')) {
                $attribute = $transformer::originalAttribute(ltrim($attribute, '-'));
                $collection = $collection->sortByDesc($attribute);
            } else {
                $attribute = $transformer::originalAttribute(request()->sort_by);
                $collection = $collection->sortBy($attribute);
            }

        }

        return $collection;
    }

    protected function paginate(Collection $collection)
    {

        $rules = [
            'per_page' => 'integer|min:2|max:50',
        ];

        Validator::validate(request()?->all(), $rules);

        // 쿼리 파라미터에서 페이지정보 가져오기
        $perPage = max(1, (int) request('per_page', 15));
        $page = max(1, LengthAwarePaginator::resolveCurrentPage());

        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()?->all());

        return $paginated;

    }

    protected function transformData($data, $transformer)
    {
        if (!isset($transformer)) {
            throw new \RuntimeException('Transformer not defined');
        }
        return fractal($data, new $transformer)->toArray() ?? [];
    }
}
