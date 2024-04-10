<?php

namespace Lunar\Hub\Tables\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Lunar\Hub\Tables\TableBuilder;
use Lunar\LivewireTables\Components\Columns\TagsColumn;
use Lunar\LivewireTables\Components\Columns\TextColumn;
use Lunar\Models\Order;
use Spatie\Activitylog\Models\Activity;

class OrdersTableBuilder extends TableBuilder
{
    /**
     * The field to sort using.
     */
    public ?string $sortField = 'id';
    public ?string $sortDir = 'desc';

    /**
     * {@inheritDoc}
     */
    public function getColumns(): Collection
    {
        $baseColumns = collect([
	        TextColumn::make('id')->sortable(true)->heading('id')->value(function ($record) {
		        return $record->id;
	        })->url(function ($record) {
		        return route('hub.orders.show', $record->id);
	        }),

	        TextColumn::make('date')->value(function ($record) {
		        return $record->created_at?->format('d.m.Y H:i');
	        }),

            TextColumn::make('status')->sortable(true)->viewComponent('hub::orders.status'),

	        TextColumn::make('total')->value(function ($record) {
		        return $record->total->formatted;
	        }),

	        TextColumn::make('email')->value(function ($record) {
		        return $record->billingAddress?->contact_email;
	        }),

	        TextColumn::make('customer')->value(function ($record) {
		        return $record->billingAddress?->fullName;
	        }),

	        TextColumn::make('phone')->value(function ($record) {
		        return $record->billingAddress?->contact_phone;
	        }),

	        TextColumn::make('shipping')->value(function ($record) {
		        return isset($record->meta['shipping']) ? __('app.shipping_title.'.$record->meta['shipping']) : '-';
	        }),


	        TextColumn::make('payment')->value(function ($record) {
		        return isset($record->meta['payment']) ? __('app.payment_title.'.$record->meta['payment']) : '-';
	        }),

	        TextColumn::make('notes')->value(function ($record) {
		        return isset($record->meta['notes']) ? Str::words($record->meta['notes'], 5) : '';
	        }),

	        TextColumn::make('manager_notes')->value(function ($record) {
		        $comment = Activity::query()
			        ->where('subject_type', 'Lunar\\Models\\Order')
			        ->where('subject_id', $record->id)
			        ->where('event', 'comment')
			        ->orderByDesc('id')
			        ->first();

		        return $comment ? $comment->properties->get('content') : '';
	        }),

	        TextColumn::make('dont_call')->value(function ($record) {
		        return isset($record->meta['dont_call']) ? __('adminhub::orders.index.dont_call') : '';
	        }),

            // TextColumn::make('customer_reference')->heading('Customer Reference')->value(function ($record) {
            //     return $record->customer_reference;
            // }),

            // TextColumn::make('postcode')->value(function ($record) {
            //     return $record->billingAddress?->postcode;
            // }),

            // TagsColumn::make('tags')->value(function ($record) {
            //     return $record->tags->pluck('value');
            // }),
        ]);

        return $this->resolveColumnPositions(
            $baseColumns,
            $this->columns
        );
    }

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
        $query = Order::with([
            'shippingLines',
            'billingAddress',
            'currency',
            'customer',
            'tags',
        ])->orderBy($this->sortField, $this->sortDir);

		$query->whereNot('status', 'deleted');

        if ($this->searchTerm)
		{
			// $query->where(function(Builder $query){
			// 	$query->where('id', 'like', $this->searchTerm.'%')
			// 		->orWhereRaw('LOWER(shipping_breakdown) LIKE "%'.strtolower($this->searchTerm).'%"')
			// 		->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(shipping_breakdown, "$.*.name.uk")) LIKE "%'.strtolower($this->searchTerm).'%"');
			// });

            $query->whereIn('id', Order::search($this->searchTerm)
                ->query(fn ($query) => $query->select('id'))
                ->take(200)
                ->keys());
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
            if ($closure = $filter->getQuery()) {
                call_user_func($filter->getQuery(), $filters, $query);
            }
        }

        return $query->paginate($this->perPage);
    }
}
