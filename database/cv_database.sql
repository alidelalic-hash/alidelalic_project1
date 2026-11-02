-- =====================================================
-- Schema (MySQL 8+) based on the ER diagram
-- =====================================================
CREATE DATABASE cv_social_db;
USE cv_social_db;

-- ---------- users ----------
CREATE TABLE users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100),
    role       ENUM('admin', 'moderator', 'member') NOT NULL,
    username   VARCHAR(50),
    created_at DATE
);

-- ---------- friendships ----------
CREATE TABLE friendships (
    relation_id INT AUTO_INCREMENT PRIMARY KEY,
    user_Id_1   INT NOT NULL,
    user_Id_2   INT NOT NULL,
    status      ENUM('pending', 'accepted', 'blocked') NOT NULL,
    FOREIGN KEY (user_Id_1) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_Id_2) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------- posts ----------
CREATE TABLE posts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(200),
    body       TEXT,
    user_id    INT NOT NULL,
    status     ENUM('draft', 'published', 'archived'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------- templates ----------
CREATE TABLE templates (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100),
    description VARCHAR(255)
);

-- ---------- styles ----------
CREATE TABLE styles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100),
    description VARCHAR(255)
);

-- ---------- template_styles (junction table) ----------
CREATE TABLE template_styles (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    temp_id  INT,
    style_id INT,
    FOREIGN KEY (temp_id)  REFERENCES templates(id) ON DELETE CASCADE,
    FOREIGN KEY (style_id) REFERENCES styles(id)    ON DELETE CASCADE
);

-- ---------- CVs ----------
CREATE TABLE CVs (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    name               VARCHAR(100),
    status             ENUM('draft', 'completed', 'shared'),
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by_user_Id INT,
    temp_id            INT,
    FOREIGN KEY (created_by_user_Id) REFERENCES users(id)     ON DELETE CASCADE,
    FOREIGN KEY (temp_id)            REFERENCES templates(id) ON DELETE SET NULL
);

-- =====================================================
-- Sample data
-- =====================================================

-- users
INSERT INTO users (id, name, role, username, created_at) VALUES
(1, 'Amar Hodžić',    'admin',     'amar_h',   '2026-01-10'),
(2, 'Lejla Begić',    'moderator', 'lejla_b',  '2026-02-14'),
(3, 'Emir Kovač',     'member',    'emir_k',   '2026-03-03'),
(4, 'Sara Delić',     'member',    'sara_d',   '2026-04-21'),
(5, 'Tarik Mehić',    'member',    'tarik_m',  '2026-05-30');

-- friendships
INSERT INTO friendships (relation_id, user_Id_1, user_Id_2, status) VALUES
(1, 1, 2, 'accepted'),
(2, 1, 3, 'accepted'),
(3, 2, 4, 'pending'),
(4, 3, 5, 'accepted'),
(5, 4, 5, 'blocked');

-- posts
INSERT INTO posts (id, title, body, user_id, status, created_at) VALUES
(1, 'Welcome to the platform', 'Excited to start building my CV here!',           1, 'published', '2026-06-01 09:15:00'),
(2, 'Tips for a great CV',     'Keep it to one page and highlight achievements.', 2, 'published', '2026-06-05 14:30:00'),
(3, 'My job hunt journey',     'Draft notes about my job search so far...',       3, 'draft',     '2026-07-12 18:45:00'),
(4, 'Choosing a template',     'I tried three templates and here is what I found.', 4, 'published', '2026-08-20 11:00:00'),
(5, 'Old announcement',        'This post is no longer relevant.',                5, 'archived',  '2026-09-02 08:20:00');

-- templates
INSERT INTO templates (id, name, description) VALUES
(1, 'Classic',   'Traditional single-column CV layout'),
(2, 'Modern',    'Two-column layout with a sidebar'),
(3, 'Creative',  'Colorful design for creative professionals');

-- styles
INSERT INTO styles (id, name, description) VALUES
(1, 'Serif Font',     'Uses a classic serif typeface'),
(2, 'Dark Theme',     'Dark background with light text'),
(3, 'Minimalist',     'Lots of whitespace and simple lines'),
(4, 'Colorful Icons', 'Adds colored icons next to sections');

-- template_styles
INSERT INTO template_styles (id, temp_id, style_id) VALUES
(1, 1, 1),
(2, 1, 3),
(3, 2, 2),
(4, 2, 3),
(5, 3, 4),
(6, 3, 2);

-- CVs
INSERT INTO CVs (id, name, status, created_at, created_by_user_Id, temp_id) VALUES
(1, 'Amar - Software Engineer CV',  'completed', '2026-06-10 10:00:00', 1, 2),
(2, 'Lejla - Marketing CV',         'shared',    '2026-06-18 13:25:00', 2, 3),
(3, 'Emir - Graduate CV',           'draft',     '2026-07-25 16:40:00', 3, 1),
(4, 'Sara - Designer Portfolio CV', 'completed', '2026-08-30 09:05:00', 4, 3),
(5, 'Tarik - General CV',           'draft',     '2026-09-15 19:10:00', 5, NULL);
