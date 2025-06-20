<?php include('include/header.php') ?>
  <body>
    <!--=============== HEADER ===============-->
    <?php include('include/navbar.php') ?>
    <!--=============== MAIN ===============-->
    <main class="main">
      <div class="blank"><br></div>
      <!--=============== BREADCRUMB ===============-->
      <section class="breadcrumb">
        <ul class="breadcrumb__list flex container">
          <li><a href="index.php" class="breadcrumb__link">Home</a></li>
          <li><span class="breadcrumb__link">></span></li>
          <li><a href="shop.php" class="breadcrumb__link">Shop</a></li>
          <li><span class="breadcrumb__link">></span></li>
          <li><span class="breadcrumb__link">Compare</span></li>
        </ul>
      </section>

      <!--=============== COMPARE ===============-->
      <section class="compare container section--lg">
        <table class="compare__table">
          <tr>
            <th>Image</th>
            <td>
              <img
                src="./assets/img/product-2-1.jpg"
                alt=""
                class="compare__img"
              />
            </td>
            <td>
              <img
                src="./assets/img/product-4-1.jpg"
                alt=""
                class="compare__img"
              />
            </td>
            <td>
              <img
                src="./assets/img/product-7-1.jpg"
                alt=""
                class="compare__img"
              />
            </td>
          </tr>
          <tr>
            <th>Name</th>
            <td><h3 class="table__title">Plain Striola Shirts</h3></td>
            <td><h3 class="table__title">Chen Cardigan</h3></td>
            <td><h3 class="table__title">Henley Shirt</h3></td>
          </tr>
          <tr>
            <th>Price</th>
            <td><span class="table__price">$35</span></td>
            <td><span class="table__price">$67</span></td>
            <td><span class="table__price">$24</span></td>
          </tr>
          <tr>
            <th>Description</th>
            <td>
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis
                perferendis nam, fuga reiciendis libero doloremque distinctio.
              </p>
            </td>
            <td>
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis
                perferendis nam, fuga reiciendis libero doloremque distinctio.
              </p>
            </td>
            <td>
              <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Omnis
                perferendis nam, fuga reiciendis libero doloremque distinctio.
              </p>
            </td>
          </tr>
          <tr>
            <th>Colors</th>
            <td>
              <ul class="color__list compare__colors">
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(37, 100%, 65%)"
                  ></a>
                </li>
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(353, 100%, 65%)"
                  ></a>
                </li>
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(49, 100%, 60%)"
                  ></a>
                </li>
              </ul>
            </td>
            <td>
              <ul class="color__list compare__colors">
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(37, 100%, 65%)"
                  ></a>
                </li>
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(353, 100%, 65%)"
                  ></a>
                </li>
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(49, 100%, 60%)"
                  ></a>
                </li>
              </ul>
            </td>
            <td>
              <ul class="color__list compare__colors">
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(37, 100%, 65%)"
                  ></a>
                </li>
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(353, 100%, 65%)"
                  ></a>
                </li>
                <li>
                  <a
                    href="#"
                    class="color__link"
                    style="background-color: hsl(49, 100%, 60%)"
                  ></a>
                </li>
              </ul>
            </td>
          </tr>
          <tr>
            <th>Stock</th>
            <td><span class="table__stock">Out of stock</span></td>
            <td><span class="table__stock">Out of stock</span></td>
            <td><span class="table__stock">Out of stock</span></td>
          </tr>
          <tr>
            <th>Weight</th>
            <td><span class="table__weight">150 gram</span></td>
            <td><span class="table__weight">150 gram</span></td>
            <td><span class="table__weight">150 gram</span></td>
          </tr>
          <tr>
            <th>Dimensions</th>
            <td><span class="table__dimension">N/A</span></td>
            <td><span class="table__dimension">N/A</span></td>
            <td><span class="table__dimension">N/A</span></td>
          </tr>
          <tr>
            <th>Buy</th>
            <td><a href="#" class="btn btm--sm">Add to Cart</a></td>
            <td><a href="#" class="btn btm--sm">Add to Cart</a></td>
            <td><a href="#" class="btn btm--sm">Add to Cart</a></td>
          </tr>
          <tr>
            <th>Remove</th>
            <td><i class="fi fi-rs-trash table__trash"></i></td>
            <td><i class="fi fi-rs-trash table__trash"></i></td>
            <td><i class="fi fi-rs-trash table__trash"></i></td>
          </tr>
        </table>
      </section>

      <!--=============== NEWSLETTER ===============-->
      <?php include('include/news.php') ?>
    </main>

    <!--=============== FOOTER ===============-->
    <?php include('include/footer.php') ?>
  </body>
</html>
