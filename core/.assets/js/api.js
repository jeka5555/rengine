API = { debug : false }

// Функции пакетной отправки
// =====================================================
API.Packets = {

    interval : 100,
    emptyCycles : 0,

    // Текущий пакет
    // -----------------------
    current : Math.round(Math.random() * 10000000),

    // Здесь все собранные пакеты
    // -----------------------
    queue : {},

    // Инициализация пакетного режима
    // -----------------------
    init : function() {
        setInterval(function() {

            // Бла бля
            // --------
            if (API.Packets.queue[API.Packets.current].length > 0) {
                API.Packets.send();
                API.Packets.create();
            }

        }, API.Packets.interval);
    },


    // Удаление пакета
    // -----------------------
    delete : function(id) {
        delete API.Packets.queue[id];
    },

    // Создание нового пакета
    // -----------------------
    create : function() {
        API.Packets.current ++;
        API.Packets.queue[API.Packets.current] = [];
    },

    // Оптравка текущего пакета
    // -----------------------
    send : function() {

        // Запомним номер пакета
        // ---------------------------
        var packetID = API.Packets.current;

        if (API.debug) {
            console.log('API.Packets: отправка пакета ' + packetID + ' количество запросов:' + API.Packets.queue[packetID].length);
        }

        var data = API.Packets.queue[packetID];

        // Посылаем запросик
        // ---------------------------
        API.request({
            'uri' : '/module/api/packetRequest',
            'data' : data,
            'callback' : function(data) {

                if (API.debug) console.log('API.Packets: результат для пакета ' + packetID + ' получен');

                // Для каждого элемента в пакете выполняем каллбак для каждой функции
                // -------------------
                if (data != null && typeof(data) == "object" && data.length > 0 ) {

                    $.each(data, function(index, response) {

                        // Вызываем каллбак, если задан
                        // -------------
                        var callback = API.Packets.queue[packetID][index].callback;
                        if (callback != null) callback(response);

                    });

                }

                // Удаляем пакет
                // -------------------
                API.Packets.delete(packetID);

            }
        });

    },

    // Добавить дейтвие в пакет
    // -----------------------
    pushAction : function(action) {

        var current = API.Packets.current;
        API.Packets.queue[current].push(action);
    }

};

API.Packets.create();
API.Packets.init();


// Отправка действия в движок
// ------------------------------
API.action = function(args) {
    if (API.debug) console.log('API.action : отправка действия ' + args.action);
    API.Packets.pushAction(args);
}


// Отправка запроса
// -------------------------------
API.request = function(args) {

    var data, uri;

    // Если запрос в виде строки, берем краткий формат
    // -----------------------------------------------
    if (typeof(args) == "string" ) { uri = args;}
    else uri = args.uri;

    // Преобразование данных запроса
    // -----------------------------
    var requestData = {
        'data' : JSON.stringify(args.data),
        'location' : window.location.toString()
    };
    // Посылаем запрос
    // ---------------
    $.ajax({
        url : uri,
        async : safeAssign(args.async, true),
        headers : {
	        'Request-Mode': "API"
        },
        type : 'POST',
        data : requestData,
        success : function(data) {

            // Разбор объекта
            // --------------------
            try {
                var response = $.parseJSON(data);
            }
            catch (err) {
                FlashMessages.add({'text' : 'Ошибка ответа сервера', 'type' : 'error'});
                return;
            }

            if (response == null) return;

            var mergeData = function() {

                // Update page content
                // -------------------
                if (response.pageContent != null && response.pageContent != "") {
                    Core.setContent(response.pageContent, response.contentBlock);
                }

                // Update page title
                // -----------------
                if (response.title != null) {
                    Core.setTitle(response.title);
                }


                // Scripts
                // -------
                if (response.script != null) {
                    $.each(response.script, function(scriptIndex, scriptBody) {
                        eval(scriptBody);
                    });
                }

                // Execute callback
                // ----------------
                if (args.callback !== undefined) args.callback(response.result);

                // Скрипты до каллбака
                // --------------------
                if (response.endScript != null) {
                    $.each(response.endScript, function(scriptIndex, scriptBody) {
                        eval(scriptBody);
                    });
                }

                // Call events
                // -----------
                if (response.events != null) {

                    // Debug message
                    // -------------
                    if (Core.debug == true) {
                        console.log('Получено '+ response.events.length + ' событий');
                    }

                    // Event
                    // -----
                    $.each(response.events, function(eventIndex, event) {
                        Core.callEvent(event.type, event.data)
                    });
                }

            };

            // Set new path
            // ------------
            if (response != null && response.location != null) {
                window.location = response.location;
            }

            // Подключение ассетов
            // -------------------
            if (response.assets != null) Core.importAssets(response.assets, mergeData);
            else mergeData.call();


        }
    });
}


// Объекты
// ======================================================================
API.Objects = {

    // Добавляем объект
    // ---------------------------------
    insert : function(args) {
        API.action({
            'action' : '/module/objects/insert',
            'data' : {
                'class' : args.class,
                'data' : args.data
            },
            'callback' : args.callback
        });
    },

    // Удаляем объект
    // ---------------------------------
    delete : function(args) {

        API.action({
            'action' : '/module/objects/'+ args.class +'/delete',
            'data' : {
                'query' : args.query
            },
            'callback' : args.callback
        });
    },

    // Чтение объектоа
    // ---------------------------------
    get : function(args) {
        API.action({
            'action' : '/module/objects/'+ args.class +'/find',
            'data' : {'query' : args.query },
            'callback' : args.callback
        });
    },

    // Обновляем объект
    // ---------------------------------
    update : function(args) {
        API.Objects.classAction({
            'action' : 'update',
            'class' : args.class,
            'data' : {'query' : args.query, 'data' : args.data},
            'callback' : args.callback
        });
    },

    // Сохранение объекта
    // ---------------------------------
    create : function(args) {
        API.action({
            'action' : '/module/objects/' + args.class + '/createObject',
            'data' : args.data,
            'callback' : args.callback
        });
    },

    // Apply object's action
    // ---------------------
    action : function(args) {
        API.action({
            'action' : '/module/objects/' + args.class + '/' +args.id + '/' + args.action,
            'data' : args.data,
            'callback' : args.callback
        });
    },

    // Class action
    // ------------
    classAction : function(args) {
        API.action({
            'action' : '/module/objects/classAction',
            'data' : {
                'action' : args.action,
                'class' : args.class,
                'objects' : args.objects,
                'data' : args.data
            },
            'callback' : args.callback
        });
    }


};



