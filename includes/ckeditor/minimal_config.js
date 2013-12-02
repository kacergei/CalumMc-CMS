CKEDITOR.editorConfig = function( config ) {
    config.toolbarGroups = [
        { name: 'links' },
        { name: 'insert' },
        { name: 'others' },
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
        { name: 'styles' },
        { name: 'colors' }
    ];
};
