<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal IQ</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="logo">Animal IQ</div>
        <ul>
            <li><a href="#home">Home</a></li>
            <li><a href="#articles">Articles</a></li>
            <li><a href="#members">Members</a></li>
            <li><a href="#events">Events</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>

    <!-- Home Section -->
    <section id="home" class="home">
        <div class="container">
            <h1>Welcome to Animal IQ</h1>
            <p class="description">
                Animal IQ is a community dedicated to creating awareness about animals, nature, and wildlife. 
                We focus on caring for wild animals, conducting research, and sharing our findings with the world.
            </p>
            <div class="founder">
                <h2>A Word from Our Founder</h2>
                <p>
                    "As the founder of Animal IQ, my vision is to create a world where humans and wild animals coexist harmoniously. 
                    Through research, education, and community efforts, we can make a difference."
                </p>
                <p><strong>- Jane Doe</strong></p>
            </div>
            <div class="top-members">
                <h2>Top Members</h2>
                <div class="members-grid">
                    <div class="member">
                        <img src="https://via.placeholder.com/100" alt="Member 1">
                        <p>John Smith</p>
                    </div>
                    <div class="member">
                        <img src="https://via.placeholder.com/100" alt="Member 2">
                        <p>Alice Johnson</p>
                    </div>
                    <div class="member">
                        <img src="https://via.placeholder.com/100" alt="Member 3">
                        <p>Michael Brown</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Articles/Research Section -->
    <section id="articles" class="articles">
        <div class="container">
            <h2>Articles & Research</h2>
            <div class="articles-grid">
                <div class="article">
                    <img src="https://via.placeholder.com/200" alt="Animal Image">
                    <h3>Behavior of African Elephants</h3>
                    <p class="date">Research Date: January 2023 | Posted: March 2023</p>
                    <p class="author">By: Sarah Lee, University of Wildlife Studies</p>
                    <p class="summary">
                        A study on the social behavior of African elephants in the Serengeti.
                    </p>
                </div>
                <!-- Add more articles here -->
            </div>
        </div>
    </section>

    <!-- Members Section -->
    <section id="members" class="members">
        <div class="container">
            <h2>Our Members</h2>
            <div class="members-grid">
                <div class="member">
                    <img src="https://via.placeholder.com/100" alt="Member 1">
                    <p>John Smith</p>
                    <p>University of Wildlife Studies</p>
                    <p>Researcher</p>
                </div>
                <!-- Add more members here -->
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="events">
        <div class="container">
            <h2>Events</h2>
            <div class="events-grid">
                <div class="event">
                    <h3>Upcoming: Wildlife Conservation Workshop</h3>
                    <p><strong>Location:</strong> Nairobi National Park</p>
                    <p><strong>Activity:</strong> Conservation Training</p>
                    <p><strong>Date:</strong> November 15, 2023</p>
                    <div class="event-images">
                        <img src="https://via.placeholder.com/150" alt="Event Image 1">
                        <img src="https://via.placeholder.com/150" alt="Event Image 2">
                    </div>
                    <a href="#" class="map-link">View Map Directions</a>
                </div>
                <!-- Add more events here -->
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2>Contact Us</h2>
            <form id="contact-form">
                <input type="text" id="name" placeholder="Your Name" required>
                <input type="email" id="email" placeholder="Your Email" required>
                <textarea id="message" placeholder="Your Message" required></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2023 Animal IQ. All rights reserved.</p>
    </footer>

    <script src="main.js"></script>
</body>
</html>