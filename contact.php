<!-- contact.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us - FitZone</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: #111;
    }

    .contact-container {
      max-width: 700px;
      margin: 50px auto;
      background: transparent;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .contact-container h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #A31D1D;
    }

    .contact-form label {
      display: block;
      margin-bottom: 5px;
      color: #F7F7F7;
      font-weight: bold;
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
      resize: vertical;
    }

    .contact-form button {
      background-color: #28a745;
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .contact-form button:hover {
      background-color: #218838;
    }

    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      margin-bottom: 20px;
      border-left: 5px solid #28a745;
      border-radius: 8px;
    }

    @media (max-width: 768px) {
      .contact-container {
        margin: 20px;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="contact-container">
    <h2>Contact Us</h2>

    <?php if (isset($_GET['success'])): ?>
      <div class="success-message">Thank you! Your message has been sent.</div>
    <?php endif; ?>

    <form action="send_message.php" method="POST" class="contact-form">
      <label for="name">Full Name *</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Email Address *</label>
      <input type="email" id="email" name="email" required>

      <label for="phone">Phone Number</label>
      <input type="tel" id="phone" name="phone">

      <label for="subject">Subject</label>
      <input type="text" id="subject" name="subject">

      <label for="message">Message *</label>
      <textarea id="message" name="message" rows="6" required></textarea>

      <button type="submit">Send Message</button>
    </form>
  </div>
</body>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Background Image Example</title>
    <style>
        /*Background Image*/
        body {
            background-image: url('img/background.jpg');
            background-size: cover;
            background-position: center; 
            background-repeat: no-repeat; 
            background-attachment: fixed; 
            height: 100vh;
            width: 100vw;
            margin: 0;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            color: black; 
            
        }

        
        .content {
            background: rgba(0, 0, 0, 0.5); 
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>


</html>
