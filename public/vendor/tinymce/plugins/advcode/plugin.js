tinymce.PluginManager.add('advcode', function (editor, url) {
    // Add a button to the toolbar
    editor.ui.registry.addButton('advcode', {
        text: 'Advanced Code',
        onAction: function () {
            // Get the selected content
            var content = editor.selection.getContent();

            // Create a dialog for advanced code editing
            editor.windowManager.open({
                title: 'Advanced Code Editor',
                body: {
                    type: 'panel',
                    items: [{
                        type: 'textarea',
                        name: 'code',
                        label: 'Code',
                        value: content
                    }]
                },
                buttons: [
                    {
                        type: 'cancel',
                        text: 'Cancel'
                    },
                    {
                        type: 'submit',
                        text: 'Save',
                        primary: true
                    }
                ],
                onSubmit: function (api) {
                    var data = api.getData();
                    editor.selection.setContent(data.code);
                    api.close();
                }
            });
        }
    });

    // Add a menu item
    editor.ui.registry.addMenuItem('advcode', {
        text: 'Advanced Code',
        onAction: function () {
            editor.execCommand('mceAdvCode');
        }
    });

    // Add a command
    editor.addCommand('mceAdvCode', function () {
        editor.execCommand('mceCodeEditor');
    });

    return {
        getMetadata: function () {
            return {
                name: 'Advanced Code',
                url: 'https://www.tiny.cloud/docs/plugins/advcode/'
            };
        }
    };
});
