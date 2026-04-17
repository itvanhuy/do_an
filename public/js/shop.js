document.addEventListener("DOMContentLoaded", function () {
  /**
   * Hàm helper dùng chung để tạo bộ tự động chuyển đổi (Slider/Carousel)
   */
  function createAutoCycle(selector, intervalTime, callback) {
    const items = document.querySelectorAll(selector);
    if (items.length === 0) return null;

    let currentIndex = 0;
    const intervalId = setInterval(() => {
      currentIndex = (currentIndex + 1) % items.length;
      callback(currentIndex);
    }, intervalTime);

    return {
      setIndex: (newIndex) => { currentIndex = newIndex; },
      stop: () => clearInterval(intervalId)
    };
  }

  // --- 1. CAROUSEL CHÍNH ---
  const track = document.querySelector(".carousel-track");
  const dots = document.querySelectorAll(".dot");
  const mainSlides = document.querySelectorAll(".carousel-slide");

  if (track && mainSlides.length > 0) {
    const updateCarousel = (index) => {
      track.style.transform = `translateX(-${index * 100}%)`;
      dots.forEach((dot, i) => dot.classList.toggle("active", i === index));
    };

    const mainCarousel = createAutoCycle(".carousel-slide", 4000, updateCarousel);

    dots.forEach(dot => {
      dot.addEventListener("click", function () {
        const index = parseInt(this.dataset.index);
        mainCarousel.setIndex(index);
        updateCarousel(index);
      });
    });
  }

  // --- 2. SLIDER SẢN PHẨM ---
  const productSlides = document.querySelectorAll(".products-slide");
  if (productSlides.length > 0) {
    createAutoCycle(".products-slide", 5000, (index) => {
      productSlides.forEach((slide, i) => slide.classList.toggle("active", i === index));
    });
  }

  // --- 3. ĐỒNG HỒ ĐẾM NGƯỢC FLASH SALE ---
  const countdownContainer = {
    hours: document.getElementById("hours"),
    minutes: document.getElementById("minutes"),
    seconds: document.getElementById("seconds")
  };

  if (countdownContainer.hours && countdownContainer.minutes && countdownContainer.seconds) {
    const targetDate = new Date();
    targetDate.setHours(23, 59, 59);

    const updateCountdown = () => {
      const diff = targetDate.getTime() - new Date().getTime();
      if (diff <= 0) return;

      countdownContainer.hours.textContent = Math.floor(diff / 3600000).toString().padStart(2, "0");
      countdownContainer.minutes.textContent = Math.floor((diff % 3600000) / 60000).toString().padStart(2, "0");
      countdownContainer.seconds.textContent = Math.floor((diff % 60000) / 1000).toString().padStart(2, "0");
    };

    setInterval(updateCountdown, 1000);
    updateCountdown();
  }
});
