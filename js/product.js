document.querySelectorAll('.add-to-cart-btn').forEach(function(button) {
    button.addEventListener('click', function() {
      alert('Bạn cần đăng nhập để mua hàng.');
    });
  });