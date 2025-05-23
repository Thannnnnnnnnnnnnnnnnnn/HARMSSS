<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Hotel Avalon</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Open+Sans&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
    }
    h1, h2 {
      font-family: 'Playfair Display', serif;
    }
  </style>
</head>
<body class="bg-[#4e3b2a] text-[#F7E6CA]">


  <header class="bg-[#3b2c1f] p-4 shadow-md">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
      <h1 class="text-3xl font-bold text-[#F7E6CA]">Hotel Avalon</h1>
      <nav class="space-x-6 text-lg">
        <a href="#about" class="hover:text-white transition">About</a>
        <a href="#rooms" class="hover:text-white transition">Rooms</a>
        <a href="#amenities" class="hover:text-white transition">Amenities</a>
        <a href="#contact" class="hover:text-white transition">Contact</a> 
         <a href="BookingButton.php" class="bg-[#F7E6CA] text-[#594423] px-4 py-2 rounded-lg hover:bg-[#fff6e8] transition font-semibold">Book Now</a>
      </nav>
    </div>
  </header>


  <section class="relative bg-cover bg-center h-[85vh]" style="background-image: url('https://images.unsplash.com/photo-1542315192-d59d3f5c4076?auto=format&fit=crop&w=1350&q=80');">
    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center text-center px-6">
      <div>
        <h2 class="text-5xl md:text-6xl font-bold mb-4 text-[#F7E6CA]">Welcome to Hotel Avalon</h2>
        <p class="text-xl mb-6 max-w-xl mx-auto">Experience timeless elegance, world-class service, and exquisite comfort in the heart of luxury.</p>
        <a href="#contact" class="bg-[#F7E6CA] text-[#594423] px-6 py-3 text-lg rounded-full hover:bg-[#fff6e8] transition font-bold">Contact Us to Reserve</a>
      </div>
    </div>
  </section>


  <section id="about" class="py-16 px-6 md:px-16 bg-[#3b2c1f]">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-10 items-center">
      <div>
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80" alt="Hotel Lobby" class="rounded-xl shadow-lg">
      </div>
      <div class="text-center md:text-left">
        <h2 class="text-4xl font-bold mb-4">About Avalon</h2>
        <p class="text-lg text-[#f3e1c5]">Nestled in serene landscapes, Hotel Avalon is a luxurious 5-star retreat offering premium hospitality, curated culinary experiences, and elegant rooms designed for ultimate comfort. Our legacy blends tradition and modernity, making every stay unforgettable.</p>
      </div>
    </div>
  </section>


  <section id="rooms" class="py-16 px-6 md:px-16 bg-[#4e3b2a]">
    <div class="text-center mb-10">
      <h2 class="text-4xl font-bold">Our Rooms</h2>
      <p class="text-[#f3e1c5] mt-2">Elegant, spacious, and crafted for your relaxation.</p>
    </div>
    <div class="max-w-6xl mx-auto grid md:grid-cols-3 gap-8">
      <div class="bg-[#3b2c1f] rounded-lg overflow-hidden shadow-lg">
        <img src="https://sec.solaireresort.com/sites/default/files/2024-07/deluxe_5.webp" alt="Deluxe Room">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">Deluxe Room</h3>
          <p class="text-[#f3e1c5]">A perfect blend of luxury and comfort for solo or couple stays.</p>
        </div>
      </div>
      <div class="bg-[#3b2c1f] rounded-lg overflow-hidden shadow-lg">
        <img src="https://diamondhotel.com/wp-content/uploads/2024/02/premier-executive3.jpg" alt="Executive Suite">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">Executive Suite</h3>
          <p class="text-[#f3e1c5]">Spacious suites with city views and exclusive amenities.</p>
        </div>
      </div>
      <div class="bg-[#3b2c1f] rounded-lg overflow-hidden shadow-lg">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRAlmOjfwXRWNTYUBmQ3Z2TTOlkoCxCTT5sOw&s" alt="Presidential Suite">
        <div class="p-4">
          <h3 class="text-xl font-semibold mb-2">Presidential Suite</h3>
          <p class="text-[#f3e1c5]">Top-tier experience with elegant interiors and premium services.</p>
        </div>
      </div>
    </div>
  </section>


  <section id="contact" class="py-16 px-6 md:px-16 bg-[#3b2c1f]">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-10 items-center">
      <div>
        <h2 class="text-4xl font-bold mb-4">Contact Us to Reserve</h2>
        <p class="text-lg text-[#f3e1c5] mb-6">Please contact our staff to make a reservation or inquire about room availability. We are here to assist you 24/7.</p>
        <p class="mb-2"><strong>Email:</strong> <a href="mailto:reservations@hotelavalon.com" class="underline hover:text-white">reservations@hotelavalon.com</a></p>
        <p class="mb-2"><strong>Phone:</strong> <a href="tel:+639123456789" class="underline hover:text-white">+63 912 345 6789</a></p>
        <p><strong>Address:</strong> 123 Luxury Lane, Makati City, Philippines</p>
      </div>
      <div>
     <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSbtDcypeD6XPoDhXzpdXDlid7lWyosNjK8nQ&s" alt="Hotel Reception" class="rounded-xl shadow-lg">

      </div>
    </div>
  </section>


  <footer class="bg-[#2e2219] py-6 text-center text-sm text-[#c8b79e]">
    © 2025 Hotel Avalon. All rights reserved.
  </footer>

</body>
</html>

    