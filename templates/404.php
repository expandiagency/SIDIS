<?php
http_response_code(404);
require __DIR__ . '/layout.php';
?>

<main class="page">

  <section class="promo promo--404">
    <div class="promo__container">
      <h1 class="promo__title">404</h1>
      <div class="promo__text">Page not found.</div>
      <div class="promo__btns">
        <a href="/" class="button promo__btn">Back to Home</a>
      </div>
    </div>
  </section>

</main>

<?php require __DIR__ . '/layout_footer.php'; ?>
