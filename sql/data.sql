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

UPDATE posts 
        SET body = 'Lion populations have declined by approximately 43% over the past two decades, with current estimates suggesting only 20,000 lions remain in the wild. Conservation efforts across Africa are multifaceted, focusing on habitat protection, human-wildlife conflict mitigation, and anti-poaching initiatives. 

        Key programs include:
        - The Lion Recovery Fund which supports 65 projects in 23 countries
        - Community conservancies in Kenya that provide economic incentives for conservation
        - GPS collaring programs to track prides and understand their ranges
        - Educational initiatives to reduce retaliatory killings
        
        Challenges remain, particularly in addressing habitat fragmentation and the illegal wildlife trade. Recent successes include population increases in protected areas like the Serengeti and Kruger National Parks, demonstrating that with proper resources, lion conservation can be effective.'
        WHERE title = 'Lion Conservation Efforts';
        

        -- My Encounter with Orcas
        UPDATE posts 
        SET body = 'During my summer expedition to Norway''s Lofoten Islands, I had the extraordinary opportunity to witness orcas in their natural habitat. The pod consisted of 12 individuals, including two calves, hunting herring using sophisticated cooperative techniques. 

        What struck me most was:
        - Their incredible speed and precision when herding fish
        - The complex vocalizations they used to coordinate
        - How the matriarch clearly directed the hunt
        - The playful behavior of the calves between feeding
        
        Scientists on our boat explained that this particular population has developed unique hunting strategies adapted to the Norwegian fjords. The experience fundamentally changed my understanding of marine mammal intelligence and social structures. I left with a profound respect for these apex predators and their fragile Arctic ecosystem.'
        WHERE title = 'My Encounter with Orcas';


        -- Raven Intelligence Experiments
        UPDATE posts 
        SET body = 'Recent studies at the University of Vermont have revealed astonishing cognitive abilities in ravens that rival those of great apes. In controlled experiments, ravens demonstrated:

        1. Tool use: They bent wires to create hooks for retrieving food
        2. Planning: They cached tools for future use, showing foresight
        3. Social learning: Knowledge spread rapidly through groups
        4. Problem-solving: They solved complex multi-step puzzles
        
        Perhaps most remarkably, the ravens showed evidence of "mental time travel" - the ability to remember specific past events and anticipate future needs. This challenges our traditional understanding of avian intelligence and suggests that advanced cognition may have evolved independently in corvids and primates. The implications for our understanding of animal consciousness are profound.'
        WHERE title = 'Raven Intelligence Experiments';
        


        -- Elephants in Asian Culture
        UPDATE posts 
        SET body = 'Elephants have been deeply woven into Asian cultures for over 4,000 years, appearing in religious, artistic, and political contexts across the continent. 

        In Hinduism:
        - Ganesha, the elephant-headed god, is revered as the remover of obstacles
        - Elephants are associated with rainfall and fertility
        
        In Buddhism:
        - The white elephant appeared in Queen Maya''s dream before Buddha''s birth
        - They symbolize mental strength and mindfulness
        
        Historically:
        - War elephants were used across India and Southeast Asia
        - Royal courts kept elephants as symbols of power
        - The Thai king''s white elephants were considered sacred
        
        Today, elephants remain culturally significant but face threats from habitat loss and human-elephant conflict. Conservation efforts increasingly incorporate traditional beliefs to promote coexistence between people and elephants.'
        WHERE title = 'Elephants in Asian Culture';
        

        -- Octopus Camouflage Mechanisms
        UPDATE posts 
        SET body = 'Cutting-edge research published in Nature reveals the astonishing complexity of octopus camouflage systems. These marine invertebrates can change their appearance in less than a second through three simultaneous mechanisms:

        1. Chromatophores: Pigment-containing cells that expand or contract
        2. Iridophores: Light-reflecting cells that create metallic sheens
        3. Papillae: Muscles that alter skin texture to match surroundings

        Recent findings show that:
        - Octopuses don''t just match colors but actually mimic the polarization of light
        - Their skin contains opsins, suggesting it may "see" independently from their eyes
        - Different species have evolved specialized camouflage for their habitats
        - Some can impersonate other marine animals like lionfish or sea snakes
        
        This research has inspired new developments in adaptive camouflage materials for military and medical applications. The octopus''s abilities remain unmatched by human technology, demonstrating the power of evolutionary innovation.'
        WHERE title = 'Octopus Camouflage Mechanisms';
        
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






