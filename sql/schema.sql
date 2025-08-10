
-- ==========================================
-- Animal IQ Community Website - Revised Schema
-- ==========================================

-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255),
    google_id VARCHAR(255) UNIQUE,
    bio TEXT,
    role ENUM('visitor', 'enthusiast', 'contributor', 'researcher', 'moderator', 'admin', 'super_admin') DEFAULT 'enthusiast',
    profile_pic VARCHAR(255),
    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255) DEFAULT 'default.png';


-- ADMINS
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'moderator') DEFAULT 'moderator',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- Status lookup table
CREATE TABLE species_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) UNIQUE NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id)
);

-- Main animals table
CREATE TABLE animals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    scientific_name VARCHAR(255),
    common_name VARCHAR(255),
    population_estimate VARCHAR(255),
    species_status_id INT,
    avg_weight_kg FLOAT,
    avg_length_cm FLOAT,
    appearance TEXT,
    main_photo VARCHAR(255),
    submitted_by INT,
    approved_by INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    -- is_animal_of_the_day BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (submitted_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    FOREIGN KEY (species_status_id) REFERENCES species_statuses(id)
);

CREATE TABLE animal_of_the_day (
    id INT PRIMARY KEY DEFAULT 1, -- singleton row
    animal_id INT,
    date DATE,
    FOREIGN KEY (animal_id) REFERENCES animals(id)
);
INSERT INTO animal_of_the_day (id, animal_id, date) VALUES (1, NULL, NULL);
CREATE TABLE animal_rotation_log (
    animal_id INT PRIMARY KEY,
    shown_on DATE,
    FOREIGN KEY (animal_id) REFERENCES animals(id)
);


-- Taxonomy hierarchy tables (all with the same structure)
CREATE TABLE kingdoms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id)
);

CREATE TABLE phyla (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kingdom_id INT,
    name VARCHAR(100) NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kingdom_id) REFERENCES kingdoms(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
);

--- Taxonomy hierarchy tables (all with consistent structure)
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phylum_id INT,
    name VARCHAR(100) NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (phylum_id) REFERENCES phyla(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT,
    name VARCHAR(100) NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
);

CREATE TABLE families (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    name VARCHAR(100) NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
);

CREATE TABLE genera (
    id INT AUTO_INCREMENT PRIMARY KEY,
    family_id INT,
    name VARCHAR(100) NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (family_id) REFERENCES families(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
);

CREATE TABLE species (
    id INT AUTO_INCREMENT PRIMARY KEY,
    genus_id INT,
    name VARCHAR(100) NOT NULL,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (genus_id) REFERENCES genera(id),
    FOREIGN KEY (created_by) REFERENCES admins(id)
);

-- Taxonomy mapping
CREATE TABLE taxonomy (
    animal_id INT PRIMARY KEY,
    species_id INT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    FOREIGN KEY (species_id) REFERENCES species(id)
);

-- Animal details tables
CREATE TABLE animal_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT,
    photo_url VARCHAR(255),
    caption TEXT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

CREATE TABLE animal_geography (
    id INT PRIMARY KEY AUTO_INCREMENT,
    animal_id INT,
    continent VARCHAR(255),
    subcontinent VARCHAR(255),
    country VARCHAR(255),
    realm VARCHAR(255),
    biome VARCHAR(255),
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

CREATE TABLE animal_habits (
    animal_id INT PRIMARY KEY,
    diet TEXT,
    mating_habits TEXT,
    behavior TEXT,
    habitat TEXT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);




CREATE TABLE animal_life_data (
    animal_id INT PRIMARY KEY,
    lifespan_years FLOAT,
    gestation_period_days INT,
    litter_size_avg FLOAT,
    maturity_age_years FLOAT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);


CREATE TABLE animal_human_interaction (
    animal_id INT PRIMARY KEY,
    threats TEXT, -- e.g., "poaching", "habitat loss"
    conservation_efforts TEXT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);


CREATE TABLE animal_facts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT,
    fact TEXT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

CREATE TABLE animal_defense (
    animal_id INT PRIMARY KEY,
    defense_mechanisms TEXT, -- e.g. "camouflage", "venom", "speed"
    notable_adaptations TEXT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

CREATE TABLE animal_health_risks (
    animal_id INT PRIMARY KEY,
    common_diseases TEXT,
    known_parasites TEXT,
    zoonotic_potential BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);










-- POSTS & COMMENTS
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    body TEXT,
    type ENUM('blog', 'story', 'article', 'infographic'),
    region VARCHAR(255),
    likes INT DEFAULT 0,
    views INT DEFAULT 0,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    author_id INT,
    approved_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

ALTER TABLE posts
ADD COLUMN featured_image VARCHAR(255) AFTER region;
ADD COLUMN approved_by INT AFTER author_id,
ADD CONSTRAINT fk_approved_by FOREIGN KEY (approved_by) REFERENCES users(id);

ALTER TABLE posts 
    DROP FOREIGN KEY author_id,
    ADD FOREIGN KEY (author_id) REFERENCES admins(id),
    DROP FOREIGN KEY approved_by,
    ADD FOREIGN KEY (approved_by) REFERENCES admins(id);




CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    content TEXT,
    related_type ENUM('animal', 'post'),
    related_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- QUIZZES
CREATE TABLE quizzes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255),
    topic VARCHAR(255),
    difficulty ENUM('easy', 'medium', 'hard'),
    is_published BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT,
    question TEXT,
    option_a VARCHAR(255),
    option_b VARCHAR(255),
    option_c VARCHAR(255),
    option_d VARCHAR(255),
    correct_option ENUM('A', 'B', 'C', 'D'),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);

CREATE TABLE quiz_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    quiz_id INT,
    score INT,
    taken_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);


