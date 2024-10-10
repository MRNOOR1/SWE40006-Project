SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `swe40006`

-- Table structure for table `announcements`
CREATE TABLE `announcements` (
  `id` int NOT NULL,
  `course_code` varchar(25) NOT NULL,
  `text` text NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `teacher_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table `announcements`
INSERT INTO `announcements` (`id`, `course_code`, `text`, `time`, `teacher_id`) VALUES
(1, 'CSC101', 'Next class will cover Chapter 5 of the textbook.', '2024-09-16 09:00:00', 1),
(2, 'CSC101', 'Assignment 1 is due next week. Please submit it by Friday.', '2024-09-14 12:00:00', 1),
(3, 'ENG201', 'Midterm exam is scheduled for next Monday.', '2024-09-20 10:00:00', 2),
(4, 'MAT301', 'Lab report submissions are due on Wednesday.', '2024-09-21 11:00:00', 3),
(5, 'PHY202', 'Quiz 2 will be held next week during class.', '2024-09-22 08:30:00', 4),
(6, 'ENG201', 'Class is rescheduled to Monday at 10 AM.', '2024-09-18 10:15:00', 2),
(7, 'MAT301', 'Please review Chapter 3 before the next lecture.', '2024-09-19 14:00:00', 3),
(8, 'PHY202', 'Next lab will cover vector calculus applications.', '2024-09-23 15:30:00', 4);

-- Table structure for table `courses`
CREATE TABLE `courses` (
  `course_code` varchar(25) NOT NULL,
  `course_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table `courses`
INSERT INTO `courses` (`course_code`, `course_name`) VALUES
('BIO202', 'Cell Biology'),
('CSC101', 'Introduction to Computer Science'),
('ENG201', 'Advanced English Literature'),
('HIS101', 'World History'),
('MAT301', 'Calculus III'),
('PHY202', 'Physics II');

-- Table structure for table `users`
CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `type` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`id`, `username`, `password`, `type`) VALUES
(1, 'teacher_john', 'hashed_password_1', 'T'),
(2, 'teacher_jane', 'hashed_password_2', 'T'),
(3, 'teacher_mark', 'hashed_password_3', 'T'),
(4, 'teacher_lisa', 'hashed_password_4', 'T'),
(5, 'student_alice', 'hashed_password_5', 'S'),
(6, 'student_bob', 'hashed_password_6', 'S'),
(7, 'student_charlie', 'hashed_password_7', 'S'),
(8, 'student_diana', 'hashed_password_8', 'S'),
(9, 'student_edward', 'hashed_password_9', 'S'),
(10, 'student_fiona', 'hashed_password_10', 'S'),
(11, 'student_george', 'hashed_password_11', 'S'),
(12, 'student_hannah', 'hashed_password_12', 'S'),
(13, 'student_ian', 'hashed_password_13', 'S'),
(14, 'student_julia', 'hashed_password_14', 'S');

-- Table structure for table `user_courses`
CREATE TABLE `user_courses` (
  `course_code` varchar(25) NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table `user_courses`
INSERT INTO `user_courses` (`course_code`, `user_id`) VALUES
('CSC101', 1),
('HIS101', 1),
('ENG201', 2),
('BIO202', 3),
('MAT301', 3),
('PHY202', 4),
('CSC101', 5),
('CSC101', 6),
('ENG201', 7),
('ENG201', 8),
('MAT301', 9),
('PHY202', 10),
('HIS101', 11),
('BIO202', 12),
('MAT301', 13),
('PHY202', 14);

-- Indexes for tables
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `course_code` (`course_code`);

ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_code`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `user_courses`
  ADD PRIMARY KEY (`course_code`,`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_code` (`course_code`);

-- AUTO_INCREMENT for tables
ALTER TABLE `announcements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

-- Constraints for tables
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`);

ALTER TABLE `user_courses`
  ADD CONSTRAINT `user_courses_ibfk_1` FOREIGN KEY (`course_code`) REFERENCES `courses` (`course_code`),
  ADD CONSTRAINT `user_courses_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

COMMIT;