INSERT INTO animals (scientific_name, common_name, population_estimate, species_status, avg_weight_kg, avg_length_cm, appearance, main_photo, submitted_by, approved_by, status, is_animal_of_the_day, created_at) VALUES
('Ailuropoda melanoleuca', 'Giant Panda', '1,864', 'vulnerable', 100, 150, 'Black and white bear with distinctive markings', 'panda.jpg', 3, 1, 'approved', FALSE, '2023-03-01 09:00:00'),
('Ursus maritimus', 'Polar Bear', '22,000-31,000', 'vulnerable', 450, 250, 'White fur with black skin, large paws', 'polar_bear.jpg', 4, 2, 'approved', TRUE, '2023-03-02 10:00:00'),
('Panthera tigris', 'Tiger', '3,900', 'endangered', 220, 290, 'Orange with black stripes, white underside', 'tiger.jpg', 5, 1, 'approved', FALSE, '2023-03-03 11:00:00'),
('Gorilla beringei', 'Mountain Gorilla', '1,063', 'endangered', 160, 150, 'Dark fur, muscular build, prominent brow ridge', 'gorilla.jpg', 6, 2, 'approved', FALSE, '2023-03-04 12:00:00'),
('Balaenoptera musculus', 'Blue Whale', '10,000-25,000', 'endangered', 100000, 2500, 'Blue-gray body with mottled patterns, enormous size', 'blue_whale.jpg', 3, 1, 'approved', TRUE, '2023-03-05 13:00:00'),
('Canis lupus', 'Gray Wolf', '300,000', 'least concern', 40, 105, 'Gray fur with variations, bushy tail', 'wolf.jpg', 4, 2, 'approved', FALSE, '2023-03-06 14:00:00'),
('Pongo pygmaeus', 'Bornean Orangutan', '104,700', 'critically endangered', 75, 120, 'Reddish-brown fur, long arms, pronounced cheek pads in males', 'orangutan.jpg', 5, 1, 'approved', FALSE, '2023-03-07 15:00:00'),
('Acinonyx jubatus', 'Cheetah', '7,100', 'vulnerable', 50, 110, 'Tan with black spots, slender body, tear marks', 'cheetah.jpg', 6, 2, 'approved', FALSE, '2023-03-08 16:00:00'),
('Spheniscus demersus', 'African Penguin', '41,700', 'endangered', 3.5, 60, 'Black and white with pink gland above eyes', 'penguin.jpg', 3, 1, 'approved', FALSE, '2023-03-09 17:00:00'),
('Crocodylus niloticus', 'Nile Crocodile', '50,000-70,000', 'least concern', 500, 450, 'Grayish-green with armored scales, powerful jaws', 'crocodile.jpg', 4, 2, 'approved', TRUE, '2023-03-10 18:00:00'),
('Bubo scandiacus', 'Snowy Owl', '28,000', 'vulnerable', 2, 60, 'White plumage with dark markings, yellow eyes', 'snowy_owl.jpg', 5, 1, 'approved', FALSE, '2023-03-11 19:00:00'),
('Loxodonta africana', 'African Elephant', '415,000', 'vulnerable', 6000, 600, 'Gray skin, large ears, both sexes have tusks', 'african_elephant.jpg', 6, 2, 'approved', FALSE, '2023-03-12 20:00:00'),
('Vulpes vulpes', 'Red Fox', 'Unknown (millions)', 'least concern', 6, 90, 'Reddish fur with white underside, bushy tail', 'fox.jpg', 3, 1, 'approved', FALSE, '2023-03-13 21:00:00'),
('Giraffa camelopardalis', 'Giraffe', '68,000', 'vulnerable', 1200, 550, 'Tall with long neck, irregular brown patches', 'giraffe.jpg', 4, 2, 'approved', TRUE, '2023-03-14 22:00:00'),
('Hippopotamus amphibius', 'Hippopotamus', '115,000-130,000', 'vulnerable', 1500, 350, 'Grayish-purple thick skin, massive mouth', 'hippo.jpg', 5, 1, 'approved', FALSE, '2023-03-15 23:00:00'),
('Puma concolor', 'Mountain Lion', '50,000', 'least concern', 65, 150, 'Tan coat, slender body, long tail', 'mountain_lion.jpg', 6, 2, 'approved', FALSE, '2023-03-16 00:00:00'),
('Alligator mississippiensis', 'American Alligator', '5,000,000', 'least concern', 360, 360, 'Dark green/black with broad snout', 'alligator.jpg', 3, 1, 'approved', FALSE, '2023-03-17 01:00:00'),
('Ursus arctos', 'Brown Bear', '200,000', 'least concern', 300, 200, 'Brown fur, muscular hump on shoulders', 'brown_bear.jpg', 4, 2, 'approved', FALSE, '2023-03-18 02:00:00'),
('Panthera pardus', 'Leopard', 'Unknown (declining)', 'vulnerable', 60, 190, 'Golden coat with rosette patterns', 'leopard.jpg', 5, 1, 'approved', TRUE, '2023-03-19 03:00:00'),
('Macropus rufus', 'Red Kangaroo', '11,500,000', 'least concern', 85, 140, 'Reddish-brown fur, muscular tail, large feet', 'kangaroo.jpg', 6, 2, 'approved', FALSE, '2023-03-20 04:00:00'),
('Pteropus vampyrus', 'Large Flying Fox', 'Unknown', 'near threatened', 1.1, 30, 'Black fur with reddish-brown collar, large wings', 'flying_fox.jpg', 3, 1, 'approved', FALSE, '2023-03-21 05:00:00'),
('Camelus dromedarius', 'Dromedary Camel', 'Unknown (domesticated)', 'domesticated', 500, 300, 'Light brown with single hump, long legs', 'camel.jpg', 4, 2, 'approved', FALSE, '2023-03-22 06:00:00'),
('Bison bison', 'American Bison', '31,000 (wild)', 'near threatened', 800, 350, 'Dark brown with massive head and hump', 'bison.jpg', 5, 1, 'approved', FALSE, '2023-03-23 07:00:00'),
('Orycteropus afer', 'Aardvark', 'Unknown', 'least concern', 60, 120, 'Pinkish-gray skin, long snout, rabbit-like ears', 'aardvark.jpg', 6, 2, 'approved', FALSE, '2023-03-24 08:00:00'),
('Equus zebra', 'Mountain Zebra', '34,000', 'vulnerable', 350, 220, 'Black and white stripes, upright mane', 'zebra.jpg', 3, 1, 'approved', TRUE, '2023-03-25 09:00:00'),
('Tursiops truncatus', 'Bottlenose Dolphin', '750,000', 'least concern', 300, 250, 'Gray with lighter belly, short beak', 'dolphin.jpg', 4, 2, 'approved', FALSE, '2023-03-26 10:00:00'),
('Phoenicopterus roseus', 'Greater Flamingo', '550,000-680,000', 'least concern', 4, 150, 'Pink plumage with black flight feathers', 'flamingo.jpg', 5, 1, 'approved', FALSE, '2023-03-27 11:00:00'),
('Procyon lotor', 'Raccoon', 'Unknown (millions)', 'least concern', 6, 70, 'Grayish fur with black mask and ringed tail', 'raccoon.jpg', 6, 2, 'approved', FALSE, '2023-03-28 12:00:00'),
('Alces alces', 'Moose', '1,500,000', 'least concern', 500, 270, 'Dark brown with paler legs, males have broad antlers', 'moose.jpg', 3, 1, 'approved', FALSE, '2023-03-29 13:00:00'),
('Physeter macrocephalus', 'Sperm Whale', '360,000', 'vulnerable', 35000, 1100, 'Dark gray with massive square head', 'sperm_whale.jpg', 4, 2, 'approved', FALSE, '2023-03-30 14:00:00');




