

/**
 * Функция добавления товара в корзину
 *
 * 
 * @param  itemId ID продукта
 * @return в случае успеха обновятся данные корзины на странице
 */
function addToCart(itemId){
   
    $.ajax({
        method: 'POST',
        
        url: "/cart/addtocart/" + itemId + "/",
        //> contentType строка дает понять принимающей стороне что запрос идет в json
        contentType: "application/json",            
        //< dataType указывает jquery что принимается строка в json
        /**
         * btw без contentType приходила string в ответ, но сейчас и без contentType
         * работает нормально
         */
        dataType: "json",
        success: function(data){ // data - echo ответ скрипта к которому пошел ajax
            if (data['success']){ 
                $('#cartCntItems').html(data['cntItems']); // количество товаров в корзине, показывается в левом меню
                
                $('#addCart_' + itemId).hide();
                $('#removeCart_' + itemId).show();
            }
        }
    });
}


/**
 * Функция удаления товара из корзины
 *
 *
 * @param itemId ID продукта
 * @return в случае успеха обновятся данные корзины на странице
 */
 function removeFromCart(itemId){
   
    $.ajax({
        method: 'POST',
        
        url: "/cart/removefromcart/" + itemId + "/",
        //> contentType строка дает понять принимающей стороне что запрос идет в json
        contentType: "application/json",            
        //< dataType указывает jquery что принимается строка в json
        /**
         * btw без contentType приходила string в ответ, но сейчас и без contentType
         * работает нормально
         */
        dataType: "json",
        success: function(data){ // data - echo ответ скрипта к которому пошел ajax
            if (data['success']){ 
                $('#cartCntItems').html(data['cntItems']); // количество товаров в корзине, показывается в левом меню
                
                $('#addCart_' + itemId).show();
                $('#removeCart_' + itemId).hide();
            }
        }
    });
}


/**
 * Подсчет стоимости купленного товара
 *
 * @param itemId ID продукта
 */
function conversionPrice(itemId){
    let newCnt = $('#itemCnt_' + itemId).val(); // парсим html значение (value='...') у этого айди ( количество данного товара )
    let itemPrice = $('#itemPrice_' + itemId).attr('value'); 
    // здесь стоит не value='...', а 'attr()' потому что 
    // у тега 'span' нет атрибута 'value' по умолчанию
    // и мы его как бы создали сами ( название можно было создать любое )
    let itemRealPrice = newCnt * itemPrice;

    $('#itemRealPrice_' + itemId).html(itemRealPrice); // изменяем цену за все экземпляры одного товара по его тегу
}

/**
 *
 * получение данных с формы
 * 
 * 
 */
function getData(obj_form){
    let hData = {};
    /**
            each - в каждом указанном теге будет выполняться функция - 
            заполняем hData значением выбранного объекта 
    */
    /*
    пример данных которые получатся:
    hData[email] = gwrgrg
    hData[pwd1] = 123
    hData[pwd2] = 123
    */
    $('input, textarea, select', obj_form).each(function(){ 
        if(this.name && this.name!==''){
            hData[this.name] = this.value;
            console.log('hData[' + this.name + '] = ' + hData[this.name]);
        }
    });
    return hData;
}


/**
 * Регистрация нового пользователя
 *
 */
function registerNewUser(){
    let postData = getData('#registerBox'); // данные выбранного html id
   
    $.ajax({
        method: 'POST',
        
        url: "/user/register/",  // myshop.local/user/register/
        //> contentType строка дает понять принимающей стороне что запрос идет в json
        //contentType: "application/json",            
        //< dataType указывает jquery что принимается строка в json
        /**
         * btw без contentType приходила string в ответ, но сейчас и без contentType
         * работает нормально
         */
        // data - дата что отправляется
        data: postData,
        dataType: "json",
        success: function(data){ 
            if (data['success']){ // if success == 1
                alert("Регистрация прошла успешно");
                //> блок в левом столбце
                    $('#registerBox').hide(); // прячем блок регистрации

                    $('#userLink').attr('href', '/user/'); // содержимое href меняем на /user/
                    $('#userLink').html(data['userName']); // содержимое #userLink меняем на data['userName]
                    $('#userBox').show();
                //<
                //> страница заказа
                $('#loginBox').hide();
                $('#btnSaveOrder').show();
                //<
            }else{
                alert(data['message']);
            }
        }
    });
}

