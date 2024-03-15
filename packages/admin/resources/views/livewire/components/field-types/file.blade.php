<div class="space-y-4">
    <div class="flex justify-end">
        <x-hub::button wire:click="$set('showUploader', true)">
            {{ __('adminhub::fieldtypes.file.choose_assets') }}
        </x-hub::button>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-3">
        @foreach ($this->selectedModels as $index => $assetModel)
            <div class="flex items-center justify-between gap-4 p-2 border rounded"
                 wire:key="initial_asset_{{ $assetModel->id }}">
                <div class="flex items-center gap-4 overflow-hidden">
                    <div class="w-12 h-12 p-2 border rounded shrink-0">
                        @if ($assetModel->file->hasGeneratedConversion('small'))
                            <a href="{{ $assetModel->file->getUrl('large') }}"
                               target="_blank"
                               class="block">
                                <img src="{{ $assetModel->file->getUrl('small') }}"
                                     class="object-contain w-8 h-8 mx-auto">
                            </a>
                        @else
                            <x-hub::icon ref="document"
                                         class="w-8 h-8 mx-auto text-gray-400" />
                        @endif
                    </div>

                    <p class="text-xs truncate">
                      {{ $assetModel->file->file_name }}
                    </p>
                </div>

                <button type="button"
                        wire:click="removeSelected({{ $assetModel->id }})"
                        class="p-0.5 rounded hover:bg-gray-100 shrink-0">
                    <x-hub::icon ref="x"
                                 class="w-4 h-4" />
                </button>
            </div>
        @endforeach
    </div>

    <x-hub::modal.dialog wire:model="showUploader">
        <x-slot name="title">
            <strong>
                {{ __('adminhub::fieldtypes.file.select_files') }}
            </strong>
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 gap-2 lg:grid-cols-1">
                <label class="p-1 mb-4 border border-dashed rounded-md cursor-pointer hover:border-sky-500">
                    <div class="flex items-center">
                        <x-hub::icon ref="upload"
                                     class="w-6 h-6 mx-auto text-gray-400" />

                        <p class="ml-2 text-xs font-medium text-center text-gray-600 mr-2">
                            {{ __('adminhub::fieldtypes.file.label') }}
                        </p>
                    </div>

                    <input type="file"
                           wire:model="file"
                           class="hidden" />
                </label>

                <div class="mb-4">
                    <input class="form-input block w-full border-gray-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500 sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed" type="text" wire:model.debounce.300ms="searchTerm" maxlength="255" placeholder="{{ __('adminhub::orders.index.search_placeholder') }}">
                </div>

                @forelse($this->assets as $asset)
                    <label @class([
                        'border rounded-md cursor-pointer p-1 hover:border-sky-500',
                        'border-sky-500' => in_array($asset->id, $selected),
                    ])>
                        <div class="flex items-center" style="float: left">
                            @if ($asset->file->hasGeneratedConversion('small'))
                                <img src="{{ $asset->file->getUrl('small') }}"
                                     class="object-contain w-6 h-6 mx-auto" style="float: left">
                            @else
                                <x-hub::icon ref="document"
                                             class="w-6 h-6 mx-auto text-gray-400" />
                            @endif

                            <p class="ml-2 text-xs font-medium text-center text-gray-600 truncate">
                                {{ $asset->file->file_name }}
                            </p>
                        </div>

                        <input wire:model="selected"
                               type="checkbox"
                               class="hidden"
                               value="{{ $asset->id }}" />
                    </label>
                @empty
                    {{ __('adminhub::fieldtypes.file.uploads_empty') }}
                @endforelse




            </div>

            <div class="mt-4">
                {{ $this->assets->onEachSide(2)->links() }}
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end space-x-4">
                <x-hub::button type="button"
                               theme="gray"
                               wire:click="$set('showUploader', false)">
                    {{ __('adminhub::global.cancel') }}
                </x-hub::button>

                <x-hub::button type="button"
                               :disabled="!count($selected)"
                               wire:click.prevent="process">
                    {{ __('adminhub::fieldtypes.file.select_files') }}
                </x-hub::button>
            </div>
        </x-slot>
    </x-hub::modal.dialog>
</div>
