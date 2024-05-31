<div
        x-ref="input"
        x-data="{
        value: @entangle($attributes->wire('model')),
        init() {
          let options = {{ json_encode($options) }};

          const fullToolbarOptions = [
                  [{ header: [1, 2, 3, false] }],
                  ['bold', 'italic', 'underline'], ['link'], [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],[{ 'indent': '-1'}, { 'indent': '+1' }],
                ['image', 'alt'],['clean']
          ];

          options.modules = {
              toolbar: {
                  container: fullToolbarOptions,
                  handlers: {
        'alt': function() {
        let quill = {{ $instanceId }};
          const range = quill.getSelection();
          if (range) {
            const [image] = quill.getLeaf(range.index);
            if (image instanceof ImageAlt) {
              const altText = prompt('Введіть текст тегу ALT:', image.domNode.getAttribute('alt'));
              if (altText !== null) {
                image.domNode.setAttribute('alt', altText);
              }
            }
          }
        }
      }
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

            const Image = Quill.import('formats/image');
            class ImageAlt extends Image {
              static create(value) {
                let node = super.create(value);
                if (typeof value === 'object') {
                  node.setAttribute('src', value.url);
                  if (value.alt) {
                    node.setAttribute('alt', value.alt);
                  }
                }
                return node;
              }

              static value(node) {
                return {
                  url: node.getAttribute('src'),
                  alt: node.getAttribute('alt'),
                };
              }
            }

            ImageAlt.blotName = 'image';
ImageAlt.tagName = 'IMG';
Quill.register(ImageAlt, true);

          const {{ $instanceId }} = new Quill($refs.editor, options)

          {{ $instanceId }}.on('text-change', () => {
            $dispatch('quill-input', {{ $instanceId }}.root.innerHTML)
          })
        }
    }"
        x-on:quill-input="value = $event.detail"
        wire:ignore
>
    <style>
        .ql-snow.ql-toolbar button.ql-alt
        {
            /*background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAABI0lEQVR42u3SAUeDURjF8TIgDYAISoJACmGEgABUqYqkTxAUgBRI6SOUECRQCYL0EZIAFpogkbZUrfXHwfW6njbTnfEcfui+514n1uHxeDyeNk4/5jANK7OYMb4X9M7frHfIIWoyYvQ+8G58P0WtThVE0403fKm438SgcSxllPTuWuZ8HtGs6MIWPvGEnDmosdzr/T7UlWt8owdnujzZqkED+MGl/l7U5eNWDdpUeSH4PVVQRj71oE4U8Yqu4PxEDyynHjSh4gHCTOn8KvWgIxWf8RB41HkVvakG5VHGC84j7vTIeqpBqyrtIpYxfb9NNehGpWGjUww6/zpoMPjvreyot5cZVMWFYajRQdsqbMDKqHol5IJBnJkKxqBm4vF4PB7PL3jNkwvFTA/SAAAAAElFTkSuQmCC) no-repeat center center;*/
            background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAA6ElEQVR42u3OJVRmQQBH8fUt6+4bNy8VMgntuLt7ouLa6Uii4O4RT7h7wWW4OHzuOvec33szk/6PZDKZTCZzgl5iCwL/oFgKBH5CsVYILcqgV74QOEGunoM8EXUDmxhReHOFXtWhH42Y1HOQYtOoh8G9xwESEAGB/9YcFIFTfMNnnCDfmoO60IKb2jGLx9YY9BdnCMVNsRBws8agHAisY+naBgQqrTFoHEOIVzCIVTy15CAXCPhDsUAIuFtyUDH28BqKvccRqhQGZSJCQYApBj3FMmqgrkbs4OXNIDXW1Q+SyWQymcwxOweCOGIeUsJsgAAAAABJRU5ErkJggg==) no-repeat center center;
            background-size: cover;
        }
    </style>
    <div>
        <div x-ref="editor">
            {!! $initialValue !!}
        </div>
    </div>
</div>
