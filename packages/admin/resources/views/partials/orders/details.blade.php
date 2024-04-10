<dl class="text-sm text-gray-600">
    <div
            x-data="{
      copy(text) {
        if (window.clipboardData && window.clipboardData.setData) {
            $wire.call('notify', '{{ __('adminhub::notifications.clipboard.copied') }}')
            // Internet Explorer-specific code path to prevent textarea being shown while dialog is visible.
            return window.clipboardData.setData('Text', text);
        } else if (document.queryCommandSupported && document.queryCommandSupported('copy')) {
            var textarea = document.createElement('textarea');
            textarea.textContent = text;
            textarea.style.position = 'fixed';  // Prevent scrolling to bottom of page in Microsoft Edge.
            document.body.appendChild(textarea);
            textarea.select();
            try {
              $wire.call('notify', '{{ __('adminhub::notifications.clipboard.copied') }}')
              return document.execCommand('copy');  // Security exception may be thrown by some browsers.
            }
            catch (ex) {
              $wire.call('notify', '{{ __('adminhub::notifications.clipboard.failed_copy') }}')
            }
            finally {
                document.body.removeChild(textarea);
            }
        }
      }
    }">

        <div class="grid grid-cols-2 gap-2 px-4 py-3 border-b">
            <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.reference') }}</dt>
            <dd class="text-right">
                <div class="flex items-center justify-end space-x-4">
                    <div>
                        {{ $order->id }}
                    </div>
                    <button type="button" x-on:click="copy('{{ $order->id }}')">
                        <x-hub::icon ref="clipboard" class="w-4"/>
                    </button>
                </div>
            </dd>
        </div>
    </div>

    @if(!$order->placed_at)
        <div class="grid grid-cols-2 gap-2 px-4 py-3 border-b">
            <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.date_created') }}</dt>
            <dd class="text-right">{{ $order->created_at->format('d.m.Y H:i') }}</dd>
        </div>
    @else
        <div class="grid grid-cols-2 gap-2 px-4 py-3 border-b">
            <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.date_created') }}</dt>
            <dd class="text-right">
                @if($order->placed_at)
                    {{ $order->placed_at->format('d.m.Y H:i') }}
                @else
                    -
                @endif
            </dd>
        </div>
    @endif

    @if ($order->customer)
        <div class="grid items-center grid-cols-2 gap-2 px-4 py-3 border-b">
            <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.contactor') }}</dt>
            <dd class="text-right">
                <div class="flex justify-end gap-4">
                    {{ $order->customer->first_name }}

                    @if ($order->customer->last_name)
                        {{ $order->customer->last_name }}
                    @endif

                    @if (isset($order->meta['sur_name']))
                        {{ $order->meta['sur_name'] }}
                    @endif

                    <a class=""
                       href="{{ route('hub.customers.show', $order->customer) }}">

                        <svg class="w-5 h-5" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4.35418C12.7329 3.52375 13.8053 3 15 3C17.2091 3 19 4.79086 19 7C19 9.20914 17.2091 11 15 11C13.8053 11 12.7329 10.4762 12 9.64582M15 21H3V20C3 16.6863 5.68629 14 9 14C12.3137 14 15 16.6863 15 20V21ZM15 21H21V20C21 16.6863 18.3137 14 15 14C13.9071 14 12.8825 14.2922 12 14.8027M13 7C13 9.20914 11.2091 11 9 11C6.79086 11 5 9.20914 5 7C5 4.79086 6.79086 3 9 3C11.2091 3 13 4.79086 13 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </a>
                </div>
            </dd>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-2 px-4 py-3 border-b">
        <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.phone') }}</dt>
        <dd class="text-right">
            @if($order->shippingAddress->contact_phone)
                <a href="tel:{{ $order->shippingAddress->contact_phone }}" class="text-sky-500 underline">{{ $order->shippingAddress->contact_phone }}</a>
            @else
                <span class="text-xs text-gray-500">{{ __('adminhub::global.not_provided') }}</span>
            @endif
        </dd>
    </div>

    <livewidele

    <div class="grid grid-cols-2 gap-2 px-4 py-3 border-b">
        <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.email') }}</dt>
        <dd class="text-right">
            @if($order->shippingAddress->contact_email)
                <a href="mailto:{{ $order->shippingAddress->contact_email }}" class="text-sky-500 underline">{{ $order->shippingAddress->contact_email }}</a>
            @else
                <span class="text-xs text-gray-500">{{ __('adminhub::global.not_provided') }}</span>
            @endif
        </dd>
    </div>

    <div class="grid items-center grid-cols-2 gap-2 px-4 py-3 border-b">
        <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.status') }}</dt>
        <dd class="text-right">
            <x-hub::orders.status :status="$order->status"/>
        </dd>
    </div>

    <div class="grid items-center grid-cols-2 gap-2 px-4 py-3 border-b">
        <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.dont_call') }}</dt>
        <dd class="text-right">
            @if (isset($order->meta['dont_call']) && $order->meta['dont_call'])
                {{ __('adminhub::partials.orders.details.yes') }}
            @else
                -
            @endif
        </dd>
    </div>

    <div class="grid items-center grid-cols-2 gap-2 px-4 py-3 border-b">
        <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.notes') }}</dt>
        <dd class="text-right">
            @if (isset($order->meta['notes']) && $order->meta['notes'])
                {{ $order->meta['notes'] }}
            @else
                -
            @endif
        </dd>
    </div>

    {{--<div class="grid grid-cols-2 gap-2 px-4 py-3 border-b">
        <dt class="font-medium text-gray-500">{{ __('adminhub::partials.orders.details.channel') }}</dt>
        <dd class="text-right">{{ $order->channel->name }}</dd>
    </div>--}}
</dl>
