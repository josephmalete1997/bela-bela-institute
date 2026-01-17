   <?php
require_once __DIR__ . '/app/bootstrap.php';

$courses = db()->query("SELECT id, title, fee FROM courses WHERE is_active=1 ORDER BY title")->fetchAll();

$ok = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_verify();
    $name = trim($_POST["name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course_name = trim($_POST["course"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if (!$name || !$email || !$phone || !$course_name) {
        $error = "Please complete required fields.";
    } else {
        // Find course_id by title
        $stmt = db()->prepare("SELECT id FROM courses WHERE title = ? LIMIT 1");
        $stmt->execute([$course_name]);
        $course = $stmt->fetch();
        if (!$course) {
            $error = "Invalid course selected.";
        } else {
            $course_id = $course['id'];
            $stmt = db()->prepare("INSERT INTO applications(course_id, full_name, email, phone, motivation) VALUES(?, ?, ?, ?, ?)");
            $stmt->execute([$course_id, $name, $email, $phone, $message]);

            $ok = "Application submitted. We will contact you soon.";
        }
    }
}
?>

<?php
include 'includes/header.php'
?>
   <!-- Apply -->
   <section class="section" id="apply">
       <div class="container">
           <div class="apply">
               <div class="apply-copy">
                   <h2>Apply for the Next Cohort</h2>
                   <p>
                       Share your details and we’ll get back to you with course dates, fees, and schedule options.
                   </p>
                   <ul class="checks">
                       <li>Beginner-friendly options available</li>
                       <li>Weekends & evenings</li>
                       <li>Certificate on completion</li>
                   </ul>
               </div>

               <form class="form" id="applyForm" method="post">
                   <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

                   <?php if ($ok): ?><p style="color:green;"><?= e($ok) ?></p><?php endif; ?>
                   <?php if ($error): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>

                   <label>
                       Full Name
                       <input name="name" type="text" placeholder="Your name" required />
                   </label>

                   <label>
                       Phone / WhatsApp
                       <input name="phone" type="tel" placeholder="+27..." required />
                   </label>

                   <label>
                       Email
                       <input name="email" type="email" placeholder="you@example.com" required />
                   </label>

                   <label>
                       Course Interested In
                       <select name="course" required>
                           <option value="" disabled selected>Select a course</option>
                           <?php foreach ($courses as $c): ?>
                               <option value="<?= e($c['title']) ?>"><?= e($c['title']) ?> - R<?= number_format($c['fee'], 2) ?></option>
                           <?php endforeach; ?>
                       </select>
                   </label>

                   <label>
                       Message (Optional)
                       <textarea name="message" rows="4" placeholder="Tell us your goal (job, business, upskilling)"></textarea>
                   </label>

                   <button class="btn" type="submit">Submit Application</button>
                   <p class="form-note" id="formNote" role="status" aria-live="polite"></p>
               </form>
           </div>
       </div>
   </section>

   <?php
    include 'includes/footer.php'
    ?>

   <style>
       /* Apply */
       .apply {
           display: grid;
           grid-template-columns: 1.05fr 0.95fr;
           gap: 14px;
           align-items: start;
           border-radius: var(--radius);
           border: 1px solid var(--line);
           background: rgba(255, 255, 255, 0.03);
           padding: 18px;
       }

       .checks {
           list-style: none;
           padding: 0;
           margin: 0;
       }

       .checks li {
           padding-left: 26px;
           position: relative;
           margin: 10px 0;
           color: rgba(255, 255, 255, 0.86);
       }

       .checks li::before {
           content: "✓";
           position: absolute;
           left: 0;
           top: 0;
           color: rgba(34, 197, 94, 0.95);
           font-weight: 900;
       }

       /* Form */
       .form label {
           display: block;
           font-size: 13px;
           color: rgba(255, 255, 255, 0.88);
           margin-bottom: 12px;
       }

       .form input,
       .form select,
       .form textarea {
           width: 100%;
           margin-top: 6px;
           padding: 12px 12px;
           border-radius: 14px;
           outline: none;
       }

       .form input:focus,
       .form select:focus,
       .form textarea:focus {
       }

       .form-note {
           margin: 10px 0 0;
           color: rgba(255, 255, 255, 0.8);
           font-size: 13px;
       }
   </style>