// Функция для добавления товара в корзину
function addToCart(product_id) {
    $.ajax({
        type: 'POST',
        url: '../controllers/cart.php',
        data: {
            product_id: product_id,
            add: true
        },
        success: function(response) {
            // Обработка успешного выполнения запроса
            console.log('Товар'+product_id+'добавлен в корзину');
            document.getElementById('removeFromCartButton'+product_id).style.display = '';
            document.getElementById('addToCartButton'+product_id).style.display = 'none';
        },
        error: function() {
            // Обработка ошибки
            console.log('Произошла ошибка при добавлении товара в корзину');
        }
    });
}

function removeFromCart(product_id) {
    $.ajax({
        type: 'POST',
        url: '../controllers/cart.php',
        data: {
            product_id: product_id,
            delete: true
        },
        success: function(response) {
            console.log('Товар Удален из корзины');
            document.getElementById('removeFromCartButton'+product_id).style.display = 'none';
            document.getElementById('addToCartButton'+product_id).style.display = '';
        },
        error: function() {
            console.log('Произошла ошибка при добавлении товара в корзину');
        }
    });
}

// Функция для получения товаров в корзине
function getCartItems() {
    $.ajax({
        type: 'GET',
        url: '../controllers/cart.php',
        success: function(response) {
            // Обработка успешного выполнения запроса
            var items = response;
            console.log('Товары в корзине:');
            for (var i = 0; i < items.length; i++) {
                console.log('Товар с ID ' + items[i]["product_id"] + ', количество: ' + items[i]["quantity"]);
            }
            displayCartItems(items);
        },
        error: function() {
            // Обработка ошибки
            console.log('Произошла ошибка при получении товаров в корзине');
        }
    });
}
function checkCart(product_id){
    $.ajax({
        type: 'POST',
        url: '../controllers/cart.php',
        data: {
            product_id: product_id,
            check: true
        },
        success: function(response) {
            if(response["status"] === "success"){
                console.log('Товар'+product_id+'есть в корзине');
                document.getElementById('removeFromCartButton'+product_id).style.display = '';
                document.getElementById('addToCartButton'+product_id).style.display = 'none';
            }
            else{
                console.log('Товар'+product_id+'отсутствует2 в корзине');
                document.getElementById('removeFromCartButton'+product_id).style.display = 'none';
                document.getElementById('addToCartButton'+product_id).style.display = '';
            }

        },
        error: function() {
            console.log('Произошла ошибка при добавлении товара в корзину');
        }
    });
}
function displayCartItems(items) {
    var resultDiv = document.getElementById('result');
    resultDiv.innerHTML = '';
    for (var i = 0; i < items.length; i++) {
        var itemDiv = document.createElement('div');
        itemDiv.innerHTML = 'Товар с ID ' + items[i].product_id + ', количество: ' + items[i].quantity;
        resultDiv.appendChild(itemDiv);
    }
}