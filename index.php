<?php
session_start();
require 'db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>HungerHub - Order Delicious Food Online</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- AOS Animation -->
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css" />
  <link href="images/logo.png" rel="icon" type="image/png">
</head>

<body>

  <?php include 'includes/navbar.php'; ?>

  <!-- Carousel Start -->
  <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="0" class="active"></button>
      <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="1"></button>
      <button type="button" data-bs-target="#homeCarousel" data-bs-slide-to="2"></button>
    </div>

    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="images/slide1.png" class="d-block w-100" alt="Delicious Dosa" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/slide1.png';" />
        <div class="carousel-caption">
          <h2 class="fw-bold text-shadow">Crispy Dosa with Flavorful Chutneys</h2>
          <p>Delivered fresh and crispy right to your doorstep.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="images/slide2.png" class="d-block w-100" alt="Burger & Fries" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/slide2.png';" />
        <div class="carousel-caption">
          <h2 class="fw-bold text-shadow">Juicy Burgers & Crispy Fries</h2>
          <p>The ultimate comfort combo everyone loves.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="images/slide3.png" class="d-block w-100" alt="Biryani Special" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/slide3.png';" />
        <div class="carousel-caption">
          <h2 class="fw-bold text-shadow">Authentic Royal Biryani Flavors</h2>
          <p>Slow-cooked with aromatic spices, every single time.</p>
        </div>
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#homeCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#homeCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>
  <!-- Carousel End -->

  <!-- About Section Start -->
  <section class="py-5 bg-light" id="about">
    <div class="container">

      <!-- Section Heading -->
      <div class="text-center mb-5" data-aos="fade-up">
        <h1 class="fw-bold mb-3">About <span class="text-warning">HungerHub</span></h1>
        <p class="lead text-muted mx-auto" style="max-width: 680px;">HungerHub is your one-stop online destination for ordering delicious, restaurant-quality food anytime, anywhere.</p>
      </div>

      <!-- Row: Image + Intro -->
      <div class="row align-items-center mb-5 g-4">
        <div class="col-md-6 text-center" data-aos="fade-up">
          <img src="images/about_us.png" alt="About HungerHub" class="img-fluid rounded-4 shadow" id="about_img" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/about_us.png';" />
        </div>
        <div class="col-md-6" data-aos="fade-up">
          <h2 class="fw-bold mb-3 why-heading">Why Choose <span class="text-warning">HungerHub</span>?</h2>
          <p class="mb-3">At HungerHub, we're passionate about delivering food that not only satisfies your hunger but also excites your taste buds. We carefully curate a variety of cuisines and partner with the best local kitchens to bring you quality and flavor in every bite.</p>
          <p class="mb-4">With an easy-to-use interface, real-time order tracking, and seamless payment options, we make food ordering enjoyable and hassle-free. Whether you're ordering lunch at the office or planning a family dinner, HungerHub has you covered.</p>
          <a href="#menu" class="btn btn-warning px-4 py-2 fw-semibold shadow-sm">Explore Menu &rarr;</a>
        </div>
      </div>

      <!-- Feature Cards Row -->
      <div class="row justify-content-center text-center g-4">
        <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
          <div class="feature-box p-4 h-100 bg-white rounded-4 shadow-sm border border-light text-center">
            <div class="feature-icon-badge mb-3 mx-auto">
              <i class="fas fa-pizza-slice text-warning fa-2x"></i>
            </div>
            <h4 class="fw-bold mb-2">Fresh & Fast</h4>
            <p class="text-muted small mb-0">We prepare your food with the freshest ingredients and deliver it piping hot — right on time.</p>
          </div>
        </div>

        <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">
          <div class="feature-box p-4 h-100 bg-white rounded-4 shadow-sm border border-light text-center">
            <div class="feature-icon-badge mb-3 mx-auto">
              <i class="fas fa-utensils text-warning fa-2x"></i>
            </div>
            <h4 class="fw-bold mb-2">Expert Chefs</h4>
            <p class="text-muted small mb-0">Our kitchen is run by professionals who ensure every bite meets our top quality standards.</p>
          </div>
        </div>

        <div class="col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="300">
          <div class="feature-box p-4 h-100 bg-white rounded-4 shadow-sm border border-light text-center">
            <div class="feature-icon-badge mb-3 mx-auto">
              <i class="fas fa-shipping-fast text-warning fa-2x"></i>
            </div>
            <h4 class="fw-bold mb-2">Safe Delivery</h4>
            <p class="text-muted small mb-0">Your food is securely sealed and delivered with care by our trained delivery partners.</p>
          </div>
        </div>
      </div>

    </div>
  </section>
  <!-- About Section End -->

  <!-- Menu Section Start -->
  <section class="py-5" id="menu">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold" data-aos="fade-up">Our Featured <span class="text-warning">Menu</span></h2>

      <div class="row justify-content-center g-4">
        <!-- Cheese Pizza -->
        <div class="col-md-4 col-sm-6">
          <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden text-center">
            <img src="images/cheese_pizza.png" class="card-img-top mx-auto" alt="Cheese Pizza" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/cheese_pizza.png';" />
            <div class="card-body d-flex flex-column p-4">
              <h3 class="fw-bold fs-5 mb-2">Cheese Pizza</h3>
              <p class="text-muted small flex-grow-1 mb-3">Delicious cheese-loaded pizza with Italian herbs and crispy crust.</p>
              <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <span class="fs-5 fw-bold text-success">&#8377;199</span>
                <a href="cart.php" class="btn btn-danger btn-sm px-3 fw-semibold">
                  <i class="fa-solid fa-cart-shopping me-1"></i> Add to Cart
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Chhole Bhature -->
        <div class="col-md-4 col-sm-6">
          <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden text-center">
            <img src="images/chhole_bhature.png" class="card-img-top mx-auto" alt="Chhole Bhature" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/chhole_bhature.png';" />
            <div class="card-body d-flex flex-column p-4">
              <h3 class="fw-bold fs-5 mb-2">Chhole Bhature</h3>
              <p class="text-muted small flex-grow-1 mb-3">Authentic North Indian delight served with spicy chana and fluffy bhature.</p>
              <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <span class="fs-5 fw-bold text-success">&#8377;149</span>
                <a href="cart.php" class="btn btn-danger btn-sm px-3 fw-semibold">
                  <i class="fa-solid fa-cart-shopping me-1"></i> Add to Cart
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Chicken Biryani -->
        <div class="col-md-4 col-sm-6">
          <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden text-center">
            <img src="images/chicken_biryani.png" class="card-img-top mx-auto" alt="Chicken Biryani" onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/gh/sonu811670-dotcom/Hungerhub@main/images/chicken_biryani.png';" />
            <div class="card-body d-flex flex-column p-4">
              <h3 class="fw-bold fs-5 mb-2">Chicken Biryani</h3>
              <p class="text-muted small flex-grow-1 mb-3">Spiced and flavorful Hyderabadi-style biryani prepared with tender pieces.</p>
              <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <span class="fs-5 fw-bold text-success">&#8377;249</span>
                <a href="cart.php" class="btn btn-danger btn-sm px-3 fw-semibold">
                  <i class="fa-solid fa-cart-shopping me-1"></i> Add to Cart
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Clean Full Width Row for View More -->
        <div class="col-12 text-center mt-3 mb-2">
          <a href="menu.php" class="btn btn-outline-primary px-4 py-2 rounded-pill fw-bold shadow-sm">
            View Full Menu <i class="fas fa-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>
  </section>
  <!-- Menu Section End -->

  <!-- Reviews Section Start -->
  <section class="py-5 bg-light" id="review">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold" data-aos="fade-up">
        What Our <span class="text-warning">Customers Say</span>
      </h2>
      <div class="row text-center g-4">
        <!-- review 1 -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="review-card p-4 shadow-sm rounded-4 h-100 bg-white">
            <p class="text-muted mb-3">"Amazing food and super quick delivery! HungerHub never disappoints."</p>
            <div class="rating mb-2">
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star-half-alt text-warning"></i>
            </div>
            <h6 class="mt-3 fw-bold text-dark">&mdash; Rahul Sinha</h6>
          </div>
        </div>

        <!-- review 2 -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
          <div class="review-card p-4 shadow-sm rounded-4 h-100 bg-white">
            <p class="text-muted mb-3">"Love the app interface and the biryani is always top-notch!"</p>
            <div class="rating mb-2">
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="far fa-star text-warning"></i>
            </div>
            <h6 class="mt-3 fw-bold text-dark">&mdash; Sneha Raj</h6>
          </div>
        </div>

        <!-- review 3 -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
          <div class="review-card p-4 shadow-sm rounded-4 h-100 bg-white">
            <p class="text-muted mb-3">"Affordable prices and great taste. My go-to food delivery site!"</p>
            <div class="rating mb-2">
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
              <i class="fas fa-star text-warning"></i>
            </div>
            <h6 class="mt-3 fw-bold text-dark">&mdash; Vikash Kumar</h6>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Reviews Section End -->

  <!-- Facts / Stats Section Start -->
  <section class="py-5" id="stats">
    <div class="container">
      <h2 class="text-center fw-bold mb-5" data-aos="fade-up">
        HungerHub <span class="text-warning">In Numbers</span>
      </h2>
      <div class="row text-center g-4">

        <!-- Stat 1 -->
        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
          <div class="p-4 border rounded-4 shadow-sm bg-white">
            <i class="fas fa-users fa-2x text-warning mb-2"></i>
            <h3 class="fw-bold">10K+</h3>
            <p class="text-muted mb-0">Happy Customers</p>
          </div>
        </div>

        <!-- Stat 2 -->
        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
          <div class="p-4 border rounded-4 shadow-sm bg-white">
            <i class="fas fa-utensils fa-2x text-warning mb-2"></i>
            <h3 class="fw-bold">250+</h3>
            <p class="text-muted mb-0">Dishes Served</p>
          </div>
        </div>

        <!-- Stat 3 -->
        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
          <div class="p-4 border rounded-4 shadow-sm bg-white">
            <i class="fas fa-star fa-2x text-warning mb-2"></i>
            <h3 class="fw-bold">4.8</h3>
            <p class="text-muted mb-0">Average Rating</p>
          </div>
        </div>

        <!-- Stat 4 -->
        <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="400">
          <div class="p-4 border rounded-4 shadow-sm bg-white">
            <i class="fas fa-motorcycle fa-2x text-warning mb-2"></i>
            <h3 class="fw-bold">500+</h3>
            <p class="text-muted mb-0">Deliveries Daily</p>
          </div>
        </div>

      </div>
    </div>
  </section>
  <!-- facts end -->

  <!-- work start -->
  <section class="py-5 bg-light" id="how-it-works">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold" data-aos="fade-up">How <span class="text-warning">HungerHub</span> Works</h2>
      <div class="row text-center g-4">

        <!-- Step 1: Order -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
          <div class="step-card p-4 shadow-sm rounded-4 h-100 bg-white">
            <div class="step-icon mb-3">
              <i class="fas fa-utensils fa-3x text-warning"></i>
            </div>
            <h5 class="fw-bold">1. Choose & Order</h5>
            <p class="text-muted mb-0">Select your favorite dishes from our wide range of mouth-watering menus.</p>
          </div>
        </div>

        <!-- Step 2: Cook -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
          <div class="step-card p-4 shadow-sm rounded-4 h-100 bg-white">
            <div class="step-icon mb-3">
              <i class="fas fa-concierge-bell fa-3x text-warning"></i>
            </div>
            <h5 class="fw-bold">2. We Cook Fresh</h5>
            <p class="text-muted mb-0">Our expert chefs prepare your order with the freshest ingredients.</p>
          </div>
        </div>

        <!-- Step 3: Deliver -->
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
          <div class="step-card p-4 shadow-sm rounded-4 h-100 bg-white">
            <div class="step-icon mb-3">
              <i class="fas fa-motorcycle fa-3x text-warning"></i>
            </div>
            <h5 class="fw-bold">3. Safe Delivery</h5>
            <p class="text-muted mb-0">Your food is delivered hot & safe by our reliable delivery partners.</p>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Contact Section Start -->
  <section class="py-5" id="contact">
    <div class="container">
      <h2 class="text-center mb-5 fw-bold" data-aos="fade-up">Get in Touch with <span class="text-warning">HungerHub</span></h2>

      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success text-center"><?php echo $_SESSION['success'];
                                                      unset($_SESSION['success']); ?></div>
      <?php endif; ?>
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger text-center"><?php echo $_SESSION['error'];
                                                    unset($_SESSION['error']); ?></div>
      <?php endif; ?>

      <div class="row contact-section align-items-center" data-aos="fade-up">
        <!-- Contact Form -->
        <div class="col-md-6">
          <form action="contact_process.php" method="POST">
            <div class="mb-3">
              <label class="form-label fw-semibold">Your Name</label>
              <input type="text" name="name" class="form-control" placeholder="Sonu Kumar" required />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" name="email" class="form-control" placeholder="you@example.com" required />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Your Message</label>
              <textarea name="message" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-warning w-100 fw-bold">Send Message</button>
          </form>
        </div>

        <!-- Contact Details -->
        <div class="col-md-6 ps-md-5 mt-5 mt-md-0">
          <div class="contact-info mb-4 d-flex align-items-center">
            <i class="fas fa-map-marker-alt me-3 fs-4 text-warning"></i>
            <p class="mb-0">Sai Vihar Colony, Road No. 1, Madhukam, Ranchi, Jharkhand</p>
          </div>
          <div class="contact-info mb-4 d-flex align-items-center">
            <i class="fas fa-envelope me-3 fs-4 text-warning"></i>
            <p class="mb-0">support@hungerhub.com</p>
          </div>
          <div class="contact-info mb-4 d-flex align-items-center">
            <i class="fas fa-phone-alt me-3 fs-4 text-warning"></i>
            <p class="mb-0">+91 8603972526</p>
          </div>
          <div class="rounded-4 overflow-hidden mt-4 shadow-sm border">
            <iframe src="https://maps.google.com/maps?q=Ranchi&t=&z=13&ie=UTF8&iwloc=&output=embed"
              width="100%" height="220" style="border:0;" allowfullscreen loading="lazy"></iframe>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Contact Section End -->

  <?php include 'includes/footer.php'; ?>

  <!-- Bootstrap & AOS Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      once: true,
      disable: 'mobile'
    });
  </script>
</body>
</html>