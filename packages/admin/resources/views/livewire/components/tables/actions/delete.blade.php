<div class="p-4 space-y-2">
    <div class="flex justify-between">
        <x-hub::button type="button" theme="gray" wire:click="$emit('bulkAction.complete')">Відмінити</x-hub::button>

        <x-hub::button type="button" theme="danger" wire:click="updateDelete">Видалити</x-hub::button>
    </div>
</div>
