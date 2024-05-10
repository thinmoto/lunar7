<div>
    @livewire('hub.components.fieldtypes.file', [
    'maxFiles' => $field['configuration']['max_files'] ?? 1,
    'signature' => $field['signature'],
    'selected' => $field['data'] ?? [],
    'title' => $field['name']
    ], key($field['signature']))
</div>
