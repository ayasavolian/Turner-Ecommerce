    <div class = "topcontainer">
        <a href = "index.php">
        <div class = "mkto-logo">
            <img id = "mkto-logo" src = "images/turnerecommerce.png">
        </div>
        </a>
        <div class = "topmenu">
            <div class = "menucell" onclick = "nyanlightboxon()">
                <div class = "menuoption">
                    User Account
                </div>
            </div>
            <div class = "menucell">
                <div class = "menuoption2" id = "cartbutton" onclick = "setvisibility()">
                    <img style = "width: 50px; height: 50px;" src = "images/cart.png">
                </div>
            </div>
        </div>
        <div class = "searchcontainer">
                <div class = "searchword">
                    Search
                </div>
                <form action = "/te2/searchresults.php" method="post">
                <div class = "searchbar">
                <div class = "topbar">
                <div class = "searchbutton">
                    <input type="submit" class = "button" value="Go" data-nav-tabindex="72" tabindex="1">
                    <?php
                    echo '<input type="hidden" name="orderid" value="'.$orderid.'" />';
                    ?>
                </div>
                <div class = "searchwidth">
                    <input type <input class="searchbardiv" id="searched" type="text" name="site-search">
                </div>
                </div>
                </div>
                </form>
        </div>
    </div>
    <div class = "cartcontainer" id = "cart">
        <?php
            if(isset($_SESSION["products"]))
            {
                $total = 0;
                echo '<div class = "carttitle">Cart Selection</div>';
                echo '<div class = "carttotalsize">';
                foreach ($_SESSION["products"] as $cart_itm)
                {
                    echo '<div class ="cartlinecontainer">';
                        echo '<div class="cartsku">'.$cart_itm['sku'].'</div>';
                        echo '<div class="cartremove"><a href="updatecart.php?removep='.$cart_itm['id'].'&return_url='.$current_url.'">x</a></div>';
                        echo '<div class="cartprice">$'.$cart_itm['price'].'</div>';
                        echo '<div style = "padding-right: 2px" class="cartprice">('.$cart_itm['quantity'].')</div>';
                    echo '</div>';
                    $total = $total + ($cart_itm['price']*$cart_itm['quantity']);
                }
                echo '</div>';
                echo '<div class = "cartcheckout">';
                echo '<form action = "checkout.php" method = "post">';
                    echo '<button type = "submit" class = "checkoutbutton">';
                        echo 'Checkout';
                    echo '</button>';
                echo '</div>';
                echo '<input type="hidden" name="orderid" value="'.$orderid.'" />';
                echo '</form>';
                echo '<div class ="cartlinecontainer">';
                    echo '<div class ="carttotal">Total: $'.$total.'</div>';
                echo '</div>';
            }else{
                echo 'Your cart is empty';
            }
        ?>
    </div>