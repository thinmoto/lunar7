<div
        x-ref="input"
        x-data="{
        value: @entangle($attributes->wire('model')),
        init() {
          let options = {{ json_encode($options) }};

          const fullToolbarOptions = [
                  [{ header: [1, 2, 3, false] }],
                  ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                ['image']
          ];

          options.modules = {
              toolbar: {
                  container: fullToolbarOptions
              },
              imageUploader: {
                  upload: (file) => {
                      return new Promise((resolve, reject) => {
                          const formData = new FormData();
                          formData.append('image', file);
                          formData.append('_token', '{{ csrf_token()}}');

                          fetch(
                            '{{ $upload }}',
                            {
                                method: 'POST',
                                body: formData
                            }
                          )
                            .then((response) => response.json())
                            .then((result) => {
                            console.log(result);
                            resolve(result.data.url);
                            })
                            .catch((error) => {
                            reject('Upload failed');
                            console.error('Error:', error);
                            });
                      });
                  }
              }
          };

          {{ $instanceId }} = new Quill($refs.editor, options)

          {{ $instanceId }}.on('text-change', () => {
            $dispatch('quill-input', {{ $instanceId }}.root.innerHTML)
          })
        }
    }"
        x-on:quill-input="value = $event.detail"
        wire:ignore
>
    <div>
        <div x-ref="editor">{!! $initialValue !!}</div>
    </div>
</div>