/** 
 * функция авторизации пользователя
 */

function login(){
    let email = $('#loginEmail').val(); // аналог функции getData
    let pwd = $('#loginPwd').val(); // возможно менее правильный( ? ) 

    let postData = "email="+email+"&pwd="+pwd;  // используется в основном когда мало значений в форме
    $.ajax({
        method: 'POST',
        
        url: "/user/login/",  // myshop.local/user/login/
        //> contentType строка дает понять принимающей стороне что запрос идет в json
        //contentType: "application/json",            
        //< dataType указывает jquery что принимается строка в json
        /**
         * btw без contentType приходила string в ответ, но сейчас и без contentType
         * работает нормально
         */
        // data - дата что отправляется
        data: postData,
        dataType: "json",
        success: function(data){ 
            if (data['success']){ // if success == 1
                
                //> блок в левом столбце
                    $('#registerBox').hide(); // прячем блок регистрации
                    $('#loginBox').hide();

                    $('#userLink').attr('href', '/user/'); // содержимое href меняем на /user/
                    $('#userLink').html(data['displayName']); // имя или почта
                    $('#userBox').show();
                //<

                //> заполняем поля на странице заказа
                $('#name').val(data['name']);
                $('#phone').val(data['phone']);            // TODO: данные заказа не видны если авторизоваться на странице заказа
                $('#address').val(data['address']);
                //<

                $('#btnSaveOrder').show();
                
            }else{
                alert(data['message']);
            }
        }
    });
}



/**
 * функция показывает ( и скрывает ) блок регистрации
 *
 */
function showRegisterBox(){
    $("#registerBoxHidden").toggle();
}

/**
 * showRegisterBox Можно было сделать так :
 * 
 * if ($("#registerBoxHidden").css('display') != 'block')  // == hide ( etc...)
 *    $("registerBoxHidden").show()
 * else
 *    $("registerBoxHidden").hide() // css свойство display будет заменено на display: none;
 *  */    


/** 
 * обновление данных пользователя
 */
function updateUserData(){
    console.log("js - updateUserData()");
    let phone = $('#newPhone').val();
    let address = $('#newAddress').val();
    let pwd1 = $('#newPwd1').val();
    let pwd2 = $('#newPwd2').val();
    let curPwd = $('#curPwd').val();
    let name = $('#newName').val();

    let postData = {
            phone: phone,
            address: address,
            pwd1: pwd1,
            pwd2: pwd2,
            curPwd: curPwd,
            name: name
    };

    $.ajax({
        method: 'POST',
        url: "/user/update/",  // myshop.local/user/update/
        data: postData,
        dataType: "json",
        success: function(data){ 
            if (data['success']){ // if success == 1
                $('#userLink').html(data['userName']);
                alert(data['message']);
            }else{
                alert(data['message']);
            }
        }
    });
}


/**
 * сохранение заказа
 */

function saveOrder(){
    let postData = getData('form');
    $.ajax({
        method: 'POST',
        url: "/cart/saveorder/",  // myshop.local/cart/saveorder/
        data: postData,
        dataType: "json",
        success: function(data){ 
            if (data['success']){ // if success == 1
                alert(data['message']);
                document.location = '/';
            }else{
                alert(data['message']);
            }
        }
    })
}


/**
 * показывать или спрятать данные о заказе( товары заказа )
 */
function showProducts(id) {
    let objName = "#purchasesForOrderId_" + id; // формируем id элемента с которого пришел запрос о показе товаров
    /* это конечно классно и круто, но есть .toggle();
    if ($(objName).css('display') !== 'table-row' ){  // http://htmlbook.ru/css/display
        $(objName).show();
    }else{
        $(objName).hide();
    }
     */
    $(objName).toggle();
}