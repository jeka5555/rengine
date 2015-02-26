console.log('REngine CMS (release 2015.02.18)');

safeAssign = function() {
    for (var a = 0; a < arguments.length; a++) {
        if (arguments[a] !== undefined && arguments[a] != null) return arguments[a];
    }
}


// Сохранение координат мышки
// ---------------------------------
$(document).ready(function() {

    if (jQuery.browser.opera != true) {
        var adminDesktop = $('<div class="admin-desktop"></div>');
        $("body").append(adminDesktop);
        Core.hasDesktop = true;
    }

    else Core.hasDesktop = false;

})


// Приложения
// ----------
Apps = {

    // Запуск приложения
    // -----------------
    start : function(appID, args, options) {

        if (Core.debug) console.log('Запуск приложения', appID);

        // Находим приложение и инициализируем
        // -----------------------------------
        var appClassName = appID.charAt(0).toUpperCase() + appID.slice(1);

        if (Apps[appClassName] != null) {
            var applicationClass = Apps[appClassName];
            var application = new applicationClass(args);
            return application;
        }
        else {
            console.log("Приложение " +  appID + " не найдено");
        }
    }
}

// Функции ядра
// ------------
Core = {

    debug: true,
    useAjax: true,
    isStarted : false,

    editModeOn : function() { Core.state.editMode = true; Global.callEvent('editModeOn'); },
    editModeOff : function() { Core.state.editMode = false; Global.callEvent('editModeOff'); },

    start : function() {

        // Отслеживаем положение курсора
        // -----------------------------
        $(document).mousemove(function(e){
            window.mouseX = e.pageX;
            window.mouseY = e.pageY;
        });

        Core.initHistory();
        Core.initPage(document);

        // Отмечаем готовность
        // -------------------
        Core.isStarted = true;
    },

    // Задаем состояние
    // ----------------
    setState : function(state) {

//		if (Core.debug) console.log('Загружено новое состояние', state);

        // Переход на страницу
        // -------------------
        if (state.path != null && Core.state.path != state.path && state.path[0] == '/') {
            window.history.pushState(state, '', state.path);
        }

        // Устанввливаем объекты
        // ---------------------
        if (state.pageTitle != null) Core.setTitle(state.pageTile);

        // Записываем
        // ----------
        Core.state = state;
        Global.callEvent('changeState', state);
    },

    // Переход на страницу
    // -------------------
    reload: function(path) {
        Core.setLocation(Core.state.path);
    },

    // Переход на страницу
    // -------------------
    setLocation: function(path, setState, setContent) {

        if (Core.debug) console.log('Переход на адрес: ', path);

        // Если ничего нет, ничего не деляем
        // ---------------------------------
        if (path == null) return;

        // Переход на нелокальные ссылки
        // -----------------------------
        if (path[0] != '/' || Core.useAjax != true) {
            window.location = path;
            return;
        }

        // Установка пути
        // --------------
        if (setState !== false) {
            Core.setState({
                'path' : path
            });
        }

        $('body').spin(spinOpts);
        API.action({
            'action' : '/module/sites/setPath',
            'data' :  {'path' : path, 'setContent' : setContent},
            'callback' : function(result) {

                $.each(result, function(key){
                    if (result[key] != null) {
                        if (key == 'content') {
                            $('#'+key).fadeOut(function(){
                                $('#'+key).replaceWith(result[key]);
                                $('#'+key).hide();
                                $('#'+key).fadeIn(function(){
                                    Core.initPage(content);
                                });

                            });
                        } else {
                            $('#'+key).replaceWith(result[key]);
                            Core.initPage(content);
                        }
                    }
                });

                $('body').spin(false);

            }
        });

        // Информируем что событие произошло
        // ---------------------------------
        Global.callEvent('changeLocation', path);
    },

    // Задаем название страницы
    // ------------------------
    setTitle : function(title) {
        document.title = title;
        Global.callEvent('changeTitle', title);
    },

    // Задаем контент
    // --------------
    setContent : function(content, block) {
        if (Core.debug) console.log('Содержимое страницы обновлено');

        var content = $(content);
        var block = $(document).find('#'+block);

        $(block).animate({
            opacity: 0
        }, 800, function() {
            $(this).replaceWith(content).promise().done(function(){
                $(document).trigger('ajaxReady');
                Core.initPage(content);
            });
            $(block).css({opacity : 0});
            $(block).animate({opacity: 1}, 800, function(){
                $(window).scrollTop(0);
            });
        });

    },


    // Импорт ассетов
    // --------------
    importAssets : function(assets, callback) {

        var newJS = [];
        var newCSS = [];

        // Подключение стилей
        // ------------------
        if (assets.css != null) {
            $.each(assets.css, function(index, file) {

                // Отбор новых
                // -----------
                if (!Core.assets.css.hasOwnProperty(index)) {
                    Core.assets.css[index] = file;

                    var part = document.createElement('link');
                    part.setAttribute('type', 'text/css');
                    part.setAttribute('href', file);
                    document.getElementsByTagName('head')[0].appendChild(part);

                }

            });
        }

        // Подключение скриптов
        // --------------------
        if (assets.js != null) {
            $.each(assets.js, function(index, file) {

                // Отбор новых
                // -----------
                if (!Core.assets.js.hasOwnProperty(index)) {
                    Core.assets.js[index] = file;
                    newJS.push(file);
                }

            });
        }

        // Если не путой список, выполняем загрузку
        // ----------------------------------------
        var filesCount = 0;

        if (newJS.length > 0) {
            filesCount += newJS.length;

            // Для всех
            // --------
            $.each(newJS, function(index, file) {
                var part = document.createElement('script');
                part.setAttribute('type', 'text/javascript');
                part.setAttribute('src', file);
                document.getElementsByTagName('head')[0].appendChild(part);
                part.onload = function() {
                    filesCount--;
                    if (filesCount <= 0) {
                        if(callback != null) callback.call();
                    }
                };
            });
        }

        // Загрузка
        // --------
        else if(callback != null) callback.call();

    },

    // Инициалиазция контента блока
    // ----------------------------
    initPage : function(page) {

        // AJAX-ссылки
        // -----------
        if (Core.useAjax == true) {

            // Замена ссылок
            // -------------
            $('body').find('a').each(function(){

                if ($(this).data('useAjax') == 'true') return;

                var href = $(this).attr('href');

                var re = /(?:\.([^.]+))?$/;
                var ext = re.exec(this.pathname)[1];

                if(href == null || (href[0] != '/' && this.origin != window.location.origin) || $(this).attr('target') == '_blank' || ext != null) return;

                $(this).data('useAjax', 'true');

                $(this).click(function(e) {
                    e.preventDefault();
                    Core.setLocation(this.pathname+this.search);

                });
            });

        }

        $(document).trigger('init');

    },

    // Иницилаизация функционала истории
    // ---------------------------------
    initHistory : function() {

        // Обработка переходов назад и вперёд по истории браузера
        // ------------------------------------------------------
        var popped = ('state' in window.history), initialURL = location.href;

        $(window).bind('popstate', function(event) {

            // Проверка, есть ли элемент в истории
            // -----------------------------------
            var initialPop = !popped && location.href == initialURL;
            popped = true;
            if ( initialPop ) return;

            // Возврат из истории
            // -------------------
            Core.state.history = true;

            // Задаем состояние
            // ----------------
            var state = event.originalEvent.state;
            if (location.pathname && event.originalEvent.state) {
                if (Core.debug) console.log('Переход назад по истории');

                // Если на главную, то перезагружаем страницу (ТОЛЬКО ДЛЯ АИСТА)
                if (location.pathname == '/') {
                    window.location = location.pathname;
                } else {
                    Core.setLocation(location.pathname, false);
                }
            }

        });

    },


    // Информация о состоянии
    // ----------------------
    state : {},

    // Ресурсы
    // -------
    assets : {
        'css' : {},
        'js' : {},
        'image' : {}
    },

    // Вызов системного события
    // ------------------------
    callEvent : function(type, data) {
        Global.callEvent(type, data);
    }

}