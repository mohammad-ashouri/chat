tinymce.PluginManager.add('advtable', function (editor, url) {
    // Add a button to the toolbar
    editor.ui.registry.addButton('advtable', {
        text: 'Advanced Table',
        onAction: function () {
            // Create a dialog for advanced table editing
            editor.windowManager.open({
                title: 'Advanced Table Editor',
                body: {
                    type: 'panel',
                    items: [
                        {
                            type: 'input',
                            name: 'rows',
                            label: 'Rows',
                            value: '3'
                        },
                        {
                            type: 'input',
                            name: 'cols',
                            label: 'Columns',
                            value: '3'
                        },
                        {
                            type: 'checkbox',
                            name: 'header',
                            label: 'Header Row',
                            checked: true
                        },
                        {
                            type: 'checkbox',
                            name: 'footer',
                            label: 'Footer Row',
                            checked: false
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
                    var rows = parseInt(data.rows) || 3;
                    var cols = parseInt(data.cols) || 3;
                    var header = data.header;
                    var footer = data.footer;

                    // Create table HTML
                    var table = '<table class="mce-item-table" border="1">';

                    // Add header row if needed
                    if (header) {
                        table += '<thead><tr>';
                        for (var i = 0; i < cols; i++) {
                            table += '<th>Header ' + (i + 1) + '</th>';
                        }
                        table += '</tr></thead>';
                    }

                    // Add body
                    table += '<tbody>';
                    for (var i = 0; i < rows; i++) {
                        table += '<tr>';
                        for (var j = 0; j < cols; j++) {
                            table += '<td>Cell ' + (i + 1) + ',' + (j + 1) + '</td>';
                        }
                        table += '</tr>';
                    }
                    table += '</tbody>';

                    // Add footer row if needed
                    if (footer) {
                        table += '<tfoot><tr>';
                        for (var i = 0; i < cols; i++) {
                            table += '<th>Footer ' + (i + 1) + '</th>';
                        }
                        table += '</tr></tfoot>';
                    }

                    table += '</table>';

                    // Insert the table
                    editor.insertContent(table);
                    api.close();
                }
            });
        }
    });

    // Add a menu item
    editor.ui.registry.addMenuItem('advtable', {
        text: 'Advanced Table',
        onAction: function () {
            editor.execCommand('mceAdvTable');
        }
    });

    // Add a command
    editor.addCommand('mceAdvTable', function () {
        editor.execCommand('mceInsertTable');
    });

    return {
        getMetadata: function () {
            return {
                name: 'Advanced Table',
                url: 'https://www.tiny.cloud/docs/plugins/advtable/'
            };
        }
    };
});