INSERT INTO animal_photos (animal_id, photo_url, caption) VALUES
(6, 'panda1.jpg', 'Giant panda eating bamboo'),
(6, 'panda2.jpg', 'Panda cub climbing tree'),
(7, 'polar_bear1.jpg', 'Polar bear on ice floe'),
(7, 'polar_bear2.jpg', 'Mother polar bear with cubs'),
(8, 'tiger1.jpg', 'Tiger stalking through grass'),
(8, 'tiger2.jpg', 'Tiger swimming in water'),
(9, 'gorilla1.jpg', 'Mountain gorilla silverback'),
(9, 'gorilla2.jpg', 'Gorilla family group'),
(10, 'blue_whale1.jpg', 'Blue whale surfacing'),
(10, 'blue_whale2.jpg', 'Blue whale tail fluke'),
(11, 'wolf1.jpg', 'Gray wolf howling'),
(11, 'wolf2.jpg', 'Wolf pack in snow'),
(12, 'orangutan1.jpg', 'Orangutan in the trees'),
(12, 'orangutan2.jpg', 'Young orangutan'),
(13, 'cheetah1.jpg', 'Cheetah running at full speed'),
(13, 'cheetah2.jpg', 'Cheetah with cubs'),
(14, 'penguin1.jpg', 'African penguin colony'),
(14, 'penguin2.jpg', 'Penguin swimming underwater'),
(15, 'crocodile1.jpg', 'Nile crocodile with mouth open'),
(15, 'crocodile2.jpg', 'Crocodile basking in sun'),
(16, 'snowy_owl1.jpg', 'Snowy owl in flight'),
(16, 'snowy_owl2.jpg', 'Owl perched on snow'),
(17, 'african_elephant1.jpg', 'African elephant herd'),
(17, 'african_elephant2.jpg', 'Elephant spraying water'),
(18, 'fox1.jpg', 'Red fox in winter coat'),
(18, 'fox2.jpg', 'Fox with prey'),
(19, 'giraffe1.jpg', 'Giraffe eating leaves'),
(19, 'giraffe2.jpg', 'Giraffes necking'),
(20, 'hippo1.jpg', 'Hippo in water'),
(20, 'hippo2.jpg', 'Hippo yawning'),
(21, 'mountain_lion1.jpg', 'Mountain lion on rock'),
(21, 'mountain_lion2.jpg', 'Cougar stalking prey'),
(22, 'alligator1.jpg', 'Alligator in swamp'),
(22, 'alligator2.jpg', 'Baby alligator'),
(23, 'brown_bear1.jpg', 'Brown bear fishing'),
(23, 'brown_bear2.jpg', 'Bear standing up'),
(24, 'leopard1.jpg', 'Leopard in tree'),
(24, 'leopard2.jpg', 'Leopard with kill'),
(25, 'kangaroo1.jpg', 'Red kangaroo hopping'),
(25, 'kangaroo2.jpg', 'Kangaroo with joey'),
(26, 'flying_fox1.jpg', 'Flying fox hanging'),
(26, 'flying_fox2.jpg', 'Bat in flight'),
(27, 'camel1.jpg', 'Dromedary camel in desert'),
(27, 'camel2.jpg', 'Camel caravan'),
(28, 'bison1.jpg', 'American bison herd'),
(28, 'bison2.jpg', 'Bison in snow'),
(29, 'aardvark1.jpg', 'Aardvark foraging'),
(29, 'aardvark2.jpg', 'Aardvark close-up'),
(30, 'zebra1.jpg', 'Mountain zebra grazing'),
(30, 'zebra2.jpg', 'Zebra stripes close-up'),
(31, 'dolphin1.jpg', 'Dolphin jumping'),
(31, 'dolphin2.jpg', 'Dolphin pod swimming'),
(32, 'flamingo1.jpg', 'Flamingo flock'),
(32, 'flamingo2.jpg', 'Flamingo feeding'),
(33, 'raccoon1.jpg', 'Raccoon in tree'),
(33, 'raccoon2.jpg', 'Raccoon washing food'),
(34, 'moose1.jpg', 'Moose in wetland'),
(34, 'moose2.jpg', 'Bull moose with antlers'),
(35, 'sperm_whale1.jpg', 'Sperm whale diving'),
(35, 'sperm_whale2.jpg', 'Whale tail above water');




