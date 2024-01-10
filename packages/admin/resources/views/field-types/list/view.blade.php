<div
  x-data="{
    items: @entangle($field['signature'].(isset($language) ? '.' . $language : null)),
    newItem: '',
    addItem(item) {
      item = item || this.newItem;

      if(!item)
          return;

      this.items.push(item)
      this.items = JSON.parse(
        JSON.stringify(this.items)
      )
      this.newItem = ''
    },
    deleteItem(index) {
        this.items = this.items.filter((item, itemIndex) => {
            return index !== itemIndex
        })
    },
    hasItem(item) {
        if(this.items.indexOf(item) > -1)
            return false;

        return true;
    },
    isFull() {
        return this.items.length != '{{ count($field['configuration']['lookups']) }}';
    },
  }"
  x-init="
    items = Array.isArray(items) ? items : []

    getKey = () => {
      return btoa(Math.random().toString()).substr(10, 5)
    }

     new Sortable($refs.list, {
      animation: 150,
      handle: '.handle',
      onSort: ({ newIndex, oldIndex }) => {

        list = JSON.parse(
          JSON.stringify(items)
        )

        const moved = list[oldIndex]
        const node = list[newIndex]

        list.splice(oldIndex, 1)
        list.splice(newIndex, 0, moved)

        items = list
      }
    });
  ">
  <ul class="border divide-y rounded" x-ref="list">
    <template x-for="(item, index) in items" :key="getKey()">
        <li class="flex px-3 py-2 text-sm bg-white">
          <x-hub::icon ref="selector" style="solid" class="mr-2 text-gray-400 hover:text-gray-700 handle cursor-grab" />
          <div class="flex justify-between grow">
            <span x-text="item"></span>
            <button
              type="button"
              class="text-gray-500 hover:text-red-500"
              x-on:click.debounce.100ms="deleteItem(index)"
              wire:loading.attr="disabled"
            >
              <x-hub::icon ref="x" style="solid" class="w-3" />
            </button>
          </div>
        </li>
    </template>
  </ul>

  <div class="mt-2" x-show="isFull()">
    {{--<x-hub::input.text x-model="newItem" placeholder="Type item and press enter" @keydown.enter="addItem()" />--}}
    <x-hub::input.autocomplete x-model="newItem" @keydown.enter="addItem()">
        @foreach($field['configuration']['lookups'] as $lookup)
            <li
                x-show="hasItem('{{ $lookup['value'] }}')"
                class="px-2 py-1 cursor-pointer"
                @click="addItem('{{ $lookup['value'] }}')"
            >
                {{ $lookup['value'] }}
            </li>
        @endforeach
    </x-hub::input.autocomplete>
  </div>
</div>
