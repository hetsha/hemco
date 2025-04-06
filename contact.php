<?php include('include/header.php') ?>
<style>
  /* Contact Form Styling */
.contact {
  padding: 2rem 1rem;
  border-radius: 12px;
  margin-top: 2rem;
}

.contact__content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  margin-top: 2rem;
  width: 100%;
}

.contact__form,
.contact__info {
  background-color: #ffffff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
}

.form__group {
  margin-bottom: 1.5rem;
}

.form__group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
}

.form__group input,
.form__group textarea {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 1rem;
  transition: border 0.3s ease;
}

.form__group input:focus,
.form__group textarea:focus {
  outline: none;
  border-color: #007bff;
}

.button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.button:hover {
  background-color: #0056b3;
}

.contact__info h3 {
  margin-bottom: 1rem;
  color: #007bff;
}

.contact__info p {
  margin-bottom: 0.8rem;
  color: #555;
}

.contact__map {
  margin-top: 1.5rem;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.contact__map iframe {
  width: 100%;
  height: 300px;
  border: 0;
}

/* Responsive */
@media screen and (max-width: 768px) {
  .contact__content {
    grid-template-columns: 1fr;
  }

  .contact__form,
  .contact__info {
    padding: 1.5rem;
  }

  .button {
    width: 100%;
    padding: 1rem;
  }
}
@media screen and (max-width: 480px) {
  .contact__map iframe {
    height: 200px;
  }
}

</style>

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
        <li><span class="breadcrumb__link">Contact Us</span></li>
      </ul>
    </section>

    <!--=============== CONTACT US ===============-->
    <section class="products container section--lg">
      <h2 class="section__title">Get in Touch</h2>
      <p class="section__subtitle">We’d love to hear from you. Fill out the form below to reach us.</p>

      <div class="contact__content ">
        <form action="contact_submit.php" method="POST" class="contact__form">
          <div class="form__group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required placeholder="Your Name">
          </div>

          <div class="form__group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required placeholder="Your Email">
          </div>

          <div class="form__group">
            <label for="message">Message</label>
            <textarea name="message" id="message" rows="5" required placeholder="Your Message"></textarea>
          </div>

          <button type="submit" class="button">Send Message</button>
        </form>

        <div class="contact__info">
          <h3>Contact Information</h3>
          <p><strong>Address:</strong> Your Office Address, City, State</p>
          <p><strong>Email:</strong> support@example.com</p>
          <p><strong>Phone:</strong> +91 98765 43210</p>
          <div class="contact__map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3672.364469747031!2d72.5625445!3d23.0103863!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e8505c38b6e5d%3A0xc1c15dd556d5bf11!2sHEMCO%20OPTICAL!5e0!3m2!1sen!2sin!4v1743922084163!5m2!1sen!2sin"
              width="600" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </section>

    <!--=============== NEWSLETTER ===============-->
    <?php include('include/news.php') ?>
  </main>

  <!--=============== FOOTER ===============-->
  <?php include("include/footer.php") ?>
</body>

</html>