--TRIVIA
CREATE TABLE trivia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fact TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO trivia (fact) VALUES
("A group of flamingos is called a 'flamboyance'."),
("Octopuses have three hearts and blue blood."),
("Elephants can recognize themselves in mirrors."),
("Some lizards can squirt blood out of their eyes to scare predators."),
("The fingerprints of a koala are so indistinguishable from humans that they can taint crime scenes.");



-- GALLERY
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    type ENUM('photo', 'infographic', 'art', 'video'),
    file_url VARCHAR(255),
    caption TEXT,
    tags TEXT,
    submitted_by INT,
    approved_by INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submitted_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

-- EVENTS
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    event_date DATETIME,
    type VARCHAR(255),
    location VARCHAR(255),
    max_attendees INT,
    created_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE event_signups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    event_id INT,
    signed_up_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (event_id) REFERENCES events(id)
);

-- FORUM
CREATE TABLE forum_threads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    category VARCHAR(255),
    views INT DEFAULT 0,
    is_pinned BOOLEAN DEFAULT FALSE,
    last_activity_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    author_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

CREATE TABLE forum_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    thread_id INT,
    author_id INT,
    content TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (thread_id) REFERENCES forum_threads(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- DAILY FEATURE
CREATE TABLE daily_feature (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT,
    date DATE UNIQUE,
    FOREIGN KEY (animal_id) REFERENCES animals(id)
);




-- BADGES & GAMIFICATION
CREATE TABLE badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    badge_type ENUM('milestone', 'engagement', 'special'),
    icon VARCHAR(255)
);

CREATE TABLE user_badges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    badge_id INT,
    awarded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (badge_id) REFERENCES badges(id)
);

CREATE TABLE leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category VARCHAR(255),
    points INT DEFAULT 0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- INDIGENOUS KNOWLEDGE & PODCASTS
CREATE TABLE indigenous_knowledge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    region VARCHAR(255),
    story TEXT,
    submitted_by INT,
    approved_by INT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submitted_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id)
);

CREATE TABLE podcasts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    file_url VARCHAR(255),
    description TEXT,
    tags TEXT,
    duration_seconds INT,
    cover_image_url VARCHAR(255),
    contributor_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contributor_id) REFERENCES users(id)
);

-- SETTINGS & LOGS
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    `key` VARCHAR(100) UNIQUE,
    `value` TEXT
);
INSERT INTO settings (`key`, `value`) VALUES
('site_vision', ''),
('site_mission', ''),
('site_logo', '')
ON DUPLICATE KEY UPDATE `value` = `value`; 


CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action TEXT,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    occurred_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);


CREATE TABLE partners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT, -- What they do
    location VARCHAR(150), -- Where they are based
    partners_since DATE, -- When they became a partner
    logo_url VARCHAR(255), -- URL to logo image
    
    contact_email VARCHAR(150), -- Optional: contact person
    website_url VARCHAR(255), -- Optional: link to partner's website
    status ENUM('active', 'inactive') DEFAULT 'active', -- Whether they are still a partner
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


CREATE TABLE management_team (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,                  -- Full name of the team member
    role VARCHAR(100) NOT NULL,                  -- Post / Role / Designation
    message TEXT,                                 -- "What word they have to say"
    photo_url VARCHAR(255),                       -- URL to profile picture
    email VARCHAR(150),                           -- Contact email (optional)
    linkedin_url VARCHAR(255),                    -- Link to LinkedIn profile
    status ENUM('active', 'inactive') DEFAULT 'active', -- Whether they are still in the team
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

