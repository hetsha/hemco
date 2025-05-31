<div class="banner-slider">
  <div class="banner-slide" style="background-image: url('https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=1200&q=80');">
    <div class="banner-content">
      <h1 class="banner-title">Discover Your Perfect Eyewear</h1>
      <p class="banner-subtitle">Trendy, Comfortable, and Affordable Glasses for Everyone</p>
      <a href="shop.php" class="banner-btn">Shop Now</a>
    </div>
  </div>
  <div class="banner-slide" style="background-image: url('https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=1200&q=80');">
    <div class="banner-content">
      <h1 class="banner-title">Summer Sunglasses Collection</h1>
      <p class="banner-subtitle">Protect Your Eyes in Style – Explore Our Latest Arrivals</p>
      <a href="shop.php?category=sunglasses" class="banner-btn">View Sunglasses</a>
    </div>
  </div>
  <div class="banner-slide" style="background-image: url('https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=1200&q=80');">
    <div class="banner-content">
      <h1 class="banner-title">Blue Light Protection</h1>
      <p class="banner-subtitle">Computer Glasses for Work & Play – Shop Now</p>
      <a href="shop.php?category=computer" class="banner-btn">Computer Glasses</a>
    </div>
  </div>
  <button class="banner-nav banner-nav-left" aria-label="Previous slide">&#10094;</button>
  <button class="banner-nav banner-nav-right" aria-label="Next slide">&#10095;</button>
