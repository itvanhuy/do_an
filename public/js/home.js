// Khi toàn bộ nội dung HTML đã tải xong thì thực thi đoạn mã
document.addEventListener('DOMContentLoaded', function () {

  // Lấy tất cả các thẻ ảnh bên trong slider có class .hero-slider
  var images = document.querySelectorAll('.hero-slider img');

  // Lấy phần tử tiêu đề (thẻ h1) và mô tả (thẻ p) trong phần nội dung slider
  var heading = document.querySelector('.hero-content h1');
  var paragraph = document.querySelector('.hero-content p');

  // Chỉ số của ảnh hiện tại trong slider, bắt đầu từ 0 (tức là ảnh đầu tiên)
  var currentIndex = 0;

  // Mảng chứa tiêu đề và mô tả tương ứng với từng ảnh trong slider
  var slideText = [
    {
      title: "Join Exciting Esports Tournaments",
      description: "Compete together in major esports tournaments, with incredibly attractive prizes."
    },
    {
      title: "Discover the Latest Gaming Products!",
      description: "Professional gaming devices for all gamers, ready to conquer any hot game title."
    },
    {
      title: "Welcome to TECHSHOP – The World of Technology & Esports",
      description: "Explore high-quality gaming devices, participate in exciting tournaments, and connect with the gaming community."
    }
  ];

  // Hàm dùng để hiển thị ảnh và cập nhật tiêu đề/mô tả tương ứng
  function changeSlide() {
    // Ẩn tất cả ảnh bằng cách gỡ bỏ class 'active'
    images.forEach(function(img) {
      img.classList.remove('active');
    });

    // Chỉ hiển thị ảnh tại vị trí currentIndex bằng cách thêm class 'active'
    images[currentIndex].classList.add('active');

    // Cập nhật tiêu đề và mô tả cho phù hợp với ảnh hiện tại
    heading.textContent = slideText[currentIndex].title;
    paragraph.textContent = slideText[currentIndex].description;
  }

  // Hàm để chuyển sang ảnh kế tiếp
  function nextSlide() {
    // Tăng chỉ số ảnh hiện tại lên 1, nếu vượt quá số lượng ảnh thì quay lại ảnh đầu
    currentIndex = (currentIndex + 1) % images.length;
    changeSlide(); // Gọi hàm cập nhật ảnh và nội dung
  }

  // Tự động chuyển ảnh mỗi 4 giây
  setInterval(nextSlide, 4000);

  // Khi trang vừa mở, hiển thị ngay ảnh đầu tiên
  changeSlide();
});
