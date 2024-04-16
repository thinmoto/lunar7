<?php

namespace Lunar\Hub\Http\Livewire\Components;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Lunar\Models\Product;

class ProductSearch extends Component
{
    public bool $showBrowser = false;

    public $searchTerm = null;

    public $maxResults = 50;

    public $existing;

    public $selected = [];

    public $exclude = [];

    public $ref = null;

    public $showBtn = true;

    protected $listeners = [
        'updatedExistingProductAssociations',
        'showBrowser',
    ];

    public function rules()
    {
        return [
            'searchTerm' => 'required|string|max:255',
        ];
    }

    public function getSelectedModelsProperty()
    {
        return Product::whereIn('id', $this->selected)->withTrashed()->get();
    }

    public function getExistingIdsProperty()
    {
        return $this->existing->pluck('id');
    }

    public function updatedShowBrowser()
    {
        $this->selected = [];
        $this->searchTerm = null;
    }

    public function showBrowser($reference = null)
    {
        if ($reference && $reference == $this->ref) {
            $this->showBrowser = true;
            $this->selected = [];
            $this->searchTerm = null;
        }
    }

    public function selectProduct($id)
    {
        $this->selected[] = $id;
    }

    public function removeProduct($id)
    {
        $index = collect($this->selected)->search($id);
        unset($this->selected[$index]);
        $this->selected = collect($this->selected)->values();
    }

    public function updatedExistingProductAssociations($selected)
    {
        $this->existing = collect($selected);
    }

    /**
     * Returns the computed search results.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getResultsProperty()
    {
        if (! $this->searchTerm) {
            return null;
        }

	    $query = Product::query();

	    $query->where(function($query){
		    $query
			    ->whereRaw('LOWER(attribute_data) LIKE "%'.strtolower(addslashes($this->searchTerm)).'%"')
			    ->orWhereRaw('JSON_UNQUOTE(JSON_EXTRACT(attribute_data, "$.name.value.uk")) LIKE "%'.strtolower(addslashes($this->searchTerm)).'%"');
	    });

        //return Product::search($this->searchTerm)->paginate($this->maxResults);
        //return Product::query()->whereRaw('LOWER(attribute_data) LIKE "%'.strtolower($this->searchTerm).'%"')->paginate($this->maxResults);
	    return $query->paginate($this->maxResults);
    }

    public function triggerSelect()
    {
        $this->emit('productSearch.selected', $this->selected, $this->ref);
        $this->showBrowser = false;
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.product-search')
            ->layout('adminhub::layouts.base');
    }
}
