<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List Modal</title>
    <style>


        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-dialog {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            max-width: 50vw;
            width: 100%;
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
            transform: translateX(100%);
            transition: transform 0.4s ease-in-out;
        }

        .modal.show .modal-dialog {
            transform: translateX(0);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            background-color: #007bff;
            color: white;
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
            max-height: calc(100vh - 60px);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: white;
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.5s ease-in-out forwards;
        }

        .list-group-item:nth-child(1) { animation-delay: 0.2s; }
        .list-group-item:nth-child(2) { animation-delay: 0.4s; }
        .list-group-item:nth-child(3) { animation-delay: 0.6s; }

        .list-group-item img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 5px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 992px) {
            .modal-dialog {
                position: fixed;
                bottom: 0;
                width: 100%;
                height: 80vh;
                max-width: none;
                border-radius: 15px 15px 0 0;
                transform: translateY(100%);
            }

            .modal.show .modal-dialog {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>


    <div class="modal" id="productModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h5>Product List</h5>
                <button class="close-btn" onclick="closeModal()">✖</button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <img src="https://via.placeholder.com/50" alt="Product 1">
                        <div>
                            <h6>Product 1</h6>
                            <p>- High quality material</p>
                            <p>- Affordable price</p>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <img src="https://via.placeholder.com/50" alt="Product 2">
                        <div>
                            <h6>Product 2</h6>
                            <p>- Stylish design</p>
                            <p>- Durable and long-lasting</p>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <img src="https://via.placeholder.com/50" alt="Product 3">
                        <div>
                            <h6>Product 3</h6>
                            <p>- Lightweight and comfortable</p>
                            <p>- Available in multiple colors</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("productModal").classList.add("show");
        }

        function closeModal() {
            document.getElementById("productModal").classList.remove("show");
        }
    </script>

</body>
</html>


<section class="details__tab container">
    <div class="detail__tabs">
      <span class="detail__tab active-tab" data-target="#info">
        Additional Info
      </span>
      <span class="detail__tab" data-target="#reviews">Reviews(3)</span>
    </div>
    <div class="details__tabs-content">
      <div class="details__tab-content active-tab" content id="info">
        <table class="info__table">
          <tr>
            <th>Stand Up</th>
            <td>35" L x 24"W x 37-45"H(front to back wheel)</td>
          </tr>
          <tr>
            <th>Folded (w/o wheels)</th>
            <td>32.5"L x 18.5"W x 16.5"H</td>
          </tr>
          <tr>
            <th>Folded (w/o wheels)</th>
            <td>32.5"L x 24"W x 18.5"H</td>
          </tr>
          <tr>
            <th>Door Pass THrough</th>
            <td>24</td>
          </tr>
          <tr>
            <th>Frame</th>
            <td>Aluminum</td>
          </tr>
          <tr>
            <th>Weight (w/o wheels)</th>
            <td>20 LBS</td>
          </tr>
          <tr>
            <th>Weight Capacity</th>
            <td>60 LBS</td>
          </tr>
          <tr>
            <th>Width</th>
            <td>24</td>
          </tr>
          <tr>
            <th>Handle Height (ground to handle)</th>
            <td>37-45</td>
          </tr>
          <tr>
            <th>Wheels</th>
            <td>12" air / wide track slick tread</td>
          </tr>
          <tr>
            <th>Seat back height</th>
            <td>21.5</td>
          </tr>
          <tr>
            <th>Head Room(inside canopy)</th>
            <td>25"</td>
          </tr>
          <tr>
            <th>Color</th>
            <td>Black, Blue, Red, White</td>
          </tr>
          <tr>
            <th>Size</th>
            <td>M, S</td>
          </tr>
        </table>
      </div>
      <div class="details__tab-content" content id="reviews">
        <div class="reviews__container grid">
          <div class="review__single">
            <div>
              <img
                src="./assets/img/avatar-1.jpg"
                alt=""
                class="review__img" />
              <h4 class="review__title">Jacky Chan</h4>
            </div>
            <div class="review__data">
              <div class="review__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <p class="review__description">
                Thank you, very fast shipping from Poland only 3days.
              </p>
              <span class="review__date">December 4, 2022 at 3:12 pm</span>
            </div>
          </div>
          <div class="review__single">
            <div>
              <img
                src="./assets/img/avatar-2.jpg"
                alt=""
                class="review__img" />
              <h4 class="review__title">Meriem Js</h4>
            </div>
            <div class="review__data">
              <div class="review__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <p class="review__description">
                Great low price and works well
              </p>
              <span class="review__date">August 23, 2022 at 19:45 pm</span>
            </div>
          </div>
          <div class="review__single">
            <div>
              <img
                src="./assets/img/avatar-3.jpg"
                alt=""
                class="review__img" />
              <h4 class="review__title">Moh Benz</h4>
            </div>
            <div class="review__data">
              <div class="review__rating">
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
                <i class="fi fi-rs-star"></i>
              </div>
              <p class="review__description">
                Authentic and beautiful, Love these ways more than ever
                expected, They are great earphones.
              </p>
              <span class="review__date">March 2, 2021 at 10:01 am</span>
            </div>
          </div>
        </div>
        <div class="review__form">
          <h4 class="review__form-title">Add a review</h4>
          <div class="rate__product">
            <i class="fi fi-rs-star"></i>
            <i class="fi fi-rs-star"></i>
            <i class="fi fi-rs-star"></i>
            <i class="fi fi-rs-star"></i>
            <i class="fi fi-rs-star"></i>
          </div>
          <form action="" class="form grid">
            <textarea
              class="form__input textarea"
              placeholder="Write Comment"></textarea>
            <div class="form__group grid">
              <input type="text" placeholder="Name" class="form__input">
              <input type="email" placeholder="Email" class="form__input">
            </div>
            <div class="form__btn">
              <button class="btn">Submit Review</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>