Component.register({

    // Create component
    // ----------------
    'type' : 'content-block',
    'id' : 'widget',
    'title' : 'Вывод пользовательского виджета',
    'inherit' : [
        ['component', 'content-block']
    ],
    'constructor' : function(args) {

        // Parent constructor
        // ------------------
        this.parentConstructor.call(this, args);

        // Data
        // ----
        this.type = 'widget';
        this.args = {};
        this.options = {};

        // Set values
        // ----------
        if (this.data != null) {
            this.widgetType = this.data[0];
            this.args = this.data[1];
            this.options = this.data[2];
        }
    },

    // Edit mode
    // ---------
    'renderEdit' : function() {

        var block = this;

        $(block.contentWidget).empty();

        if (this.widgetType != null) {
            var widgetType =  this.widgetType;
        } else if (this.data != null) {
            var widgetType =  this.data[0];
        } else {
            var widgetType = 'Новый виджет';
        }

        $(block.contentWidget).append('<div class="title">'+widgetType+'</div>');

        // Add edit button
        // ---------------
        var editButton = new FlexButton({'title' : 'Настройки виджета', 'click' : function() {
            block.openWidgetEditor();
        }});

        $(block.contentWidget).append(editButton.widget);
    },

    // Open editor of widget properties
    // --------------------------------
    'openWidgetEditor' : function() {

        var block = this;

        // Request whole render
        // --------------------
        API.action({
            'action' : '/module/content/getWidgetEditor',
            'data' : {
                'type' : block.widgetType
            },
            'callback' : function(result) {

                if (result != null) {

                    // Window
                    // ------
                    var editorWindow = new Flex.Window({'title' : 'Изменение параметров видежта', 'width' : 800,  'class' : ['adminTools']});

                    // Create editor
                    // -------------
                    var editor = new CoreEditor({'elements': result.structure, 'jsController': 'WidgetEditorController', 'properties' : result.properties, 'data' : {
                        'type' : block.widgetType,
                        'args' : block.args,
                        'options': block.options
                    }});

                    // Add save button
                    // ---------------
                    editorWindow.windowToolbar.addButton({
                        'title' : 'Сохранить',
                        'click' : function() {

                            var data = editor.getValue();

                            // Set data to block
                            // -----------------
                            block.widgetType = data['type'];
                            block.args = data['args'];
                            block.options = data['options'];

                            // Close window
                            // ------------
                            editorWindow.close();

                            block.renderEdit();
                        }
                    });

                    // Append editor to window
                    // -----------------------
                    $(editorWindow.widget).append(editor.widget);
                }
            }
        });


    },

    // Value
    // -----
    'setValue' : function(value) {
        this.data = value;
    },

    // Get value
    // ---------
    'getValue' : function() {
        this.data = [this.widgetType, this.args, this.options];
        return this.parent.getValue.call(this);
    }


});

