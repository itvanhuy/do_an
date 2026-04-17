/**
 * TECHSHOP - Main JavaScript Bundle
 * Gộp các file: product.js, home.js, tournament.js, detaiproduct.js, shop.js
 */

// 1. CÁC HÀM GLOBAL (Dùng cho thuộc tính onclick trong HTML)
window.changeImage = function (imgElement) {
    const mainImg = document.getElementById("mainImage");
    if (mainImg) mainImg.src = imgElement.src;
};

document.addEventListener("DOMContentLoaded", function () {

    // --- OPTIMIZATION: Lazy Loading Images ---
    // Tự động thêm loading="lazy" cho tất cả ảnh để giảm lag khi tải trang
    document.querySelectorAll('img').forEach(img => {
        if (!img.hasAttribute('loading')) {
            img.setAttribute('loading', 'lazy');
        }
        img.setAttribute('loading', 'lazy');
        
        // Xử lý khi ảnh bị lỗi (không tìm thấy file nén)
        img.onerror = function() {
            if (this.src.endsWith('.webp')) {
                // Nếu file webp không tồn tại, thử quay lại file gốc (jpg)
                this.src = this.src.replace('.webp', '.jpg');
            } else {
                this.src = '/img/default-placeholder.png';
            }
        };
    });

    // --- OPTIMIZATION: Debounce function ---
    // Tránh việc thực thi hàm quá nhiều lần liên tục (như khi cuộn trang)
    function debounce(func, wait = 20) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // --- HELPER: Tạo bộ tự động chuyển đổi (Slider/Carousel) ---
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

    // --- 1. CHỨC NĂNG CHUNG (Dùng cho nhiều trang) ---
    // Nút thêm vào giỏ hàng (Từ product.js)
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function () {
            const originalText = this.innerText;
            this.innerText = 'Đang xử lý...';
            this.disabled = true;

            setTimeout(() => {
                alert('Vui lòng đăng nhập để thực hiện chức năng này!');
                this.innerText = originalText;
                this.disabled = false;
            }, 500);
        });
    });


    // --- 2. TRANG CHỦ - HERO SLIDER (Từ home.js) ---
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        const images = heroSlider.querySelectorAll('img');
        const heading = document.querySelector('.hero-content h1');
        const paragraph = document.querySelector('.hero-content p');
        const slideText = [
            { title: "Join Exciting Esports Tournaments", description: "Compete together in major esports tournaments, with incredibly attractive prizes." },
            { title: "Discover the Latest Gaming Products!", description: "Professional gaming devices for all gamers, ready to conquer any hot game title." },
            { title: "Welcome to TECHSHOP", description: "The World of Technology & Esports." }
        ];

        if (images.length > 0) {
            let heroIndex = 0;
            const updateHero = (index) => {
                images.forEach(img => img.classList.remove('active'));
                images[index].classList.add('active');
                if (heading) heading.textContent = slideText[index].title;
                if (paragraph) paragraph.textContent = slideText[index].description;
            };

            setInterval(() => {
                heroIndex = (heroIndex + 1) % images.length;
                updateHero(heroIndex);
            }, 4000);
        }
    }


    // --- 3. TRANG CỬA HÀNG (Từ shop.js) ---
    // Carousel chính trang Shop
    const track = document.querySelector(".carousel-track");
    const dots = document.querySelectorAll(".dot");
    if (track && document.querySelectorAll(".carousel-slide").length > 0) {
        const updateShopCarousel = (index) => {
            track.style.transform = `translateX(-${index * 100}%)`;
            dots.forEach((dot, i) => dot.classList.toggle("active", i === index));
        };
        const shopCarousel = createAutoCycle(".carousel-slide", 4000, updateShopCarousel);
        dots.forEach(dot => {
            dot.addEventListener("click", function () {
                const index = parseInt(this.dataset.index);
                shopCarousel.setIndex(index);
                updateShopCarousel(index);
            });
        });
    }

    // Slider sản phẩm trang Shop
    const productSlides = document.querySelectorAll(".products-slide");
    if (productSlides.length > 0) {
        createAutoCycle(".products-slide", 5000, (index) => {
            productSlides.forEach((slide, i) => slide.classList.toggle("active", i === index));
        });
    }

    // Countdown Flash Sale
    const hoursEl = document.getElementById("hours");
    if (hoursEl) { // Chỉ chạy nếu tìm thấy ID hours
        const targetDate = new Date();
        targetDate.setHours(23, 59, 59);
        const updateCountdown = () => {
            const diff = targetDate.getTime() - new Date().getTime();
            if (diff <= 0) return;
            document.getElementById("hours").textContent = Math.floor(diff / 3600000).toString().padStart(2, "0");
            document.getElementById("minutes").textContent = Math.floor((diff % 3600000) / 60000).toString().padStart(2, "0");
            document.getElementById("seconds").textContent = Math.floor((diff % 60000) / 1000).toString().padStart(2, "0");
        };
        setInterval(updateCountdown, 1000);
        updateCountdown();
    }


    // --- 4. TRANG GIẢI ĐẤU (Từ tournament.js) ---
    const filterBtns = document.querySelectorAll('.filter-btn');
    if (filterBtns.length > 0) {
        const tournamentSections = document.querySelectorAll('.tournament-section');
        const rankingsSection = document.querySelector('.rankings-section');
        const rankingsTitle = document.querySelector('.rankings-section h2');

        const updateTournamentDisplay = (filter) => {
            if (rankingsTitle) {
                rankingsTitle.innerHTML = filter === 'all'
                    ? '<i class="fas fa-list-ul"></i> All Tournaments'
                    : '<i class="fas fa-trophy"></i> Global Team Rankings';
            }

            if (filter === 'all') {
                if (rankingsSection) rankingsSection.style.display = 'block';
                tournamentSections.forEach(s => s.style.display = 'block');
            } else if (filter === 'rankings_only') {
                if (rankingsSection) rankingsSection.style.display = 'block';
                tournamentSections.forEach(s => s.style.display = 'none');
            } else {
                if (rankingsSection) rankingsSection.style.display = 'none';
                tournamentSections.forEach(s => s.style.display = s.getAttribute('data-id') === filter ? 'block' : 'none');
            }
        };

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                updateTournamentDisplay(this.getAttribute('data-filter'));
            });
        });

        // Khởi tạo trạng thái ban đầu
        const activeBtn = document.querySelector('.filter-btn.active');
        if (activeBtn) updateTournamentDisplay(activeBtn.getAttribute('data-filter'));
    }

    // Toggle Score (Tournament match)
    document.querySelectorAll('.toggle-score').forEach(btn => {
        btn.addEventListener('click', function () {
            const matchDetails = this.closest('.match-details');
            if (matchDetails) {
                const score = matchDetails.querySelector('.score');
                if (score) {
                    const isHidden = score.style.display === 'none' || score.style.display === '';
                    score.style.display = isHidden ? 'inline-block' : 'none';
                }
            }
        });
    });

});