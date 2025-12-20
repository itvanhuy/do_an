
// Hàm này được gọi khi người dùng nhấn nút kiểm tra mã giảm giá
function checkDiscount() {
  // Lấy ô input nơi người dùng nhập mã giảm giá (theo id "discount-input")
  var discountInput = document.getElementById("discount-input");

  // Lấy phần tử để hiển thị thông báo (theo id "discount-message")
  var discountMessage = document.getElementById("discount-message");

  // Lấy giá trị người dùng nhập, xóa khoảng trắng hai đầu và đổi sang chữ in hoa
  var code = discountInput.value.trim().toUpperCase();

  // Nếu mã giảm giá là "SALE10"
  if (code === "SALE10") {
    // Hiển thị thông báo thành công
    discountMessage.textContent = "You've received a 10% discount!"; // Thông báo được giảm 10%
    discountMessage.style.color = "green"; // Đổi màu chữ thành xanh lá
  } 
  // Nếu người dùng chưa nhập gì cả
  else if (code === "") {
    discountMessage.textContent = "Please enter a discount code."; // Yêu cầu nhập mã
    discountMessage.style.color = "red"; // Màu chữ đỏ để cảnh báo
  } 
  // Nếu mã nhập sai
  else {
    discountMessage.textContent = "Invalid discount code."; // Mã giảm giá không hợp lệ
    discountMessage.style.color = "red"; // Màu chữ đỏ
  }
}
