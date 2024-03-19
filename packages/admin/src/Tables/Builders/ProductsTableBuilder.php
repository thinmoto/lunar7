<?php

namespace Lunar\Hub\Tables\Builders;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Lunar\Hub\Tables\TableBuilder;
use Lunar\Models\Product;

class ProductsTableBuilder extends TableBuilder
{
    /**
     * Return the query data.
     *
     * @param  string|null  $searchTerm
     * @param  array  $filters
     * @param  string  $sortField
     * @param  string  $sortDir
     * @return LengthAwarePaginator
     */
    public function getData(): iterable
    {
        $query = Product::orderBy($this->sortField, $this->sortDir)
            ->withTrashed();

        if ($this->searchTerm) {
			$query->where(function(Builder $query){
				$query
					->whereRaw('LOWER(attribute_data) LIKE "%'..'%"')
					->orWhereRaw('LOWER(attribute_data) LIKE "%'.json_encode(strtolower($this->searchTerm), JSON_UNESCAPED_UNICODE).'%"');
			});

            /*$query->whereIn('id', Product::search($this->searchTerm)
                ->query(fn ($query) => $query->select('id'))
                ->take(500)
                ->keys());*/
        }

        $filters = collect($this->queryStringFilters)->filter(function ($value) {
            return (bool) $value;
        });

        foreach ($this->queryExtenders as $qe) {
            call_user_func($qe, $query, $this->searchTerm, $filters);
        }

        // Get the table filters we want to apply.
        $tableFilters = $this->getFilters()->filter(function ($filter) use ($filters) {
            return $filters->has($filter->field);
        });

        foreach ($tableFilters as $filter) {
            call_user_func($filter->getQuery(), $filters, $query);
        }

        return $query->paginate($this->perPage);
    }
}
