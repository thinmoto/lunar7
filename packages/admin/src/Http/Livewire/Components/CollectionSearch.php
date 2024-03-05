<?php

namespace Lunar\Hub\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Facades\DB;
use Lunar\Models\Collection as ModelsCollection;

class CollectionSearch extends Component
{
    /**
     * Should the browser be visible?
     */
    public bool $showBrowser = false;

    /**
     * The search term.
     *
     * @var string
     */
    public $searchTerm = null;

    /**
     * Max results we want to show.
     *
     * @var int
     */
    public $maxResults = 50;

    /**
     * Any existing collections to exclude from selecting.
     */
    public Collection $existing;

    /**
     * The currently selected collections.
     */
    public array $selected = [];

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            'searchTerm' => 'required|string|max:255',
        ];
    }

    /**
     * Return the selected collections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSelectedModelsProperty()
    {
        return ModelsCollection::whereIn('id', $this->selected)->get();
    }

    /**
     * Return the existing collection ids.
     *
     * @return array
     */
    public function getExistingIdsProperty()
    {
        return $this->existing->pluck('id');
    }

    /**
     * Listener for when show browser is updated.
     *
     * @return void
     */
    public function updatedShowBrowser()
    {
        $this->selected = [];
        $this->searchTerm = null;
    }

    /**
     * Add the collection to the selected array.
     *
     * @param  string|int  $id
     * @return void
     */
    public function selectCollection($id)
    {
        $this->selected[] = $id;
    }

    /**
     * Remove a collection from the selected collections.
     *
     * @param  string|int  $id
     * @return void
     */
    public function removeCollection($id)
    {
        $index = collect($this->selected)->search($id);
        unset($this->selected[$index]);
    }

    /**
     * Returns the computed search results.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getResultsProperty()
    {
        if (! $this->searchTerm) {
            return ModelsCollection::query()->with(['group'])->orderBy(DB::raw('IFNULL(parent_id, 0) * 100 + id * IFNULL(parent_id, 100)'))->paginate($this->maxResults);
        }

        return ModelsCollection::search($this->searchTerm)
            ->query(function (Builder $query) {
                $query->with([
                    'group',
                ]);
            })->paginate($this->maxResults);
    }

    public function triggerSelect()
    {
        $this->emit('collectionSearch.selected', $this->selected);

        $this->showBrowser = false;
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.collection-search')
            ->layout('adminhub::layouts.base');
    }
}
