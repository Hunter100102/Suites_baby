<?php $title = "Aban Suites — Private Suites for Professionals"; include __DIR__ . "/partials_header.php"; ?>
<section class="hero">
  <video autoplay muted loop playsinline>
    <source src="/assets/video/hero.mp4" type="video/mp4" />
  </video>
  <div class="overlay"></div>
  <div class="content">
    <h1>Find your perfect suite</h1>
    <p>Simple, elegant, and move-in ready. Choose an available room and book a viewing today.</p>
    <a class="btn cta" href="#layout">See Availability</a>
  </div>
</section>

<div class="container" id="layout">
  <div class="card">
    <h2>Layout</h2>

    <!-- Availability legend -->
    <div class="legend">
      <span><i class="swatch avail"></i> Available</span>
      <span><i class="swatch occ"></i> Occupied</span>
    </div>

    <!-- Floorplan that mirrors your sketch -->
    <div class="floorplan">
      <!-- Back shampoo/dryer area with 6 “ovals” -->
      <div class="back-area">
        <div class="appliances">
          <i class="appliance"></i><i class="appliance"></i><i class="appliance"></i>
          <i class="appliance"></i><i class="appliance"></i><i class="appliance"></i>
        </div>
      </div>

      <!-- Left stack: Rooms 1–10 -->
      <div class="left-stack" id="left-rooms"></div>

      <!-- Corridor -->
      <div class="corridor"></div>

      <!-- Right stack: Rooms 11–20 -->
      <div class="right-stack" id="right-rooms"></div>

      <!-- Angled entrance -->
      <div class="entrance"><span>Entrance</span></div>
    </div>
  </div>

  <!-- Keep your viewing request card, tenants card, etc. EXACTLY as before -->
  <?php // If you had other cards/sections after "Layout", keep them. ?>
</div>

<script src="/assets/js/app.js?v=layout-sketch"></script>
<?php include __DIR__ . "/partials_footer.php"; ?>
