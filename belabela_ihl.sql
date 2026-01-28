-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2026 at 08:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `belabela_ihl`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `intake_id` int(11) DEFAULT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `motivation` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `course_id`, `intake_id`, `full_name`, `email`, `phone`, `motivation`, `status`, `admin_notes`, `created_at`) VALUES
(2, 3, NULL, 'Thabo somo', 'thabo@gmail.com', '0818198122', 'Test', 'approved', 'Accepted\nTemp password: Student@9460', '2026-01-17 08:34:44'),
(3, 3, NULL, 'Thabo somo', 'thabo@gmail.com', '0818198122', 'Test', 'rejected', 'rejected', '2026-01-17 08:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` varchar(500) DEFAULT NULL,
  `content` longtext NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `slug`, `excerpt`, `content`, `author_id`, `featured_image`, `tags`, `is_published`, `published_at`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Bela-Bela Institute Opens New Intake', 'bela-bela-institute-opens-new-intake', 'Weekend and evening classes now available. Enrol today.', '<p>We are excited to announce new intake dates for our practical ICT courses. Classes available on weekends and evenings to suit working learners.</p>', 3, 'uploads/articles/1768580951-0781b48e7dd2.png', NULL, 1, '2026-01-16 17:21:00', 1, '2026-01-16 15:21:34', '2026-01-16 16:30:44'),
(2, 'Student Project Showcase', 'student-project-showcase', 'See the projects our learners built this term.', '<p>Our students recently completed capstone projects that showcase their skills in web and data development.</p>', 1, 'uploads/articles/projects.jpg', '[\"students\", \"projects\"]', 1, '2026-01-09 17:21:34', 0, '2026-01-16 15:21:34', '2026-01-16 15:21:34'),
(3, 'Free Workshop: Intro to Python', 'intro-to-python-workshop', 'Join our free workshop to learn Python basics.', '<p>Sign up for a hands-on introduction to Python programming suitable for beginners. Limited seats available.</p>', 1, 'uploads/articles/python.jpg', '[\"workshop\", \"python\"]', 1, '2026-01-02 17:21:34', 0, '2026-01-16 15:21:34', '2026-01-16 15:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` varchar(500) DEFAULT NULL,
  `content` longtext NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(120) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`highlights`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `kanban_status` varchar(32) NOT NULL DEFAULT 'backlog',
  `kanban_position` int(11) NOT NULL DEFAULT 0,
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `slug`, `description`, `image`, `highlights`, `is_active`, `created_at`, `kanban_status`, `kanban_position`, `fee`) VALUES
(3, 'Full-Stack Web development', 'full-stack-web-development', 'Front-end\r\nBack-end', 'images/a1.png', '[]', 1, '2026-01-14 14:15:57', 'backlog', 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `course_educators`
--

CREATE TABLE `course_educators` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `educator_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course_educators`
--

INSERT INTO `course_educators` (`id`, `course_id`, `educator_id`, `assigned_at`) VALUES
(1, 3, 5, '2026-01-17 15:26:54'),
(2, 3, 6, '2026-01-17 15:32:15');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `intake_id` int(11) NOT NULL,
  `status` enum('enrolled','completed','cancelled') NOT NULL DEFAULT 'enrolled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `intake_id`, `status`, `created_at`) VALUES
(8, 4, 5, 'enrolled', '2026-01-17 08:38:37'),
(10, 2, 5, 'enrolled', '2026-01-17 10:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `intakes`
--

CREATE TABLE `intakes` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `schedule` varchar(140) NOT NULL,
  `seats` int(11) NOT NULL DEFAULT 20,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `intakes`
--

INSERT INTO `intakes` (`id`, `course_id`, `start_date`, `end_date`, `schedule`, `seats`, `is_active`, `created_at`) VALUES
(5, 3, '2026-01-14', NULL, 'Weekends & Evenings', 20, 1, '2026-01-14 15:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'New Application Received', 'Application from Thabo somo for Full-Stack Web development', 'admin/applications', 0, '2026-01-17 08:34:44'),
(2, 3, 'New Application Received', 'Application from Thabo somo for Full-Stack Web development', 'admin/applications', 0, '2026-01-17 08:34:44'),
(3, 1, 'New Application Received', 'Application from Thabo somo for Full-Stack Web development', 'admin/applications', 0, '2026-01-17 08:42:18'),
(4, 3, 'New Application Received', 'Application from Thabo somo for Full-Stack Web development', 'admin/applications', 0, '2026-01-17 08:42:18'),
(5, 4, 'Application Approved', 'Your application has been approved. Check your enrollments for details.', 'student/tasks_board', 1, '2026-01-17 09:07:28'),
(6, 4, 'Application Rejected', 'Your application has been rejected. Please contact us for more information.', 'student/portal', 1, '2026-01-17 09:07:32'),
(7, 4, 'Application Approved', 'Your application has been approved. Check your enrollments for details.', 'student/tasks_board', 1, '2026-01-17 09:07:37'),
(8, 4, 'Application Rejected', 'Your application has been rejected. Please contact us for more information.', 'student/portal', 1, '2026-01-17 09:07:39'),
(9, 4, 'Application Rejected', 'Your application has been rejected. Please contact us for more information.', 'student/portal', 1, '2026-01-17 09:07:43'),
(10, 4, 'Application Rejected', 'Your application has been rejected. Please contact us for more information.', 'student/portal', 1, '2026-01-17 09:09:23'),
(11, 3, 'Message from Joseph Malete', 'Can you please review my work?', '/student/task_view.php?id=6&submitter_id=2', 0, '2026-01-17 14:29:42'),
(12, 3, 'Message from Joseph Malete', 'Can you please review my work?', '/student/task_view.php?id=6&submitter_id=2', 0, '2026-01-17 14:29:56'),
(13, 3, 'Message from Joseph Malete', 'Okay', '/student/task_view.php?id=6&submitter_id=2', 0, '2026-01-17 14:37:14'),
(14, 3, 'Message from Joseph Malete', 'Thanks', '/student/task_view.php?id=6&submitter_id=2', 0, '2026-01-17 14:37:27'),
(15, 6, 'New task message', 'Can you please review my work', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:22'),
(16, 1, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:22'),
(17, 3, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:22'),
(18, 5, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:22'),
(19, 6, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:22'),
(20, 6, 'New task message', 'Can you please review my work', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:32'),
(21, 1, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:32'),
(22, 3, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:32'),
(23, 5, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:32'),
(24, 6, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:00:32'),
(25, 6, 'New task message', 'Can you please review my work', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:42'),
(26, 1, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:42'),
(27, 3, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:42'),
(28, 5, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:42'),
(29, 6, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:42'),
(30, 6, 'New task message', 'Can you please review my work', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:54'),
(31, 1, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:54'),
(32, 3, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:54'),
(33, 5, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:54'),
(34, 6, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:03:54'),
(35, 6, 'New task message', 'Can you please review my work', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:05:02'),
(36, 1, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:05:02'),
(37, 3, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:05:02'),
(38, 5, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:05:02'),
(39, 6, 'Message on project review', 'Can you please review my work', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:05:02'),
(40, 4, 'New task message', 'I will', '/student/task_view.php?id=10&submitter_id=4', 0, '2026-01-17 17:07:52'),
(41, 1, 'Message on project review', 'I will', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:07:52'),
(42, 3, 'Message on project review', 'I will', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:07:52'),
(43, 5, 'Message on project review', 'I will', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:07:52'),
(44, 6, 'New task message', 'Okay sir', '/student/task_view.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:23'),
(45, 1, 'Message on project review', 'Okay sir', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:23'),
(46, 3, 'Message on project review', 'Okay sir', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:23'),
(47, 5, 'Message on project review', 'Okay sir', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:23'),
(48, 6, 'Message on project review', 'Okay sir', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:23'),
(49, 4, 'New task message', 'Well-done', '/student/task_view.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:58'),
(50, 1, 'Message on project review', 'Well-done', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:58'),
(51, 3, 'Message on project review', 'Well-done', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:58'),
(52, 5, 'Message on project review', 'Well-done', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:08:58'),
(53, 1, 'Review requested', 'A project is awaiting review: #13', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:19:11'),
(54, 3, 'Review requested', 'A project is awaiting review: #13', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:19:11'),
(55, 5, 'Review requested', 'A project is awaiting review: #13', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:19:11'),
(56, 6, 'Review requested', 'A project is awaiting review: #13', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:19:11'),
(57, 1, 'Review requested', 'A project is awaiting review: #13', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:19:16'),
(58, 3, 'Review requested', 'A project is awaiting review: #13', '/student/task_view.php?id=13&submitter_id=2', 0, '2026-01-17 17:19:16'),
(59, 5, 'Review requested', 'A project is awaiting review: #13', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:19:16'),
(60, 6, 'Review requested', 'A project is awaiting review: #13', '/admin/review.php?id=13&submitter_id=2', 0, '2026-01-17 17:19:16'),
(61, 1, 'Review requested', 'A project is awaiting review: #10', '/student/task_view.php?id=10&submitter_id=4', 0, '2026-01-17 17:19:45'),
(62, 3, 'Review requested', 'A project is awaiting review: #10', '/student/task_view.php?id=10&submitter_id=4', 0, '2026-01-17 17:19:45'),
(63, 5, 'Review requested', 'A project is awaiting review: #10', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:19:45'),
(64, 6, 'Review requested', 'A project is awaiting review: #10', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:19:45'),
(65, 1, 'Review requested', 'A project is awaiting review: #10', '/student/task_view.php?id=10&submitter_id=4', 0, '2026-01-17 17:22:57'),
(66, 3, 'Review requested', 'A project is awaiting review: #10', '/student/task_view.php?id=10&submitter_id=4', 0, '2026-01-17 17:22:57'),
(67, 5, 'Review requested', 'A project is awaiting review: #10', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:22:57'),
(68, 6, 'Review requested', 'A project is awaiting review: #10', '/admin/review.php?id=10&submitter_id=4', 0, '2026-01-17 17:22:57');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('topic','project') NOT NULL DEFAULT 'topic',
  `description` text DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `submitter_id` int(11) DEFAULT NULL,
  `assigned_user_id` int(11) DEFAULT NULL,
  `status` enum('backlog','studying','in_review','review_feedback','completed') NOT NULL DEFAULT 'backlog',
  `position` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `type`, `description`, `course_id`, `submitter_id`, `assigned_user_id`, `status`, `position`, `created_at`, `updated_at`, `url`) VALUES
(10, 'Full Stack CRUD Web App', 'project', 'Overview: Build a full stack CRUD web app for a local service such as a clinic, repair shop, or tutoring center. The system must allow users to create, read, update, and delete records in a secure way. Use a relational database and a server side language of your choice. Your app must include user authentication, input validation, and a clear navigation flow.\n\nObjectives: Practice data modeling, routing, controllers, and views. Demonstrate how you design tables, connect to the database, and separate concerns in your code. Show that you can build a complete feature from UI to database and back. You must document how each route works and why each validation rule exists.\n\nRequirements: Create at least two tables and a relationship between them. Example tables are Customers and Appointments or Students and Projects. Include a login form with password hashing. Add a dashboard that lists records and supports search or filtering. Build forms for create and edit actions. Prevent SQL injection by using prepared statements. Validate and sanitize all user input. Add basic error handling and friendly messages for invalid entries.\n\nDeliverables: Provide a working demo with seed data. Add a README with setup steps, database schema, and screenshots. Include a short explanation of how to run migrations or import SQL. Add a list of assumptions and any limitations.\n\nEvaluation: Your project will be assessed on code structure, security, UI clarity, and correctness of CRUD operations. Your database must remain consistent after repeated create and delete actions. The app should not break if a user submits empty or invalid data. The design does not need to be fancy but must be consistent and readable.\n\nSuggested plan: Start with the database schema and a simple list view. Then build create and edit forms. Add authentication and protect routes. Finally add search and polish. Test each feature with multiple records and edge cases. Ensure your delete action requires confirmation. Make sure your edit form is prefilled with current data. Add a logout action and session checks.\n\nReflection: In a short paragraph, explain what you learned about full stack development and what you would improve next time. Include one performance improvement and one security improvement you would implement with more time. This project should be at least 500 words in documentation and description across your README and comments.', 3, NULL, NULL, 'backlog', 1, '2026-01-17 16:12:05', '2026-01-17 16:12:05', NULL),
(11, 'Data Analysis and Reporting Portfolio', 'project', 'Overview: Create a data analysis portfolio using a real or simulated dataset. The goal is to clean data, explore patterns, and present insights in a clear report. You must use spreadsheets or SQL for cleaning and at least one visualization tool for charts. The final output should read like a report for a manager or client.\n\nObjectives: Demonstrate data cleaning, descriptive statistics, and basic visualization skills. Show that you can ask the right questions, calculate metrics, and communicate findings in plain language. This task focuses on clarity and evidence based reasoning.\n\nDataset: Choose a dataset with at least 200 rows. It can be sales, student scores, clinic visits, or support tickets. If you create your own data, describe how you generated it and why it is realistic. Include a data dictionary that defines each column.\n\nSteps: First, inspect missing values and duplicates. Document how you handle them. Second, compute key metrics such as totals, averages, and rates. Third, build charts that explain trends or comparisons. Fourth, write a short narrative summary that links the charts to practical recommendations. Fifth, create a one page dashboard or report view.\n\nDeliverables: Submit a cleaned dataset, a report document, and a short slide deck with 5 to 7 slides. Your report should include an executive summary, a method section, findings, and recommendations. Provide queries or formulas used to compute metrics. Include at least three charts with clear labels and titles.\n\nEvaluation: We look for correct calculations, consistent formatting, and logical conclusions. Your charts must match the data and show appropriate scales. Your writing should avoid jargon and focus on decision making. You should mention at least one risk or limitation in your analysis.\n\nSuggested plan: Start by understanding the business question you want to answer. Then clean the data and compute metrics. Build charts that reveal trends. Write the report and review it for clarity. Ask a friend to read it and see if they understand the conclusions. Revise if needed.\n\nReflection: Include a short section describing what you learned about data quality and how you would automate parts of the process in the future. Mention one additional dataset you would like to join if you had access.', 3, NULL, NULL, 'backlog', 2, '2026-01-17 16:12:05', '2026-01-17 16:12:05', NULL),
(12, 'Networking Lab and Topology Design', 'topic', 'Overview: Design a small office network and document the plan. The network should support at least three departments and include wired and wireless access. You must show an IP address plan, a simple topology diagram, and a list of devices needed. The goal is to practice network fundamentals such as addressing, segmentation, and basic security.\n\nObjectives: Demonstrate understanding of IP addressing, subnetting, VLANs, and routing. Show how you choose IP ranges and why. Explain how network segmentation improves security and performance. Include a brief section on how you would monitor and troubleshoot the network.\n\nRequirements: Create a diagram that includes a router, a switch, a wireless access point, and at least one server. Define at least three VLANs and assign each to a department. Provide an IP plan with subnet masks and gateways. Include DHCP scope settings and a note on DNS. Add a brief policy for WiFi access and guest access.\n\nDeliverables: Submit a PDF report with your diagram, IP plan table, device list, and configuration notes. Include a short description of how traffic flows between departments. Mention any firewall rules you would apply to protect sensitive data. Add a section on redundancy and backup ideas, even if the budget is small.\n\nEvaluation: We assess the correctness of your IP plan, the clarity of the diagram, and the logic of your segmentation. Your report should be easy to follow and consistent in notation. You should explain why each choice makes sense for a small office and how it could scale in the future.\n\nSuggested plan: Start by listing the departments and the number of devices per department. Choose private IP ranges and calculate subnets. Draw the topology and label each segment. Then write the report and check for errors. Make sure the diagram matches the IP plan.\n\nReflection: Describe what you found most challenging about subnetting or topology design. Mention one real world constraint you had to consider, such as cost or future growth.', 3, NULL, NULL, 'backlog', 3, '2026-01-17 16:12:05', '2026-01-17 16:12:05', NULL),
(13, 'Cybersecurity Risk Review and Controls', 'project', 'Overview: Conduct a cybersecurity risk review for a small organization and propose controls. The organization could be a school, clinic, or retail store. Identify at least five common risks, assess their likelihood and impact, and propose practical mitigations. The aim is to build awareness of basic security posture and response planning.\n\nObjectives: Practice risk identification, prioritization, and mitigation planning. Demonstrate that you can translate technical risks into business impact. Show that you can select simple, affordable controls that reduce risk without complex infrastructure.\n\nScope: Include risks related to phishing, weak passwords, unpatched systems, data loss, and unauthorized access. You may add risks related to WiFi, mobile devices, or third party vendors. For each risk, provide a short description, possible causes, and the expected impact if the risk is realized.\n\nControls: Propose controls such as multi factor authentication, password policies, basic endpoint protection, regular updates, backups, user training, and access reviews. Include a short incident response checklist with clear steps. Add a minimal logging plan so that incidents can be investigated later.\n\nDeliverables: Submit a report with a risk table, mitigation plan, and a one page executive summary. The risk table should include likelihood and impact ratings, plus a priority score. The mitigation plan should include who owns each control and a timeline. Provide a short training outline for staff.\n\nEvaluation: We look for clear reasoning, practical recommendations, and a balanced approach. Your controls should match the risk level and the size of the organization. The report should be well structured and easy to read. Avoid vague advice and focus on actionable steps.\n\nSuggested plan: Start by describing the organization and its assets. Then list the risks, rate them, and select controls. Write the summary last. Review the report for clarity and make sure each risk has a matching control. If possible, create a simple checklist that the organization can follow monthly.\n\nReflection: Mention one risk you feel is most urgent and why. Describe one control that could be improved with a higher budget and what that would achieve.', 3, NULL, NULL, 'backlog', 4, '2026-01-17 16:12:05', '2026-01-17 16:12:05', NULL),
(14, 'Digital Communication and Professional Skills', 'topic', 'Overview: Build a professional communication guide and portfolio. The goal is to demonstrate strong written communication, email etiquette, and documentation skills. You will write a set of templates and create a brief guide that explains when and how to use them. This task supports workplace readiness and collaboration.\n\nObjectives: Show that you can write clearly, structure information, and adapt tone to the audience. Demonstrate practical skills like writing meeting notes, status updates, and support responses. Practice summarizing complex information for non technical readers.\n\nRequirements: Create at least five templates. Include a professional email, a weekly status update, a meeting agenda, meeting minutes, and a support response. Each template should include a subject line, purpose, and key points. Write a one page guide that explains best practices such as clarity, tone, response time, and follow up.\n\nDeliverables: Submit a PDF or document with the templates and guide. Include one example scenario for each template. Provide a short checklist that students can use before sending an email or update. Add a section on version control or document naming to show good organization.\n\nEvaluation: We assess clarity, tone, completeness, and professionalism. Your templates should be easy to reuse. Your guide should be concise but meaningful. The examples should be realistic and reflect common workplace situations.\n\nSuggested plan: Draft the templates first, then write the guide. Ask a peer to review the tone and clarity. Revise and finalize. Ensure consistent formatting and headings. Consider readability for busy readers.\n\nReflection: Describe how communication affects project success and teamwork. Mention one habit you will adopt to improve communication in your future work.', 3, NULL, NULL, 'backlog', 5, '2026-01-17 16:12:05', '2026-01-17 16:12:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `task_edit_requests`
--

CREATE TABLE `task_edit_requests` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `educator_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('topic','project') NOT NULL DEFAULT 'project',
  `description` text DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` enum('backlog','studying','in_review','review_feedback','completed') NOT NULL DEFAULT 'backlog',
  `url` varchar(255) DEFAULT NULL,
  `request_status` enum('pending','approved','denied') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_messages`
--

CREATE TABLE `task_messages` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `submitter_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_messages`
--

INSERT INTO `task_messages` (`id`, `task_id`, `submitter_id`, `sender_id`, `recipient_id`, `message`, `created_at`) VALUES
(1, 13, 2, 2, 6, 'Can you please review my work', '2026-01-17 17:00:22'),
(2, 13, 2, 2, 6, 'Can you please review my work', '2026-01-17 17:00:32'),
(3, 13, 2, 2, 6, 'Can you please review my work', '2026-01-17 17:03:42'),
(4, 13, 2, 2, 6, 'Can you please review my work', '2026-01-17 17:03:54'),
(5, 13, 2, 2, 6, 'Can you please review my work', '2026-01-17 17:05:02'),
(6, 10, 4, 6, 4, 'I will', '2026-01-17 17:07:52'),
(7, 10, 4, 4, 6, 'Okay sir', '2026-01-17 17:08:23'),
(8, 10, 4, 6, 4, 'Well-done', '2026-01-17 17:08:58');

-- --------------------------------------------------------

--
-- Table structure for table `task_progress`
--

CREATE TABLE `task_progress` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('backlog','studying','in_review','review_feedback','completed') NOT NULL DEFAULT 'backlog',
  `position` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_progress`
--

INSERT INTO `task_progress` (`id`, `task_id`, `user_id`, `status`, `position`, `updated_at`) VALUES
(19, 13, 2, 'in_review', 0, '2026-01-17 17:19:16'),
(20, 10, 4, 'in_review', 0, '2026-01-17 17:11:40');

-- --------------------------------------------------------

--
-- Table structure for table `task_reviewer_assignments`
--

CREATE TABLE `task_reviewer_assignments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `submitter_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_reviews`
--

CREATE TABLE `task_reviews` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `submitter_id` int(11) DEFAULT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `is_competent` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_reviews`
--

INSERT INTO `task_reviews` (`id`, `task_id`, `submitter_id`, `reviewer_id`, `comment`, `is_competent`, `created_at`) VALUES
(5, 10, 4, 6, 'yes', 1, '2026-01-17 17:22:57');

-- --------------------------------------------------------

--
-- Table structure for table `task_review_overrides`
--

CREATE TABLE `task_review_overrides` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `granted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(180) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','student','educator') NOT NULL DEFAULT 'student',
  `status` enum('active','blocked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `avatar`, `password_hash`, `role`, `status`, `created_at`) VALUES
(1, 'Site Admin', 'admin@belabela.co.za', '0000000000', NULL, '$2y$10$H2fYVbXgF2Gm9k8bB7j6jOQq3j2h7lq6l0YcDkSxwq8vXl5yqJ7p2', 'admin', 'active', '2026-01-13 16:05:14'),
(2, 'Joseph Malete', 'jjm@gmail.com', '0818198122', 'uploads/avatars/student_2.webp', '$2y$10$YtFZJlP/7toJoxFAqs2gmekCf1E1AhhWYc4uGky/VpyWIVjiV/U1C', 'student', 'active', '2026-01-13 16:36:12'),
(3, 'Joseph Malete', 'admin@belabelacollege.co.za', '0818198122', NULL, '$2y$10$FhBMSen/JKzr3kQ4d6ecTOtLsNDAHHPjdcB1UKOESJVWugnhnEDU6', 'admin', 'active', '2026-01-14 16:06:41'),
(4, 'Thabo somo', 'thabo@gmail.com', '0818198122', 'uploads/avatars/student_4.jpg', '$2y$10$rmrlYCdRhIRqr9awCNNUc..JtYHwrIQpQVStgYRV.1WS8H6JEdnKy', 'student', 'active', '2026-01-17 08:35:37'),
(5, 'Thato Mokoena', 'thato.mokoena@belabelainstitute.co.za', NULL, NULL, '323f22b257e7245e92b1c3ce384e0b8b2eda5865091c5cfe9e707c03fdfc4070', 'educator', 'active', '2026-01-17 15:26:54'),
(6, 'Thabo Molema', 'thabo.molema@belabelacollege.co.za', NULL, NULL, '$2y$10$wXU.QcEJcsWHx5Eb99WQKeeVS6Z9OhIBEyLoi7EF4Mge4oYYmCMAq', 'educator', 'active', '2026-01-17 15:28:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `intake_id` (`intake_id`),
  ADD KEY `email` (`email`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `is_published` (`is_published`),
  ADD KEY `published_at` (`published_at`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `is_published` (`is_published`),
  ADD KEY `published_at` (`published_at`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `course_educators`
--
ALTER TABLE `course_educators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_course_educator` (`course_id`,`educator_id`),
  ADD KEY `educator_id` (`educator_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_enroll` (`user_id`,`intake_id`),
  ADD KEY `intake_id` (`intake_id`);

--
-- Indexes for table `intakes`
--
ALTER TABLE `intakes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enrollment_id` (`enrollment_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submitter_id` (`submitter_id`);

--
-- Indexes for table `task_edit_requests`
--
ALTER TABLE `task_edit_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `educator_id` (`educator_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `task_messages`
--
ALTER TABLE `task_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `submitter_id` (`submitter_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `task_progress`
--
ALTER TABLE `task_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_task_user` (`task_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `task_reviewer_assignments`
--
ALTER TABLE `task_reviewer_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_assignment` (`task_id`,`submitter_id`,`reviewer_id`),
  ADD KEY `submitter_id` (`submitter_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `task_reviews`
--
ALTER TABLE `task_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `idx_task_submitter` (`task_id`,`submitter_id`),
  ADD KEY `fk_task_reviews_submitter` (`submitter_id`);

--
-- Indexes for table `task_review_overrides`
--
ALTER TABLE `task_review_overrides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_task_user` (`task_id`,`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `course_educators`
--
ALTER TABLE `course_educators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `intakes`
--
ALTER TABLE `intakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `task_edit_requests`
--
ALTER TABLE `task_edit_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_messages`
--
ALTER TABLE `task_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `task_progress`
--
ALTER TABLE `task_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `task_reviewer_assignments`
--
ALTER TABLE `task_reviewer_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_reviews`
--
ALTER TABLE `task_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `task_review_overrides`
--
ALTER TABLE `task_review_overrides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`intake_id`) REFERENCES `intakes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_educators`
--
ALTER TABLE `course_educators`
  ADD CONSTRAINT `course_educators_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_educators_ibfk_2` FOREIGN KEY (`educator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`intake_id`) REFERENCES `intakes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `intakes`
--
ALTER TABLE `intakes`
  ADD CONSTRAINT `intakes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_edit_requests`
--
ALTER TABLE `task_edit_requests`
  ADD CONSTRAINT `task_edit_requests_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_edit_requests_ibfk_2` FOREIGN KEY (`educator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_edit_requests_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_messages`
--
ALTER TABLE `task_messages`
  ADD CONSTRAINT `task_messages_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_messages_ibfk_2` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_messages_ibfk_3` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_messages_ibfk_4` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_progress`
--
ALTER TABLE `task_progress`
  ADD CONSTRAINT `task_progress_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_progress_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_reviewer_assignments`
--
ALTER TABLE `task_reviewer_assignments`
  ADD CONSTRAINT `task_reviewer_assignments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_reviewer_assignments_ibfk_2` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_reviewer_assignments_ibfk_3` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_reviews`
--
ALTER TABLE `task_reviews`
  ADD CONSTRAINT `fk_task_reviews_submitter` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `task_reviews_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_reviews_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_review_overrides`
--
ALTER TABLE `task_review_overrides`
  ADD CONSTRAINT `task_review_overrides_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
