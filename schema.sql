-- ESAHub Africa database schema

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    featured_image VARCHAR(255) DEFAULT NULL,
    status ENUM('draft', 'published') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  description TEXT NULL,
  featured_image VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE programs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  subtitle VARCHAR(255) NULL,
  summary TEXT NOT NULL,
  description LONGTEXT NOT NULL,
  delivery_mode ENUM('online','physical','both') NOT NULL DEFAULT 'online',
  duration VARCHAR(120) NULL,
  price_label VARCHAR(120) NULL,
  featured_image VARCHAR(255) NULL,
  whatsapp_prefill TEXT NULL,
  registration_link VARCHAR(255) NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_program_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Sample admin user (replace password after first login)
-- Password is: Admin@123 (change after first login)
INSERT INTO admins (username, password_hash)
VALUES ('admin', '$2y$12$vwqkbW0rdoa0atxXHmJcMeZZtrtm5uY5Ij.ycLROQ.JXzsTiE/l.G');

INSERT INTO categories (name, slug, is_active) VALUES
('Career Development', 'career-development', 1),
('Personal Development', 'personal-development', 1),
('Business Development', 'business-development', 1),
('Skills Development', 'skills-development', 1),
('HDC', 'hdc', 1);

-- Settings table for site-wide options (hero, contact, etc.)
CREATE TABLE IF NOT EXISTS settings (
  `k` VARCHAR(120) NOT NULL PRIMARY KEY,
  `v` TEXT NULL
);

-- Seed basic settings (hero text + contact fallback)
INSERT INTO settings (`k`,`v`) VALUES
('hero_title', 'Empowering communities through practical education and innovation.'),
('hero_subtitle', 'ESAHub Africa supports youth, women, and families with career, business, and digital skills programs.'),
('hero_images', JSON_ARRAY('hero.svg')),
('hero_image', NULL),
('programs_section_title', 'Our Programs'),
('programs_section_subtitle', 'Practical training and support designed for people who want to learn, grow, and build better futures.'),
('programs_section_image', NULL),
('service_1_title', 'Hall Booking'),
('service_1_description', 'Secure a welcoming, well-equipped venue for trainings, meetings, workshops, and community events.'),
('service_2_title', 'Business Consultation'),
('service_2_description', 'Get practical guidance on business planning, strategy, and growth for entrepreneurs and SMEs.'),
('hub_address', '1st floor of Risk Mitigation and Engineering Plaza opp zone 1 police station Buk Road Kano.'),
('phone', '+2347013596333'),
('email', 'esahubafrica@gmail.com')
ON DUPLICATE KEY UPDATE `v` = VALUES(`v`);

-- Blog posts table (used by public/index.php queries)
CREATE TABLE IF NOT EXISTS blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  excerpt TEXT NULL,
  content TEXT NOT NULL,
  featured_image VARCHAR(255) DEFAULT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Example blog post
INSERT INTO blog_posts (title, slug, excerpt, content, status) VALUES
('Welcome to ESAHub','welcome-to-esahub','An introduction to ESAHub Africa and our mission.','Welcome to ESAHub Africa — we deliver practical education and support for communities.', 'published')
ON DUPLICATE KEY UPDATE title = VALUES(title);
