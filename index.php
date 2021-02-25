<?php
    /** 
     *   InSales API
     *   API (JSON): https://api.insales.ru/?doc_format=JSON 
     *   Info: https://www.insales.ru/collection/doc-rabota-s-api-i-prilozheniya/product/kak-sdelat-zapros-k-api
     *   Wiki: https://wiki.insales.ru/wiki/%D0%9A%D0%BE%D0%BC%D0%B0%D0%BD%D0%B4%D1%8B_API
     */

    require_once 'vendor/autoload.php';

    // Settings 
    $identity = '570fa0aa4417f7a12ded9bf291403113';
    $password = 'ab496e50523dd99c42de2c0b03e00aa9';
    $host_name = 'myshop-bkg431.myinsales.ru';

    $client = new \InSales\API\ApiClient($identity, $password, $host_name);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Test App Store from inSales API</title>
        <link rel="stylesheet" href="/styles/style.css">
    </head>
    <body>      
        <div class="product-count">
            <div class="product-count__block">
                <div class="product-count__title">
                    Test App Store from inSales API
                </div>
                Количество продуктов в магазине: 
                <span id="count-products">
                    <?= count($client->getProducts()->getData()) ?>
                </span>
            </div>
            <div class="product-count__block" id="putInOrder">
                <div class="product-count__text">
                    Количество товаров в корзине: <span id="count-products-cart">0</span>
                </div>
                <button class="product-count__button" onclick="addInCartAPI(localCart)">Сформировать заказ</button>
            </div>
        </div>
        
        <div class="product-list" id="product-list">
            <?php 
                foreach ($client->getProducts()->getData() as $key => $product) {
            ?>
                <div class="product-item">
                    <div class="product-item__image">
                        <img src="<?= $product['images'][0]['url'] ?>">
                    </div>
                    <div class="product-item__title">
                        <?= $product['title'] ?>
                    </div>
                    <div class="product-item__price">
                        <?= $product['variants'][0]['price_in_site_currency'] ?> Руб
                    </div>
                    <div class="product-item__desc">
                        <?= $product['short_description'] ?>
                    </div>
                    <button class="product-item__button" 
                            onclick="addInCart(<?= $product['variants'][0]['id'] ?>, 1)">
                        Добавить в корзину
                    </button>
                </div>
            <?php }?>
        </div>
        
        <script>
            let localCart    = [],                                // Set Local Cart For Added Product in Page
                cartCount    = 0,                                 // Set Total Counter Product Cart
                actionForm   = 'http://just-team.ru/cart_items',  // Set Action URL to InSales Shop
                putInOrder   = document.querySelector('#putInOrder'),
                countCardDOM = document.querySelector('#count-products-cart');

            // Add in Local Cart
            function addInCart(id, quantity = 1) {
                localCart.push({
                    'productID': id,
                    'quantity': quantity
                });

                if (localCart.length > 0) {
                    putInOrder.style.display = 'flex';
                    countCardDOM.innerHTML = localCart.length + cartCount;
                } else {
                    putInOrder.style.display = null;
                }
            }

            // Send Query in InSales Shop
            function addInCartAPI(localCart) {
                let form = document.createElement('form');

                form.action = actionForm;
                form.method = 'GET';
                form.innerHTML += `
                    <input type="hidden" name="lang" value="">
                    <input type="hidden" name="_method" value="patch">
                    <input type="hidden" name="variant_ids" value='${JSON.stringify(localCart)}'>
                `;

                document.body.append(form);
                form.submit();
            }
        </script>
    </body>
</html>