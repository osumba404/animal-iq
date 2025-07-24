-- 1. First insert users (since many tables reference users)
INSERT INTO users (email, name, password_hash, bio, role, profile_pic, registered_at, last_login) VALUES
('admin@animaliq.com', 'Admin User', '$2a$10$xJwL5v5Jz5UZ5Z5Z5Z5Z5O', 'Site administrator with full privileges', 'super_admin', 'admin.jpg', '2023-01-01 10:00:00', '2023-06-15 14:30:00'),
('moderator@animaliq.com', 'Moderator Jane', '$2a$10$xJwL5v5Jz5UZ5Z5Z5Z5Z5O', 'Content moderator for the community', 'moderator', 'moderator.jpg', '2023-01-05 11:00:00', '2023-06-14 09:15:00'),
('researcher@animaliq.com', 'Dr. David Wilson', '$2a$10$xJwL5v5Jz5UZ5Z5Z5Z5Z5O', 'Wildlife biologist specializing in primate intelligence', 'researcher', 'david.jpg', '2023-01-10 12:00:00', '2023-06-15 16:45:00'),
('contributor@animaliq.com', 'Sarah Johnson', '$2a$10$xJwL5v5Jz5UZ5Z5Z5Z5Z5O', 'Wildlife photographer and conservationist', 'contributor', 'sarah.jpg', '2023-01-15 13:00:00', '2023-06-13 18:20:00'),
('user1@animaliq.com', 'Alex Chen', '$2a$10$xJwL5v5Jz5UZ5Z5Z5Z5Z5O', 'Animal enthusiast and bird watcher', 'enthusiast', 'alex.jpg', '2023-02-01 14:00:00', '2023-06-15 10:10:00'),
('user2@animaliq.com', 'Maria Garcia', '$2a$10$xJwL5v5Jz5UZ5Z5Z5Z5Z5O', 'Marine life lover and scuba diver', 'enthusiast', 'maria.jpg', '2023-02-10 15:00:00', '2023-06-14 11:30:00');

-- 2. Insert animals (submitted by users, approved by moderators/admin)
INSERT INTO animals (scientific_name, common_name, population_estimate, species_status, avg_weight_kg, avg_length_cm, appearance, main_photo, submitted_by, approved_by, status, is_animal_of_the_day, created_at) VALUES
('Panthera leo', 'Lion', '20,000-30,000', 'vulnerable', 190, 250, 'Large muscular cat with short tawny fur, males have prominent manes', 'lion.jpg', 4, 2, 'approved', TRUE, '2023-02-15 10:00:00'),
('Orcinus orca', 'Orca (Killer Whale)', '50,000', 'least concern', 4000, 800, 'Black and white coloration with tall dorsal fin', 'orca.jpg', 5, 2, 'approved', FALSE, '2023-02-16 11:00:00'),
('Corvus corax', 'Common Raven', '16,000,000', 'least concern', 1.2, 65, 'Glossy black feathers, wedge-shaped tail, thick bill', 'raven.jpg', 4, 1, 'approved', FALSE, '2023-02-17 12:00:00'),
('Elephas maximus', 'Asian Elephant', '40,000-50,000', 'endangered', 4000, 550, 'Gray skin, smaller ears than African elephants, some males have tusks', 'asian_elephant.jpg', 3, 1, 'approved', TRUE, '2023-02-18 13:00:00'),
('Octopus vulgaris', 'Common Octopus', 'Unknown', 'least concern', 3, 90, 'Soft-bodied with eight arms, color-changing ability', 'octopus.jpg', 6, 2, 'approved', FALSE, '2023-02-19 14:00:00');

-- 3. Insert animal photos
INSERT INTO animal_photos (animal_id, photo_url, caption) VALUES
(1, 'lion1.jpg', 'Male lion resting in the savanna'),
(1, 'lion2.jpg', 'Lioness with cubs'),
(2, 'orca1.jpg', 'Orca breaching in the Pacific'),
(2, 'orca2.jpg', 'Orca pod swimming together'),
(3, 'raven1.jpg', 'Raven perched on a branch'),
(4, 'elephant1.jpg', 'Asian elephant in the forest'),
(4, 'elephant2.jpg', 'Elephant family at watering hole'),
(5, 'octopus1.jpg', 'Octopus camouflaged on coral');

