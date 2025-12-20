document.addEventListener("DOMContentLoaded", function () {
    var paymentMethod = document.getElementById("payment-method");
    var bankSelect = document.getElementById("bank-select");

    // Ẩn phần chọn ngân hàng mặc định
    bankSelect.style.display = "none";

    // Khi thay đổi phương thức thanh toán
    paymentMethod.addEventListener("change", function () {
      if (paymentMethod.value === "online") {
        bankSelect.style.display = "block"; // Hiện phần ngân hàng
      } else {
        bankSelect.style.display = "none"; // Ẩn đi
      }
    });
  });
 function goToSuccessPage() {
    // (Tuỳ chọn) kiểm tra dữ liệu trước

    window.location.href = "success.html";
  }