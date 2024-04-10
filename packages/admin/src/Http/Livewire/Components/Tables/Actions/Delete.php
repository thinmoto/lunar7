<?php

namespace Lunar\Hub\Http\Livewire\Components\Tables\Actions;

use Livewire\Component;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Models\Order;

class Delete extends Component
{
    use Notifies;

    /**
     * The array of selected IDs
     */
    public array $ids = [];

    public $status = null;

    /**
     * {@inheritDoc}
     */
    public function getListeners()
    {
        return [
            'table.selectedRows' => 'setSelected',
        ];
    }

    /**
     * Set the selected ids
     *
     * @return void
     */
    public function setSelected(array $rows)
    {
        $this->ids = $rows;
    }

    /**
     * Save the updated status
     */
    public function updateDelete()
    {
        Order::whereIn('id', $this->ids)->update([
            'status' => 'deleted',
        ]);
        $this->emit('bulkAction.complete');
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.tables.actions.delete')
            ->layout('adminhub::layouts.base');
    }
}
