<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>HAU University Days | Sign In</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="main-wrapper">

    <!-- LEFT SIDE -->
    <div class="left-section">
        <div class="logo-text">
            <img src="images/logo.png" alt="Description of the image">
        </div>

        <div class="slider">
            <div class="slides">

                <?php
                $slides = [
                ["image" => "images/event1.jpg", "link" => "#"],
                ["image" => "images/event2.jpg", "link" => "#"],
                ["image" => "images/event3.jpg", "link" => "#"],
                ["image" => "images/event4.jpg", "link" => "#"],
                ["image" => "images/event5.png", "link" => "#"]
                ];

                foreach ($slides as $index => $slide):
                ?>
                <div class="slide">
                    <a href="<?= $slide['link']; ?>">
                    <img src="<?= $slide['image']; ?>" alt="Slide <?= $index ?>">
                    </a>
                </div>
                <?php endforeach; ?>

            </div>

            <div class="dots">
                <?php foreach ($slides as $index => $slide): ?>
                <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $index ?>)"></span>
                <?php endforeach; ?>
            </div>
        </div>


    </div>

    <!-- RIGHT SIDE -->
    <div class="container">
        <h2>Guest Sign In</h2>
        <form action="process.php" method="post">
            <input type="email" name="guest_email" placeholder="Email" required>
            <button type="submit" name="guest_login">Continue as Guest</button>
        </form>

        <hr>

        <h2>Student Sign In</h2>
        <form action="process.php" method="post">
            <input type="email" name="student_email" placeholder="University Email" required>
            <input type="text" name="student_number" placeholder="Student Number" required>
            <button type="submit" name="student_login">Continue as Student</button>
        </form>
    </div>

</div>

<script>
let index = 0;
const slides = document.querySelectorAll(".slide");
const dots = document.querySelectorAll(".dot");
const totalSlides = slides.length;

function showSlide(i) {
  const slider = document.querySelector(".slides");
  slider.style.transform = `translateX(-${i * 100}%)`;

  dots.forEach(dot => dot.classList.remove("active"));
  dots[i].classList.add("active");
}

function autoSlide() {
  index++;
  if (index >= totalSlides) index = 0;
  showSlide(index);
}

function goToSlide(i) {
  index = i;
  showSlide(index);
}

setInterval(autoSlide, 3000);
</script>

</body>
</html>