-- 4. Insert taxonomy data
INSERT INTO taxonomy (animal_id, kingdom, phylum, class, `order`, family, genus, species) VALUES
(1, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Felidae', 'Panthera', 'leo'),
(2, 'Animalia', 'Chordata', 'Mammalia', 'Cetacea', 'Delphinidae', 'Orcinus', 'orca'),
(3, 'Animalia', 'Chordata', 'Aves', 'Passeriformes', 'Corvidae', 'Corvus', 'corax'),
(4, 'Animalia', 'Chordata', 'Mammalia', 'Proboscidea', 'Elephantidae', 'Elephas', 'maximus'),
(5, 'Animalia', 'Mollusca', 'Cephalopoda', 'Octopoda', 'Octopodidae', 'Octopus', 'vulgaris');

-- 5. Insert animal geography
INSERT INTO animal_geography (animal_id, continent, subcontinent, country, realm, biome) VALUES
(1, 'Africa', 'Sub-Saharan Africa', 'Multiple', 'Afrotropical', 'Savanna'),
(2, 'Multiple', NULL, 'Multiple', 'Marine', 'Ocean'),
(3, 'Multiple', 'Northern', 'Multiple', 'Holarctic', 'Various'),
(4, 'Asia', 'South Asia', 'India, Sri Lanka', 'Indomalayan', 'Forest'),
(5, 'Multiple', NULL, 'Multiple', 'Marine', 'Coral reef');

-- 6. Insert animal habits
INSERT INTO animal_habits (animal_id, diet, mating_habits, behavior, habitat) VALUES
(1, 'Carnivorous: antelope, zebra, wildebeest', 'Polygynous, males compete for prides', 'Social, live in prides, females do most hunting', 'Savannas, grasslands'),
(2, 'Carnivorous: fish, seals, whales', 'Polygynous, complex social structure', 'Highly social, live in pods, sophisticated hunters', 'Oceans worldwide'),
(3, 'Omnivorous: insects, grains, small animals', 'Monogamous, pair for life', 'Highly intelligent, problem solvers, playful', 'Various including urban areas'),
(4, 'Herbivorous: grasses, leaves, bark', 'Polygynous, males compete for females', 'Matriarchal herds, strong social bonds', 'Forests, grasslands'),
(5, 'Carnivorous: crabs, mollusks, fish', 'Semelparous (die after mating)', 'Solitary, highly intelligent, excellent camouflage', 'Coral reefs, ocean floor');

-- 7. Insert posts (some approved, some pending)
INSERT INTO posts (title, body, type, region, likes, views, status, author_id, approved_by, created_at) VALUES
('Lion Conservation Efforts', 'Detailed look at current lion conservation programs...', 'article', 'Africa', 45, 320, 'approved', 3, 2, '2023-03-01 10:00:00'),
('My Encounter with Orcas', 'Personal story of swimming near orcas in Norway...', 'story', 'Europe', 28, 180, 'approved', 5, 2, '2023-03-05 11:00:00'),
('Raven Intelligence Experiments', 'Research findings on raven problem-solving abilities...', 'article', 'North America', 12, 95, 'approved', 3, 1, '2023-03-10 12:00:00'),
('Elephants in Asian Culture', 'The cultural significance of elephants across Asia...', 'blog', 'Asia', 34, 210, 'approved', 4, 1, '2023-03-15 13:00:00'),
('Octopus Camouflage Mechanisms', 'New research on how octopuses change color...', 'article', 'Global', 8, 65, 'pending', 6, NULL, '2023-03-20 14:00:00');

-- 8. Insert comments
INSERT INTO comments (user_id, content, related_type, related_id, created_at) VALUES
(4, 'Great article! I didn''t know lion populations were so low.', 'post', 1, '2023-03-02 09:00:00'),
(5, 'Amazing experience! I''d love to see orcas in the wild.', 'post', 2, '2023-03-06 10:00:00'),
(6, 'Ravens are so smart. I see them solving problems in my backyard!', 'post', 3, '2023-03-11 11:00:00'),
(3, 'Elephants have such deep cultural connections in India.', 'post', 4, '2023-03-16 12:00:00'),
(4, 'Lions are my favorite animals!', 'animal', 1, '2023-03-03 13:00:00'),
(5, 'Orcas are such magnificent creatures.', 'animal', 2, '2023-03-07 14:00:00');

-- 9. Insert quizzes
INSERT INTO quizzes (title, topic, difficulty, is_published, created_at) VALUES
('Big Cat Knowledge', 'Felines', 'medium', TRUE, '2023-04-01 10:00:00'),
('Marine Mammals', 'Ocean Animals', 'easy', TRUE, '2023-04-05 11:00:00'),
('Bird Intelligence', 'Avian Cognition', 'hard', FALSE, '2023-04-10 12:00:00');

-- 10. Insert quiz questions
INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES
(1, 'What is the largest big cat species?', 'Lion', 'Tiger', 'Jaguar', 'Leopard', 'B'),
(1, 'How many subspecies of lion are recognized?', '1', '2', '5', '7', 'B'),
(2, 'Which of these is a toothed whale?', 'Blue whale', 'Humpback whale', 'Orca', 'Gray whale', 'C'),
(2, 'What is a group of orcas called?', 'Herd', 'Pack', 'Pod', 'School', 'C'),
(3, 'Which bird can recognize itself in a mirror?', 'Pigeon', 'Raven', 'Parrot', 'Eagle', 'B');

-- 11. Insert quiz scores
INSERT INTO quiz_scores (user_id, quiz_id, score, taken_at) VALUES
(4, 1, 80, '2023-04-02 10:30:00'),
(5, 1, 90, '2023-04-03 11:30:00'),
(6, 2, 100, '2023-04-06 12:30:00'),
(4, 2, 70, '2023-04-07 13:30:00');

-- 12. Insert gallery items
INSERT INTO gallery (title, type, file_url, caption, tags, submitted_by, approved_by, status, created_at) VALUES
('Lion Sunset', 'photo', 'lion_sunset.jpg', 'Male lion at sunset in Serengeti', 'lion, africa, wildlife', 4, 2, 'approved', '2023-05-01 10:00:00'),
('Orca Breach', 'photo', 'orca_breach.jpg', 'Orca breaching in Alaska', 'orca, whale, marine', 5, 2, 'approved', '2023-05-05 11:00:00'),
('Animal Intelligence Infographic', 'infographic', 'intelligence_info.png', 'Comparing animal cognition levels', 'intelligence, science', 3, 1, 'approved', '2023-05-10 12:00:00'),
('Elephant Sketch', 'art', 'elephant_sketch.jpg', 'Charcoal drawing of Asian elephant', 'elephant, art', 6, NULL, 'pending', '2023-05-15 13:00:00');

-- 13. Insert events
INSERT INTO events (title, description, event_date, type, location, max_attendees, created_by, created_at) VALUES
('Wildlife Photography Workshop', 'Learn techniques for capturing amazing wildlife photos', '2023-07-15 09:00:00', 'workshop', 'Online', 50, 4, '2023-06-01 10:00:00'),
('Marine Conservation Talk', 'Discussion on current marine conservation efforts', '2023-07-20 18:00:00', 'lecture', 'Community Center, Seattle', 100, 5, '2023-06-05 11:00:00'),
('Zoo Volunteer Day', 'Help with enrichment activities for zoo animals', '2023-07-25 08:00:00', 'volunteer', 'Local Zoo', 20, 6, '2023-06-10 12:00:00');

-- 14. Insert event signups
INSERT INTO event_signups (user_id, event_id, signed_up_at) VALUES
(5, 1, '2023-06-02 09:00:00'),
(6, 1, '2023-06-03 10:00:00'),
(4, 2, '2023-06-06 11:00:00'),
(3, 2, '2023-06-07 12:00:00');

-- 15. Insert forum threads
INSERT INTO forum_threads (title, category, views, is_pinned, last_activity_at, author_id, created_at) VALUES
('Best places to see lions?', 'Travel', 120, FALSE, '2023-06-05 14:00:00', 4, '2023-06-01 10:00:00'),
('Animal intelligence research', 'Science', 85, TRUE, '2023-06-10 15:00:00', 3, '2023-06-02 11:00:00'),
('Photography tips needed', 'Photography', 45, FALSE, '2023-06-08 16:00:00', 5, '2023-06-03 12:00:00');

-- 16. Insert forum posts
INSERT INTO forum_posts (thread_id, author_id, content, created_at) VALUES
(1, 5, 'I highly recommend Tanzania for lion sightings!', '2023-06-01 11:00:00'),
(1, 6, 'Botswana also has excellent lion populations.', '2023-06-02 12:00:00'),
(2, 3, 'Recent studies show ravens can plan for the future.', '2023-06-02 13:00:00'),
(2, 4, 'Octopus intelligence is remarkably advanced too.', '2023-06-03 14:00:00'),
(3, 6, 'What camera settings do you use for wildlife?', '2023-06-03 15:00:00');

-- 17. Insert daily features
INSERT INTO daily_feature (animal_id, date) VALUES
(1, '2023-06-01'),
(4, '2023-06-02');

-- 18. Insert badges
INSERT INTO badges (name, description, badge_type, icon) VALUES
('Contributor', 'Submitted 5 approved items', 'milestone', 'contributor.png'),
('Expert', 'Scored 90+ on 3 quizzes', 'milestone', 'expert.png'),
('Community Star', 'Active participant in discussions', 'engagement', 'star.png'),
('Wildlife Hero', 'Special recognition for conservation efforts', 'special', 'hero.png');

-- 19. Insert user badges
INSERT INTO user_badges (user_id, badge_id, awarded_at) VALUES
(4, 1, '2023-06-05 10:00:00'),
(5, 3, '2023-06-10 11:00:00'),
(3, 2, '2023-06-15 12:00:00');

-- 20. Insert leaderboard entries
INSERT INTO leaderboard (user_id, category, points, updated_at) VALUES
(3, 'Research', 150, '2023-06-15 10:00:00'),
(4, 'Photography', 120, '2023-06-15 11:00:00'),
(5, 'Community', 90, '2023-06-15 12:00:00');

-- 21. Insert indigenous knowledge
INSERT INTO indigenous_knowledge (title, region, story, submitted_by, approved_by, status, created_at) VALUES
('Raven in Native American Lore', 'North America', 'Traditional stories about the clever raven...', 5, 2, 'approved', '2023-07-01 10:00:00'),
('Elephants in Thai Culture', 'Southeast Asia', 'The sacred role of elephants in Thailand...', 4, 1, 'approved', '2023-07-05 11:00:00'),
('Lion in Maasai Traditions', 'East Africa', 'The significance of lions to the Maasai people...', 6, NULL, 'pending', '2023-07-10 12:00:00');

-- 22. Insert podcasts
INSERT INTO podcasts (title, file_url, description, tags, duration_seconds, cover_image_url, contributor_id, created_at) VALUES
('Animal Minds', 'animal_minds.mp3', 'Exploring animal intelligence with experts', 'intelligence, science', 3600, 'minds.jpg', 3, '2023-08-01 10:00:00'),
('Ocean Giants', 'ocean_giants.mp3', 'Documentary on marine mammal behavior', 'marine, whales', 2700, 'ocean.jpg', 5, '2023-08-05 11:00:00');

-- 23. Insert settings
INSERT INTO settings (`key`, `value`) VALUES
('site_name', 'Animal IQ Community'),
('maintenance_mode', 'false'),
('default_role', 'enthusiast');

-- 24. Insert logs
INSERT INTO logs (action, user_id, ip_address, user_agent, occurred_at) VALUES
('user_login', 4, '192.168.1.1', 'Mozilla/5.0', '2023-06-01 09:00:00'),
('content_approval', 2, '192.168.1.2', 'Mozilla/5.0', '2023-06-02 10:00:00'),
('quiz_completed', 5, '192.168.1.3', 'Chrome/114.0', '2023-06-03 11:00:00');