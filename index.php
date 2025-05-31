<?php include('include/header.php') ?>
  <body>
    <?php include('include/navbar.php') ?>
    <!--=============== MAIN ===============-->
    <main class="main">

      <!--=============== HOME ===============-->

      <section class="home section--lg">
        <?php include('include/slider.php') ?>
      </section>

      <?php include('include/cat.php') ?>
      <!--=============== PRODUCTS ===============-->
      <?php include('include/pro.php') ?>

      <!--=============== DEALS ===============-->

      <?php //include('include/deals.php') ?>

      <!--=============== NEW ARRIVALS ===============-->
      <?php include('include/product.php') ?>

      <!--=============== SHOWCASE ===============-->
      <?php //include('include/show.php') ?>

      <!--=============== ABOUT / STORY SECTION ===============-->
      <section class="about-story-section container section" style="margin-top: 3.5rem; margin-bottom: 3.5rem;">
        <div class="about-story-wrapper" style="display: flex; flex-wrap: wrap; align-items: center; gap: 2.5rem;">
          <div class="about-story-image" style="flex: 1 1 320px; min-width: 280px; max-width: 480px;">
            <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&w=600&q=80" alt="Our Eyewear Story" style="width: 100%; border-radius: 1.5rem; box-shadow: 0 8px 32px #0002; object-fit: cover;">
          </div>
          <div class="about-story-content" style="flex: 2 1 400px; min-width: 280px;">
            <h2 style="font-size: 2.2rem; font-weight: 800; color: #1e293b; margin-bottom: 1.1rem;">Our Story</h2>
            <p style="font-size: 1.18rem; color: #334155; margin-bottom: 1.2rem; line-height: 1.7;">
              Founded with a passion for vision and style, <b>Hemco Optical</b> has been helping people see the world more clearly and confidently for over a decade. Our journey began with a simple belief: everyone deserves high-quality, fashionable eyewear at a fair price.<br><br>
              From classic frames to the latest trends, we handpick every collection to ensure comfort, durability, and a look you'll love. Our team is dedicated to providing expert advice, personalized service, and a seamless shopping experience—whether you're buying your first pair or adding to your collection.
            </p>
            <p style="font-size: 1.08rem; color: #64748b; margin-bottom: 0.7rem;">
              We believe eyewear is more than just vision correction—it's a statement of who you are. Thank you for making us part of your story.
            </p>
            <a href="shop.php" class="banner-btn" style="margin-top: 1.2rem;">Explore Our Collection</a>
          </div>
        </div>
      </section>

      <!--=============== NEWSLETTER ===============-->
      <?php include('include/news.php') ?>

    </main>

    <!--=============== FOOTER ===============-->
    <?php include('include/footer.php') ?>

    <!-- Offer Popup Modal -->
    <!--
    <style>
      #offerModal {
        animation: fadeIn 0.5s;
        display: flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        inset: 0;
        z-index: 50;
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(2px);
      }
      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
      }
      #offerModal .offer-content {
        box-shadow: 0 12px 48px #1e293b33, 0 1.5px 0 #a5b4fc;
        border-radius: 2.2rem;
        border: 2.5px solid #c7d2fe;
        background: linear-gradient(135deg, #fff 80%, #e0e7ff 100%);
        position: relative;
        animation: popIn 0.7s cubic-bezier(.68,-0.55,.27,1.55);
        margin: 0;
        padding: 2.7rem 2.2rem 2.3rem 2.2rem;
        max-width: 500px;
        width: 97vw;
        overflow: visible;
      }
      @keyframes popIn {
        0% { transform: scale(0.7); opacity: 0; }
        80% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); }
      }
      /* Sparkle Animation */
      .sparkle {
        position: absolute;
        pointer-events: none;
        width: 18px; height: 18px;
        background: radial-gradient(circle, #facc15 60%, #fff0 100%);
        opacity: 0.7;
        border-radius: 50%;
        animation: sparkleMove 1.7s linear infinite;
      }
      @keyframes sparkleMove {
        0% { opacity: 0; transform: scale(0.7) translateY(0); }
        30% { opacity: 1; }
        100% { opacity: 0; transform: scale(1.2) translateY(-60px); }
      }
      #offerModal .offer-content img {
        border-radius: 1.7rem;
        box-shadow: 0 2px 16px #2563eb22;
        background: #f3f4f6;
        width: 210px;
        height: 130px;
        object-fit: cover;
        margin-bottom: 1.5rem;
        border: 2px solid #c7d2fe;
      }
      #offerModal .offer-content h2 {
        letter-spacing: 0.01em;
        font-size: 2.1rem;
        margin-bottom: 0.5rem;
        color: #1e293b;
        font-family: 'Montserrat',sans-serif;
      }
      #offerModal .offer-content p {
        font-size: 1.18em;
        margin-bottom: 1.5rem;
        color: #334155;
      }
      #offerModal .offer-content a {
        font-weight: 700;
        font-size: 1.13em;
        box-shadow: 0 2px 8px #2563eb22;
        padding: 0.95rem 2.4rem;
        border-radius: 1.2rem;
        background: linear-gradient(90deg, #6366f1 0%, #2563eb 100%);
        color: #fff;
        transition: transform 0.18s, box-shadow 0.18s, background 0.18s;
        display: inline-flex;
        align-items: center;
        gap: 0.6em;
        border: none;
        outline: none;
        text-decoration: none;
      }
      #offerModal .offer-content a:hover {
        background: linear-gradient(90deg, #2563eb 0%, #6366f1 100%);
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 4px 16px #6366f144;
      }
      #closeOfferModal {
        top: 0.7rem;
        right: 0.7rem;
        font-size: 2.1rem;
        background: #f1f5f9;
        border: none;
        cursor: pointer;
        z-index: 10;
        border-radius: 50%;
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px #2563eb22;
      }
      #closeOfferModal:hover {
        background: #f87171;
        color: #fff;
        box-shadow: 0 4px 16px #f8717144;
      }
      .dont-show-row {
        display: flex;
        align-items: center;
        gap: 0.5em;
        margin-top: 0.7em;
        font-size: 0.98em;
        color: #64748b;
        justify-content: flex-start;
      }
      .dont-show-row input[type="checkbox"] {
        accent-color: #6366f1;
        width: 1.1em;
        height: 1.1em;
      }
      @media (max-width: 600px) {
        #offerModal .offer-content {
          padding: 1.2rem 0.3rem 1.2rem 0.3rem;
          max-width: 99vw;
        }
        #offerModal .offer-content img {
          width: 110px;
          height: 70px;
        }
        #offerModal .offer-content h2 {
          font-size: 1.2rem;
        }
        #offerModal .offer-content p {
          font-size: 1em;
        }
        #offerModal .offer-content a {
          font-size: 1em;
          padding: 0.7rem 1.2rem;
        }
      }
    </style>
    <div id="offerModal" style="display:none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40" role="dialog" aria-modal="true" aria-labelledby="offerTitle">
      <div class="offer-content" tabindex="-1">
        <button id="closeOfferModal" aria-label="Close offer popup">&times;</button>
        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=facearea&w=600&q=80" alt="Eyewear Offer" />
        <h2 id="offerTitle" class="text-2xl font-bold mb-2 text-blue-700">Special Offer!</h2>
        <p class="mb-4 text-gray-700">Get <span class="text-red-500 font-bold">20% OFF</span> on all Eyeglass Frames.<br>Use code: <span class="font-mono bg-blue-100 px-2 py-1 rounded">FRAME20</span></p>
        <a href="shop.php?category=eyeglasses" class="inline-block bg-blue-600 text-white rounded hover:bg-blue-700 transition" id="shopNowBtn">
          <svg width="22" height="22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 11h8m0 0-3-3m3 3-3 3" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Shop Now
        </a>
        <div class="dont-show-row">
          <input type="checkbox" id="dontShowOffer" />
          <label for="dontShowOffer">Don't show again</label>
        </div>
        <!-- Sparkles -->
        <span class="sparkle" style="top:18px; left:30px; animation-delay:0s;"></span>
        <span class="sparkle" style="top:60px; right:40px; animation-delay:0.5s;"></span>
        <span class="sparkle" style="bottom:30px; left:60px; animation-delay:1s;"></span>
        <span class="sparkle" style="bottom:40px; right:30px; animation-delay=1.3s;"></span>
      </div>
    </div>
    <script>
      // Accessibility: focus trap
      function trapFocus(element) {
        var focusableEls = element.querySelectorAll('a, button, input, [tabindex]:not([tabindex="-1"])');
        var firstFocusableEl = focusableEls[0];
        var lastFocusableEl = focusableEls[focusableEls.length - 1];
        element.addEventListener('keydown', function(e) {
          if (e.key === 'Tab') {
            if (e.shiftKey) {
              if (document.activeElement === firstFocusableEl) {
                e.preventDefault();
                lastFocusableEl.focus();
              }
            } else {
              if (document.activeElement === lastFocusableEl) {
                e.preventDefault();
                firstFocusableEl.focus();
              }
            }
          }
        });
      }
      window.addEventListener('DOMContentLoaded', function() {
        var offerModal = document.getElementById('offerModal');
        var closeBtn = document.getElementById('closeOfferModal');
        var dontShow = document.getElementById('dontShowOffer');
        var shopNowBtn = document.getElementById('shopNowBtn');
        // Show modal only if not dismissed
        if (!localStorage.getItem('hideOfferModal')) {
          offerModal.style.display = 'flex';
          setTimeout(function() {
            offerModal.querySelector('.offer-content').focus();
          }, 200);
          trapFocus(offerModal.querySelector('.offer-content'));
        }
        closeBtn.onclick = function() {
          offerModal.style.display = 'none';
          if (dontShow.checked) {
            localStorage.setItem('hideOfferModal', '1');
          }
        };
        dontShow.onchange = function() {
          if (dontShow.checked) {
            localStorage.setItem('hideOfferModal', '1');
          } else {
            localStorage.removeItem('hideOfferModal');
          }
        };
        // Optional: close on outside click
        offerModal.onclick = function(e) {
          if (e.target === offerModal) offerModal.style.display = 'none';
        };
        // Animate Shop Now button
        shopNowBtn.onmouseover = function() {
          shopNowBtn.style.transform = 'scale(1.06)';
        };
        shopNowBtn.onmouseout = function() {
          shopNowBtn.style.transform = '';
        };
      });
    </script>
  </body>
</html>
