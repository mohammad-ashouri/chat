tinymce.PluginManager.add('template', function (editor, url) {
    // Define some default templates
    var templates = [
        {
            title: 'New Table',
            description: 'Creates a new table',
            content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
        },
        {
            title: 'Header',
            description: 'Adds a header section',
            content: '<div class="mceTmpl"><h1>Header</h1><p>Your content here</p></div>'
        },
        {
            title: 'Two Columns',
            description: 'Creates a two column layout',
            content: '<div class="mceTmpl"><div style="display: flex;"><div style="flex: 1; padding: 10px;">Column 1</div><div style="flex: 1; padding: 10px;">Column 2</div></div></div>'
        }
    ];

    // Add a button to the toolbar
    editor.ui.registry.addButton('template', {
        text: 'Templates',
        onAction: function () {
            // Create a dialog for template selection
            editor.windowManager.open({
                title: 'Insert Template',
                body: {
                    type: 'panel',
                    items: [
                        {
                            type: 'selectbox',
                            name: 'template',
                            label: 'Select Template',
                            items: templates.map(function (template, index) {
                                return {
                                    text: template.title,
                                    value: index
                                };
                            })
                        }
                    ]
                },
                buttons: [
                    {
                        type: 'cancel',
                        text: 'Cancel'
                    },
                    {
                        type: 'submit',
                        text: 'Insert',
                        primary: true
                    }
                ],
                onSubmit: function (api) {
                    var data = api.getData();
                    var templateIndex = parseInt(data.template);
                    var template = templates[templateIndex];

                    if (template) {
                        editor.insertContent(template.content);
                    }

                    api.close();
                }
            });
        }
    });

    // Add a menu item
    editor.ui.registry.addMenuItem('template', {
        text: 'Insert Template',
        onAction: function () {
            editor.execCommand('mceTemplate');
        }
    });

    // Add a command
    editor.addCommand('mceTemplate', function () {
        editor.execCommand('mceInsertTemplate');
    });

    return {
        getMetadata: function () {
            return {
                name: 'Template',
                url: 'https://www.tiny.cloud/docs/plugins/template/'
            };
        }
    };
});
