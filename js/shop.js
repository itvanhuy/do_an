// Đợi trang web tải xong mới bắt đầu chạy mã bên trong
document.addEventListener("DOMContentLoaded", function () {
  // Tìm phần chứa các ảnh trượt (carousel)
  var track = document.querySelector(".carousel-track");
  // Tìm tất cả các nút chấm tròn dưới carousel
  var dots = document.querySelectorAll(".dot");
  // Tìm tất cả các slide (nhóm sản phẩm) thay vì thẻ card lẻ
  var slides = document.querySelectorAll(".carousel-slide");

  var currentIndex = 0; // Đánh dấu đang ở slide số mấy (bắt đầu từ 0)

  if (!track || slides.length === 0) return;

  // Hàm này dùng để chuyển đến slide số index
  function updateCarousel(index) {
    // Di chuyển phần track sang trái (mỗi ảnh chiếm 100%)
    track.style.transform = "translateX(-" + (index * 100) + "%)";

    // Bỏ màu active của tất cả các dot (xóa chấm sáng)
    dots.forEach(function (dot) {
      dot.classList.remove("active");
    });

    // Chỉ làm dot đang chọn sáng lên (chấm active)
    if (dots[index]) {
      dots[index].classList.add("active");
    }
  }

  // Khi người dùng bấm vào dot nào thì chuyển đến slide đó
  dots.forEach(function (dot) {
    dot.addEventListener("click", function () {
      currentIndex = parseInt(dot.dataset.index); // Lấy số thứ tự từ dot
      updateCarousel(currentIndex); // Gọi hàm chuyển slide
    });
  });

  // Tự động chuyển slide mỗi 4 giây
  setInterval(function () {
    currentIndex = (currentIndex + 1) % slides.length; // Tăng số, rồi quay lại 0 khi hết
    updateCarousel(currentIndex); // Chuyển slide
  }, 4000); // 4000 mili giây = 4 giây
});



// Đồng hồ đếm ngược Flash Sale (Kết thúc vào cuối ngày)
document.addEventListener("DOMContentLoaded", function () {
  // Thiết lập thời gian kết thúc là 23:59:59 của ngày hiện tại
  var now = new Date();
  var countdownDate = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59).getTime();

  // Lấy các phần hiển thị giờ, phút, giây trên trang
  var hoursEl = document.getElementById("hours");
  var minutesEl = document.getElementById("minutes");
  var secondsEl = document.getElementById("seconds");

  if (!hoursEl || !minutesEl || !secondsEl) return;

  // Hàm cập nhật thời gian còn lại
  function updateCountdown() {
    var now = new Date().getTime(); // Lấy thời gian hiện tại
    var distance = countdownDate - now; // Còn bao lâu nữa đến mốc thời gian

    // Nếu đã hết thời gian thì dừng lại
    if (distance < 0) {
      hoursEl.textContent = "00";
      minutesEl.textContent = "00";
      secondsEl.textContent = "00";
      clearInterval(interval); // Dừng không đếm nữa
      return;
    }

    // Tính giờ, phút, giây còn lại
    var hours = Math.floor(distance / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    // Hiển thị số ra giao diện với 2 chữ số (thêm số 0 nếu thiếu)
    hoursEl.textContent = hours.toString().padStart(2, "0");
    minutesEl.textContent = minutes.toString().padStart(2, "0");
    secondsEl.textContent = seconds.toString().padStart(2, "0");
  }

  // Cập nhật đồng hồ mỗi giây
  var interval = setInterval(updateCountdown, 1000);
  updateCountdown(); // Gọi ngay lần đầu để hiển thị đúng luôn
});



//  Tự động chuyển đổi các sản phẩm (giống banner sản phẩm)
document.addEventListener("DOMContentLoaded", function () {
  var slides = document.querySelectorAll(".products-slide"); // Lấy tất cả các slide sản phẩm
  var current = 0; // Slide hiện tại (bắt đầu từ 0)

  // Hàm này dùng để chỉ hiện slide đang cần, ẩn các slide khác
  function showSlide(index) {
    slides.forEach(function (slide, i) {
      // Nếu là slide đúng index thì hiển thị (thêm class 'active')
      slide.classList.toggle("active", i === index);
    });
  }

  // Mỗi 5 giây chuyển sang slide kế tiếp
  setInterval(function () {
    current = (current + 1) % slides.length; // Tăng số, rồi quay lại 0 nếu hết
    showSlide(current); // Gọi hàm hiển thị
  }, 5000); // 5 giây
});