</div>
<style>
.banner-slider {
  position: relative;
  width: 100%;
  min-height: 480px;
  max-height: 650px;
  overflow: hidden;
}
.banner-slide {
  width: 100%;
  min-height: 480px;
  max-height: 630px;
  background-size: cover;
  background-position: center;
  display: none;
  align-items: center;
  justify-content: flex-start;
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  opacity: 0;
  z-index: 1;
  transition: opacity 1s cubic-bezier(.4,0,.2,1), transform 1s cubic-bezier(.4,0,.2,1);
  transform: scale(1.04) translateX(40px);
  box-shadow: 0 12px 48px #0003, 0 1.5px 8px #2563eb22;
  filter: brightness(0.98) saturate(1.08);
  /* border-radius: 2.2rem; */
  overflow: hidden;
}
.banner-slide.slide-in-left {
  animation: slideInLeft 0.8s cubic-bezier(.4,0,.2,1);
}
.banner-slide.slide-in-right {
  animation: slideInRight 0.8s cubic-bezier(.4,0,.2,1);
}
@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: scale(1.04) translateX(100vw);
  }
  to {
    opacity: 1;
    transform: scale(1) translateX(0);
  }
}
@keyframes slideInRight {
  from {
    opacity: 0;
    transform: scale(1.04) translateX(-100vw);
  }
  to {
    opacity: 1;
    transform: scale(1) translateX(0);
  }
}
.banner-slide.active {
  display: flex;
  opacity: 1;
  z-index: 2;
  transform: scale(1) translateX(0);
  animation: fadeIn 0.7s cubic-bezier(.4,0,.2,1);
}
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.banner-content {
  background: rgba(255,255,255,0.82);
  border-radius: 1.5rem;
  padding: 2.5rem 2.5rem 2.2rem 2.5rem;
  margin-left: 5vw;
  box-shadow: 0 8px 32px #0002;
  max-width: 480px;
  min-width: 270px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  animation: contentPopIn 0.8s cubic-bezier(.4,0,.2,1);
}
@keyframes contentPopIn {
  0% {
    opacity: 0;
    transform: translateY(40px) scale(0.98);
  }
  60% {
    opacity: 1;
    transform: translateY(-8px) scale(1.03);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
.banner-title {
  font-size: 2.5rem;
  font-weight: 800;
  color: #1e293b;
  margin-bottom: 0.7rem;
  line-height: 1.1;
}
.banner-subtitle {
  font-size: 1.25rem;
  color: #334155;
  margin-bottom: 1.5rem;
  font-weight: 500;
}
.banner-btn {
  background: linear-gradient(90deg, #6366f1 0%, #2563eb 100%);
  color: #fff;
  font-weight: 700;
  font-size: 1.1rem;
  padding: 0.9rem 2.2rem;
  border-radius: 1.2rem;
  text-decoration: none;
  box-shadow: 0 4px 16px #6366f133, 0 1.5px 8px #2563eb22;
  transition: background 0.2s, transform 0.2s;
  display: inline-block;
  letter-spacing: 0.03em;
  animation: btnPulse 2.5s infinite cubic-bezier(.4,0,.2,1);
}
@keyframes btnPulse {
  0%, 100% { box-shadow: 0 4px 16px #6366f133, 0 1.5px 8px #2563eb22; }
  50% { box-shadow: 0 8px 32px #6366f155, 0 2.5px 12px #2563eb33; }
}
.banner-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255,255,255,0.85);
  border: none;
  color: #2563eb;
  font-size: 2.5rem;
  border-radius: 50%;
  width: 54px;
  height: 54px;
  cursor: pointer;
  z-index: 10;
  box-shadow: 0 2px 8px #2563eb22;
  transition: background 0.2s, color 0.2s, box-shadow 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.85;
  animation: navFadeIn 1.2s cubic-bezier(.4,0,.2,1);
}
@keyframes navFadeIn {
  from { opacity: 0; transform: scale(0.7); }
  to { opacity: 0.85; transform: scale(1); }
}
.banner-nav:hover {
  background: #2563eb;
  color: #fff;
  opacity: 1;
}
.banner-nav-left {
  left: 2vw;
}
.banner-nav-right {
  right: 2vw;
}
@media (max-width: 700px) {
  .banner-content {
    padding: 1.2rem 0.7rem 1.2rem 0.7rem;
    margin-left: 0.5rem;
    max-width: 95vw;
  }
  .banner-title {
    font-size: 1.3rem;
  }
  .banner-subtitle {
    font-size: 1rem;
  }
  .banner-btn {
    font-size: 1rem;
    padding: 0.7rem 1.2rem;
  }
  .banner-slider, .banner-slide {
    min-height: 320px;
    max-height: 420px;
  }
  .banner-nav {
    font-size: 1.5rem;
    width: 36px;
    height: 36px;
  }
}
</style>
<script>
(function() {
  var slides = document.querySelectorAll('.banner-slide');
  var leftBtn = document.querySelector('.banner-nav-left');
  var rightBtn = document.querySelector('.banner-nav-right');
  var current = 0;
  var timer;
  function showSlide(idx, direction) {
    slides.forEach(function(slide, i) {
      slide.classList.remove('slide-in-left', 'slide-in-right', 'active');
      if (i === idx) {
        slide.classList.add('active');
        if (typeof direction === 'string') {
          slide.classList.add(direction === 'left' ? 'slide-in-left' : 'slide-in-right');
        }
        // Animate content
        var content = slide.querySelector('.banner-content');
        if(content) {
          content.style.animation = 'none';
          // Force reflow
          void content.offsetWidth;
          content.style.animation = null;
        }
      }
    });
  }
  function nextSlide() {
    var prev = current;
    current = (current + 1) % slides.length;
    showSlide(current, 'left');
  }
  function prevSlide() {
    var prev = current;
    current = (current - 1 + slides.length) % slides.length;
    showSlide(current, 'right');
  }
  function resetTimer() {
    clearInterval(timer);
    timer = setInterval(nextSlide, 5000);
  }
  leftBtn.addEventListener('click', function() {
    prevSlide();
    resetTimer();
  });
  rightBtn.addEventListener('click', function() {
    nextSlide();
    resetTimer();
  });
  showSlide(current);
  timer = setInterval(nextSlide, 5000);
})();
</script>

<!-- Eyewear Brands Section -->
<div class="brands-section" style="margin: 60px 0 0 0;">
  <h2 class="brands-title">Our Eyewear Brands</h2>
  <div class="brands-grid">
    <div class="brand-box"><img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Swatch_Logo.svg" alt="Swatch"></div>
    <div class="brand-box"><img src="https://upload.wikimedia.org/wikipedia/commons/6/6a/Emporio_Armani_logo.svg" alt="Emporio Armani"></div>
    <div class="brand-box"><span class="brand-text">Jacques Lemans</span></div>
    <div class="brand-box"><span class="brand-text">Citizen</span></div>
    <div class="brand-box"><span class="brand-text">Zeppelin</span></div>
    <div class="brand-box"><span class="brand-text">Rolex</span></div>
    <div class="brand-box"><span class="brand-text">Lee Cooper</span></div>
    <div class="brand-box"><span class="brand-text">Pierre Cardin</span></div>
    <div class="brand-box"><span class="brand-text">Gant</span></div>
    <div class="brand-box"><span class="brand-text">Guess</span></div>
    <div class="brand-box"><span class="brand-text">Esprit</span></div>
    <div class="brand-box"><span class="brand-text">Adora</span></div>
    <div class="brand-box"><span class="brand-text">Danish Design</span></div>
    <div class="brand-box"><span class="brand-text">Quantum</span></div>
    <div class="brand-box"><span class="brand-text">Originals</span></div>
  </div>
</div>
<style>
.brands-section {
  width: 100%;
  text-align: center;
}
.brands-title {
  font-size: 2.1rem;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 2.2rem;
  letter-spacing: 0.01em;
}
.brands-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 2.2rem 2.5rem;
  max-width: 1200px;
  margin: 0 auto;
}
.brand-box {
  background: #f5f5f7;
  border-radius: 0.7rem;
  box-shadow: 0 4px 18px #0001, 0 1.5px 8px #2563eb11;
  padding: 1.2rem 2.5rem;
  min-width: 180px;
  min-height: 70px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: box-shadow 0.2s, transform 0.2s;
  cursor: pointer;
}
.brand-box:hover {
  box-shadow: 0 8px 32px #6366f133, 0 2.5px 12px #2563eb22;
  transform: translateY(-4px) scale(1.04);
}
.brand-box img {
  max-width: 140px;
  max-height: 48px;
  object-fit: contain;
  filter: grayscale(0.2) contrast(1.1);
}
.brand-text {
  font-size: 1.25rem;
  font-weight: 600;
  color: #22223b;
  letter-spacing: 0.02em;
  font-family: 'Segoe UI', 'Arial', sans-serif;
}
@media (max-width: 900px) {
  .brands-grid {
    gap: 1.2rem 1.2rem;
  }
  .brand-box {
    min-width: 120px;
    padding: 0.7rem 1.2rem;
  }
  .brand-box img {
    max-width: 90px;
    max-height: 32px;
  }
}
</style>