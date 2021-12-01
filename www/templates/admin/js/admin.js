/**
 * получение данных с формы
 */
function getData(obj_form) {
    var hData = {};
    $('input, textarea, select', obj_form).each(function(){
        if (this.name && this.name!==''){
            hData[this.name] = this.value;
            console.log('hData[' + this.name + '] = ' + hData[this.name]);
        }
    });
    return hData;
}
/*
 * добавление новой категории
 */
function newCategory(){
    var postData = getData('#blockNewCategory');

    $.ajax({
        method: 'POST',
        url: "/admin/addnewcat/",  // myshop.local/admin/addnewcat/
        data: postData,
        dataType: "json",
        success: function(data){
            if (data['success']){ // if success == 1
                alert(data['message']);
                $('#newCategoryName').val('');
            }else{
                alert(data['message']);
            }
        }
    })
}
/*
* обновление данных категории
 */
function updateCat(itemId){
    var parentId = $('#parentId_' + itemId).val(); // суть в том что мы обращаемся к свойтсву css по id айтема, и получаем внутренности
    var newName = $('#itemName_' + itemId).val(); // название свойства css отражает его внутренности, а то число что через нижнее подчеркивание,
    // просто идентификатор айтема через который удобно получать эту информацию, читается так: у айтема номер один
    // есть parent_id(parent_1) и itemId_id(itemId_1). На одной строке один и тот же айди, но value у них разное
    var postData = {itemId: itemId, parentId: parentId, newName: newName}; // json формат

    $.ajax({
        method: 'POST',
        url: "/admin/updatecategory/",  // myshop.local/admin/updatecategory/
        data: postData,
        dataType: "json",
        success: function(data) {

            alert(data['message']);

        }



    })
}

function addProduct(){
    var itemName = $('#newItemName').val();
    var itemPrice = $('#newItemPrice').val();
    var itemCatId = $('#newItemCatId').val();
    var itemDesc = $('#newItemDesc').val();

    var postData = {itemName: itemName, itemPrice: itemPrice, itemCatId: itemCatId, itemDesc: itemDesc};


    $.ajax({
        method: 'POST',
        url: "/admin/addproduct/",  // myshop.local/admin/addproduct/
        data: postData,
        dataType: "json",
        success: function(data) {
            alert(data['message']);
            if(data['success']){
                $('#newItemName').val('');
                $('#newItemPrice').val('');
                $('#newItemCatId').val('');
                $('#newItemDesc').val('');
            }
        }



    })
}

/**
 * изменение данных продукта
 * @param itemId
 */
function updateProduct(itemId){
    var itemName = $('#itemName_' + itemId).val();
    var itemPrice =  $('#itemPrice_' + itemId).val();
    var itemCatId =  $('#itemCatId_' + itemId).val();
    var itemDesc =  $('#itemDesc_' + itemId).val();
    var itemStatus =  $('#itemStatus_' + itemId).prop('checked');

    // если атрибут checked не стоит, то позиция товара активна на сайте
    if (!itemStatus){
        itemStatus = 1;
    }else{
        itemStatus = 0;
    }

    var postData = {itemId: itemId, itemName: itemName, itemPrice: itemPrice, itemCatId: itemCatId, itemDesc:itemDesc, itemStatus:itemStatus};

    $.ajax({
        method: 'POST',
        url: "/admin/updateproduct/",  // myshop.local/admin/updateproduct/
        data: postData,
        dataType: "json",
        success: function(data) {
            alert(data['message']);
        }
    })
}

/**
 * показывать или спрятать данные о заказе( товары заказа )
 */
function showProducts(id) {
    var objName = "#purchasesForOrderId_" + id; // формируем id элемента с которого пришел запрос о показе товаров

    $(objName).toggle();
}

/**
 * изменение статуса заказа
 * @param itemId
 */
function updateOrderStatus(itemId){
    var status = $('#itemStatus_' + itemId).prop('checked');
    // если галка стоит то статус товара является закрытым ( 1 )
    if (status){
        status = 1;
    }else{
        status = 0;
    }

    var postData = {itemId: itemId, status: status};

    $.ajax({
        method: 'POST',
        url: "/admin/setorderstatus/",  // myshop.local/admin/setorderstatus/
        data: postData,
        dataType: "json",
        success: function(data) {
            if (!data['success']){
                alert(data['message']);
            }
        }
    })
}

/**
 * изменение информации о заказе
 * @param itemId
 */
function updateDatePayment(itemId){
    var datePayment = $('#datePayment_' + itemId).val();
    var postData = {itemId: itemId, datePayment: datePayment};

    $.ajax({
        method: 'POST',
        url: "/admin/setorderdatepayment/",  // myshop.local/admin/setorderdatepayment/
        data: postData,
        dataType: "json",
        success: function(data) {
            if (!data['success']){
                alert(data['message']);
            }
        }
    })
}

function createXML(){
    $.ajax({
        method: 'POST',
        url: "/admin/createxml/",  // myshop.local/admin/createxml/
        dataType: "html",
        success: function(data) {
            $('#xml-place').html(data);
            window.open('http://www.myshop.local/xml/products.xml', '_blank');
        }
    })
}


function adminAuth(){

    let name = $('#name').val();
    let password = $('#password').val();
    let postData = {name: name, password: password};
    $.ajax({
        method: 'POST',
        url: "/admin/auth/",  // myshop.local/admin/auth/
        data: postData,
        dataType: "json",
        success: function(data) {
            if (data['success'] !== 0){
                $('#auth-error').html(data['message']);
                document.location = '/admin/';
            }
        }
    })
}

