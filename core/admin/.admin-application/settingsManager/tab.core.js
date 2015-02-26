// Core manager
// ------------
Component.register({

	// Create component
	// ----------------
	'type' : 'settings-tab',
	'id' : 'core',
	'title' : 'Проект',

	// Constructor
	// -----------
	'constructor' : function(args) {

		// This
		// ----
		var editor = this;

		// Create widget
		// -------------
		this.widget = $('<div></div>');

		// Save object
		// -----------
		API.action({
			'action' : '/module/apps/settingsManager/getCoreSettings',

            // Process callback
            // ----------------
			'callback' : function(result) {
				editor.settings = result;
				editor.init();
			}
		});

	},


	// Init tab
	// --------
	'init' : function() {

		var editor = this;

		// Add toolbar
		// -----------
		this.toolbar = new Flex.WindowToolbar({
			'elements' : [
				{
					'type' : 'buttons',
					'title' : 'Сохранить',
					'click' : function() {
						editor.save();
					}
				}
			]
		});

		$(this.widget).append(this.toolbar.widget);


		// Add options form
		// ----------------
		this.form = new UI.Form({
			'format' : {

				// Main properties
				// ---------------
				'id' : {'type' : 'text', 'title' : 'Идентификатор проекта'},
				'title' : {'type' : 'text', 'title' : 'Название проекта'},
				'key' : {'type' : 'text', 'title' : 'Уникальный ключ'},

				'language' : {'type' : 'select', 'title' : 'Основной язык', 'values' : {'ru' : 'Русский', 'en' : 'Английский'} },
				'applicationClass' : {'type' : 'component', 'title' : 'Вид приложения', 'componentType' : 'application'},
				'enableSuperAccess' : {'type' : 'boolean', 'title' : 'Включить доступ суперпользователя'},
				'encoding' : {'type' : 'select', 'title' : 'Основная кодировка', 'values' : {'utf-8' : 'UTF-8', 'win1251' : 'Windows-1251'}},
				'adminEmail' : {'type' : 'text', 'title' : 'Email администратора'},

				// Administration options
				// ----------------------
				'underConstruction' : {'type' : 'boolean', 'title' : 'Выводить сообщение'},
				'underConstructionMessage' : {'type' : 'text', 'title' : 'Сообщение для пользователей', 'input' : 'textarea', 'isHTML' : true}
			},
			'object' : editor.settings
		});

		$(this.widget).append(this.form.form);
	},

	// Save data
	// ---------
	'save' : function() {

		// Save via API
		// ------------
		API.action({
			'action' : '/module/apps/settingsManager/saveCoreSettings',
			'data' : this.form.getValue()
		});

		// Notice an user
		// --------------
		alert('Изменения записаны');
	}

});