INSERT INTO taxonomy (animal_id, kingdom, phylum, class, `order`, family, genus, species) VALUES
(6, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Ursidae', 'Ailuropoda', 'melanoleuca'),
(7, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Ursidae', 'Ursus', 'maritimus'),
(8, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Felidae', 'Panthera', 'tigris'),
(9, 'Animalia', 'Chordata', 'Mammalia', 'Primates', 'Hominidae', 'Gorilla', 'beringei'),
(10, 'Animalia', 'Chordata', 'Mammalia', 'Cetacea', 'Balaenopteridae', 'Balaenoptera', 'musculus'),
(11, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Canidae', 'Canis', 'lupus'),
(12, 'Animalia', 'Chordata', 'Mammalia', 'Primates', 'Hominidae', 'Pongo', 'pygmaeus'),
(13, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Felidae', 'Acinonyx', 'jubatus'),
(14, 'Animalia', 'Chordata', 'Aves', 'Sphenisciformes', 'Spheniscidae', 'Spheniscus', 'demersus'),
(15, 'Animalia', 'Chordata', 'Reptilia', 'Crocodylia', 'Crocodylidae', 'Crocodylus', 'niloticus'),
(16, 'Animalia', 'Chordata', 'Aves', 'Strigiformes', 'Strigidae', 'Bubo', 'scandiacus'),
(17, 'Animalia', 'Chordata', 'Mammalia', 'Proboscidea', 'Elephantidae', 'Loxodonta', 'africana'),
(18, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Canidae', 'Vulpes', 'vulpes'),
(19, 'Animalia', 'Chordata', 'Mammalia', 'Artiodactyla', 'Giraffidae', 'Giraffa', 'camelopardalis'),
(20, 'Animalia', 'Chordata', 'Mammalia', 'Artiodactyla', 'Hippopotamidae', 'Hippopotamus', 'amphibius'),
(21, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Felidae', 'Puma', 'concolor'),
(22, 'Animalia', 'Chordata', 'Reptilia', 'Crocodylia', 'Alligatoridae', 'Alligator', 'mississippiensis'),
(23, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Ursidae', 'Ursus', 'arctos'),
(24, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Felidae', 'Panthera', 'pardus'),
(25, 'Animalia', 'Chordata', 'Mammalia', 'Diprotodontia', 'Macropodidae', 'Macropus', 'rufus'),
(26, 'Animalia', 'Chordata', 'Mammalia', 'Chiroptera', 'Pteropodidae', 'Pteropus', 'vampyrus'),
(27, 'Animalia', 'Chordata', 'Mammalia', 'Artiodactyla', 'Camelidae', 'Camelus', 'dromedarius'),
(28, 'Animalia', 'Chordata', 'Mammalia', 'Artiodactyla', 'Bovidae', 'Bison', 'bison'),
(29, 'Animalia', 'Chordata', 'Mammalia', 'Tubulidentata', 'Orycteropodidae', 'Orycteropus', 'afer'),
(30, 'Animalia', 'Chordata', 'Mammalia', 'Perissodactyla', 'Equidae', 'Equus', 'zebra'),
(31, 'Animalia', 'Chordata', 'Mammalia', 'Cetacea', 'Delphinidae', 'Tursiops', 'truncatus'),
(32, 'Animalia', 'Chordata', 'Aves', 'Phoenicopteriformes', 'Phoenicopteridae', 'Phoenicopterus', 'roseus'),
(33, 'Animalia', 'Chordata', 'Mammalia', 'Carnivora', 'Procyonidae', 'Procyon', 'lotor'),
(34, 'Animalia', 'Chordata', 'Mammalia', 'Artiodactyla', 'Cervidae', 'Alces', 'alces'),
(35, 'Animalia', 'Chordata', 'Mammalia', 'Cetacea', 'Physeteridae', 'Physeter', 'macrocephalus');



INSERT INTO animal_habits (animal_id, diet, mating_habits, behavior, habitat) VALUES
(6, 'Herbivorous: 99% bamboo', 'Polygynous, females raise cubs alone', 'Solitary, excellent climbers', 'Mountain forests'),
(7, 'Carnivorous: seals, fish', 'Polygynous, males compete for females', 'Solitary except mothers with cubs', 'Arctic ice'),
(8, 'Carnivorous: deer, wild boar', 'Polygynous, territorial', 'Solitary, ambush predators', 'Various forest types'),
(9, 'Herbivorous: leaves, stems', 'Polygynous, dominant silverback leads', 'Social, live in troops', 'Mountain forests'),
(10, 'Carnivorous: krill, small fish', 'Polygynous, long migrations for mating', 'Solitary or small groups', 'Open ocean'),
(11, 'Carnivorous: ungulates, smaller mammals', 'Monogamous pairs, pack structure', 'Highly social, cooperative hunters', 'Various including tundra'),
(12, 'Omnivorous: fruits, insects', 'Polygynous, males attract females with calls', 'Semi-solitary, arboreal', 'Rainforest canopy'),
(13, 'Carnivorous: gazelles, impalas', 'Polygynous, males form coalitions', 'Diurnal, fastest land animal', 'Open plains'),
(14, 'Carnivorous: fish, squid', 'Monogamous, colonial nesters', 'Social, live in colonies', 'Coastal islands'),
(15, 'Carnivorous: fish, mammals', 'Polygynous, males defend territories', 'Ambush predators, bask in sun', 'Freshwater habitats'),
(16, 'Carnivorous: lemmings, rabbits', 'Monogamous, territorial', 'Diurnal in Arctic summer', 'Tundra'),
(17, 'Herbivorous: grasses, leaves', 'Polygynous, males compete for access', 'Matriarchal herds, strong bonds', 'Savanna, forests'),
(18, 'Omnivorous: small animals, fruits', 'Monogamous seasonal pairs', 'Solitary, adaptable', 'Various including urban'),
(19, 'Herbivorous: leaves, twigs', 'Polygynous, males neck-fight', 'Social, loose herds', 'Savanna, woodlands'),
(20, 'Herbivorous: grasses', 'Polygynous, males defend territories', 'Semi-aquatic, nocturnal', 'Rivers, lakes'),
(21, 'Carnivorous: deer, smaller mammals', 'Polygynous, solitary', 'Nocturnal, territorial', 'Mountains, forests'),
(22, 'Carnivorous: fish, turtles', 'Polygynous, males bellow to attract', 'Solitary, build "gator holes"', 'Freshwater wetlands'),
(23, 'Omnivorous: fish, berries', 'Polygynous, males compete', 'Solitary except mothers with cubs', 'Forests, tundra'),
(24, 'Carnivorous: antelope, monkeys', 'Polygynous, no fixed breeding season', 'Solitary, nocturnal', 'Various including forests'),
(25, 'Herbivorous: grasses, plants', 'Polygynous, males fight for access', 'Social, live in mobs', 'Arid grasslands'),
(26, 'Herbivorous: fruits, nectar', 'Polygynous, harem structure', 'Nocturnal, roost in colonies', 'Forests near water'),
(27, 'Herbivorous: grasses, leaves', 'Polygynous, seasonal breeders', 'Domesticated, used as pack animals', 'Deserts'),
(28, 'Herbivorous: grasses, sedges', 'Polygynous, males compete', 'Social, live in herds', 'Grasslands'),
(29, 'Insectivorous: ants, termites', 'Solitary, brief mating encounters', 'Nocturnal, burrowers', 'Savanna, woodlands'),
(30, 'Herbivorous: grasses, leaves', 'Polygynous, males defend territories', 'Social, live in herds', 'Mountain slopes'),
(31, 'Carnivorous: fish, squid', 'Polygynous, complex social structure', 'Highly intelligent, playful', 'Coastal waters'),
(32, 'Omnivorous: algae, crustaceans', 'Monogamous, colonial nesters', 'Social, stand on one leg', 'Shallow wetlands'),
(33, 'Omnivorous: fruits, small animals', 'Polygynous, males roam widely', 'Nocturnal, adaptable', 'Various including urban'),
(34, 'Herbivorous: aquatic plants', 'Polygynous, males compete', 'Solitary except in mating season', 'Forested wetlands'),
(35, 'Carnivorous: squid, fish', 'Polygynous, males compete', 'Social, deep divers', 'Deep ocean waters');