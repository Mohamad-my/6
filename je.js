let cartItems = [];
let cartCount = 0;

// إضافة المنتج إلى السلة
function addToCart(productId, productName, productPrice, quantity) {
    const qty = parseInt(quantity);
    if (qty > 0) {
        // بدلاً من الـ alert، استدعاء دالة تحديث السلة مباشرة
        updateCartItems(productId, productName, productPrice, qty);
        console.log(`تمت إضافة المنتج إلى السلة: ${productName}, الكمية: ${qty}`);
    } else {
        console.log(`${productName} غير متوفر.`);
    }
}



// دالة لتحديث الكمية في واجهة المستخدم بعد تقليلها
function updateQuantityOnUI(productId, newQuantity) {
    const quantityElement = document.getElementById(`quantity_${productId}`);
    if (quantityElement) {
        quantityElement.max = newQuantity; // تحديث الحد الأقصى
        if (newQuantity === 0) {
            quantityElement.value = 0;
        }
    }
}

// تحديث المنتجات في السلة
function updateCartItems(productId, productName, productPrice, quantity) {
    // تحقق مما إذا كان المنتج موجودًا بالفعل في السلة
    const existingItemIndex = cartItems.findIndex(item => item.id === productId);
    
    if (existingItemIndex !== -1) {
        // إذا كان موجودًا، قم بتحديث الكمية
        cartItems[existingItemIndex].quantity += quantity;
    } else {
        // إذا لم يكن موجودًا، أضف منتجًا جديدًا
        cartItems.push({
            id: productId,
            name: productName,
            price: productPrice,
            quantity: quantity
        });
    }

    // تحديث العدد الإجمالي للمنتجات في السلة
    cartCount += quantity; 
    console.log(cartItems); // عرض العناصر في السلة لمراقبة الإضافة
    
    // تحديث واجهة المستخدم بعد تحديث السلة
    displayCartItems();
}


// دالة لإظهار العناصر في السلة
function displayCartItems() {
    const cartList = document.querySelector('.cart-items');
    cartList.innerHTML = ''; // مسح العناصر الحالية

    cartItems.forEach((item) => {
        const li = document.createElement('li');
        li.innerHTML = `${item.name} - ${item.price} JD - الكمية: ${item.quantity}`;
        cartList.appendChild(li);
    });

    // تحديث إجمالي العناصر
    document.querySelector('.cart-summary .cart-count').innerText = cartCount;
}


// إظهار/إخفاء السلة
function toggleCart() {
    const cartModal = document.getElementById('cartModal');
    cartModal.style.display = cartModal.style.display === 'none' ? 'block' : 'none';
}

// إفراغ السلة
function emptyCart() {
    cartCount = 0;
    cartItems = [];
    document.querySelector('.cart-count').innerText = cartCount;
    displayCartItems(); // تحديث الواجهة بعد إفراغ السلة
}

// تأكيد السلة
function confirmCart() {
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
        window.location.href = "order_form .html"; // إصلاح المسار
        emptyCart(); // إفراغ السلة بعد تأكيد الطلب
    } else {
        alert("تم إلغاء الطلب.");
    }
}

// Event listener for DOM content loaded
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cart-toggle-button').addEventListener('click', toggleCart);
    document.getElementById('empty-cart-button').addEventListener('click', emptyCart);
    document.getElementById('confirm-cart-button').addEventListener('click', confirmCart);
});

