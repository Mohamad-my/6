<?php
// تضمين ملف الهيدر
include('file/header.php');


?>

<!----product start------>
<main>
    <?php
    // استعلام جلب المنتجات
    $query = "SELECT * FROM product";
    $result = mysqli_query($conn, $query);

    // التحقق من وجود نتائج
    if (mysqli_num_rows($result) > 0) {
        // حلقة لجلب كل منتج
        while ($row = mysqli_fetch_assoc($result)) {
            // جلب قيم المنتج
            $proname = $row['proname'];
            $proprice = $row['proprice'];
            $prodescipr = $row['prodescipr'];
            $prouny = $row['prouny'];
            $proimg = $row['proimg'];

            
    ?>
        <div class="product">
            <!-- عرض الصورة من قاعدة البيانات -->
            <div class="product-img">
                <img src="uploads/img/<?php echo $proimg ?>" alt="Product Image">
                <!-- إذا كانت الكمية غير متوفرة -->
                <?php if ($prouny == 0): ?>
                    <span class="unvailabel">غير متوفر</span>
                <?php endif; ?>
            </div>
            <!-- اسم المنتج -->
            <div class="product-name">
                <a href="details.php?id=<?php echo $row['id']; ?>"><?php echo $proname; ?></a>
            </div>
            <!-- السعر -->
            <div class="product-price">
                <a href="#"><?php echo $proprice; ?> JD</a>
            </div>
            <!-- وصف المنتج -->
            <div class="product-description">
                <a href="details.php?id=<?php echo $row['id']; ?>"><i class="bi bi-eye-fill"></i> تفاصيل المنتج اضغط هنا</a>
            </div>
            <!-- كمية المنتج -->
            <div class="qty-input">
            <input type="number" id="quantity_<?php echo $row['id']; ?>" name="quantity" value="1" min="1" max="<?php echo $prouny; ?>">
            </div><br><br>
            <!-- زر الإضافة إلى السلة -->
            <div class="submit">
            <button class="addto-cart" type="button" onclick="addToCart('<?php echo $row['id']; ?>', '<?php echo $proname; ?>', <?php echo $proprice; ?>, document.getElementById('quantity_<?php echo $row['id']; ?>').value)">
                    <i class="bi bi-cart">&nbsp; &nbsp;</i> اضف الى السلة
                </button>
            </div>
        </div>
    <?php
        }
    } else {
        echo "لم يتم العثور على منتجات.";
    }
    ?>
</main>
<!----product end------>

<script>

let cartItems = []; // مصفوفة العناصر في السلة
let cartCount = 0;  // عدد المنتجات في السلة

// إضافة المنتج إلى السلة
function addToCart(productId, productName, productPrice, quantity) {
    const qty = parseInt(quantity);

    if (qty > 0) {
        const existingProductIndex = cartItems.findIndex(item => item.id === productId);

        if (existingProductIndex !== -1) {
            // تحديث الكمية
            cartItems[existingProductIndex].quantity += qty;
        } else {
            // إضافة منتج جديد
            cartItems.push({ id: productId, name: productName, price: productPrice, quantity: qty });
        }

        // تحديث عدد المنتجات
        cartCount += qty;
        document.querySelector('.cart-count').innerText = cartCount;

        // تقليل الكمية في قاعدة البيانات
        updateProductQuantity(productId, -qty); // تقليل الكمية في قاعدة البيانات

        // حفظ السلة في localStorage
        localStorage.setItem('cartItems', JSON.stringify(cartItems)); // حفظ البيانات

        updateCartItems(); // تحديث السلة
    } else {
        alert(`${productName} غير متوفر.`);
    }
}


// وظيفة لتحديث الكمية في قاعدة البيانات
function updateProductQuantity(productId, change) {
    // إجراء طلب AJAX لتحديث الكمية في قاعدة البيانات
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_quantity.php', true); // تأكد من أن لديك ملف update_quantity.php
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (this.status === 200) {
            console.log(this.responseText); // طباعة استجابة الخادم
        }
    };
    xhr.send(`product_id=${productId}&change=${change}`);
}

// إزالة المنتج من السلة
function removeFromCart(productId) {
    const productIndex = cartItems.findIndex(item => item.id === productId);
    if (productIndex !== -1) {
        // زيادة الكمية مرة أخرى في قاعدة البيانات
        updateProductQuantity(productId, cartItems[productIndex].quantity); // زيادة الكمية

        // تقليل عدد العناصر في السلة
        cartCount -= cartItems[productIndex].quantity;
        cartItems.splice(productIndex, 1); // إزالة المنتج من المصفوفة
        document.querySelector('.cart-count').innerText = cartCount; // تحديث العدد

        updateCartItems(); // تحديث السلة
    }
}


// تحديث المنتجات في السلة
function updateCartItems() {
    const cartList = document.querySelector('.cart-items'); // العنصر الذي سيحتوي على العناصر المضافة
    cartList.innerHTML = ''; // تفريغ السلة الحالية

    // إضافة كل عنصر في السلة
    cartItems.forEach((item) => {
        const li = document.createElement('li');
        li.innerHTML = `${item.name} - ${item.price} JD - الكمية: ${item.quantity} 
        <button onclick="removeFromCart('${item.id}')">إزالة</button>`;
        cartList.appendChild(li); // إضافة العنصر إلى القائمة
    });

    // تحديث إجمالي العناصر
    document.querySelector('.cart-summary .cart-count').innerText = cartItems.length;
}



// إظهار/إخفاء السلة
document.addEventListener('DOMContentLoaded', function() {
    // تعريف toggleCart هنا
    function toggleCart() {
        const cartModal = document.getElementById('cartModal');
        cartModal.style.display = cartModal.style.display === 'none' ? 'block' : 'none';
    }

    // إضافة حدث لزر فتح السلة
    document.querySelector('.cart-toggle').addEventListener('click', toggleCart);
});

// إفراغ السلة
function emptyCart() {
    cartCount = 0; // إعادة تعيين عدد المنتجات
    cartItems = []; // إعادة تعيين مصفوفة العناصر
    document.querySelector('.cart-count').innerText = cartCount; // تحديث العدد
    updateCartItems(); // تحديث السلة
}

// تأكيد الطلب
function confermcart() {
    console.log("تحقق من العناصر في السلة...");
    console.log(cartItems); // طباعة محتويات السلة في وحدة التحكم

    if (cartItems.length === 0) {
        alert("سلتك فارغة.");
        return;
    }

    let cartDetails = "تفاصيل السلة:\n\n";
    cartItems.forEach(item => {
        cartDetails += `${item.name} - ${item.price} JD - الكمية: ${item.quantity}\n`;
    });

    cartDetails += `\nعدد المنتجات: ${cartCount}`;

    if (confirm(cartDetails + "\nهل تريد تأكيد الطلب؟")) {
        alert("تم تأكيد الطلب! ستتم إعادة توجيهك الآن.");
        window.location.href = "admain/order_form.php"; // تأكد من أن اسم الملف صحيح
        emptyCart(); // إفراغ السلة بعد التأكيد
    } else {
        alert("تم إلغاء الطلب.");
    }
}




</script>

</body>
</html>
