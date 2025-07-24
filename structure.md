animal-iq-community/
│
├── public/                      # Publicly accessible directory (document root)
│   ├── assets/                  # Static assets (CSS, JS, images, etc.)
│   │   ├── css/
│   │   ├── js/
│   │   └── images/              # Static icons, logo, banners
│   ├── index.php                # Homepage
│   ├── encyclopedia.php         # Animal Encyclopedia
│   ├── blog.php                 # Blog & Stories
│   ├── quizzes.php              # Quizzes & Games
│   ├── gallery.php              # Multimedia Gallery
│   ├── learn.php                # Learn & Explore
│   ├── events.php               # Events Calendar
│   ├── forum.php                # Community Forum
│   ├── contribute.php           # Contribution Page
│   ├── support.php              # Support & Donate
│   ├── login.php                # Login Page
│   ├── register.php             # Register Page
│   ├── logout.php               # Logout Logic
│   └── profile.php              # User Dashboard/Profile
│
├── admin/                       # Admin backend
│   ├── dashboard.php            # Admin Dashboard
│   ├── manage-users.php         # Role/User Management
│   ├── content-review.php       # Pending Content Review
│   ├── forum-moderation.php     # Forum Tools
│   ├── encyclopedia-editor.php  # Manage Animal Entries
│   ├── blog-editor.php          # Manage Blog Posts
│   ├── event-manager.php        # Event Management
│   ├── settings.php             # Platform Settings
│   ├── logs.php                 # Backup/Logs
│   └── analytics.php            # Analytics Dashboard
│
├── includes/                    # Reusable backend components
│   ├── db.php                   # DB connection
│   ├── functions.php            # General helper functions
│   ├── auth.php                 # Auth/session checks
│   ├── header.php               # Common header
│   ├── footer.php               # Common footer
│   ├── nav.php                  # Main nav menu
│   └── mailer.php               # Email functions
│
├── api/                         # API endpoints (AJAX, JSON)
│   ├── login.php
│   ├── register.php
│   ├── submit-contribution.php
│   ├── get-animal.php
│   ├── quiz-score.php
│   ├── upgrade-role.php
│   ├── submit-comment.php
│   └── fetch-leaderboard.php
│
├── uploads/                     # User-generated content (secure access)
│   ├── images/                  # Animal photos, infographics, sightings
│   ├── audio/                   # Podcasts, interviews
│   ├── documents/               # Research papers, summaries
│   └── profile_pics/            # User profile pictures
│
├── cron/                        # Cron scripts (automated tasks)
│   └── rotate_daily_animal.php # Updates Animal of the Day daily
│
├── sql/                         # SQL database initialization
│   └── schema.sql               # Full DB schema
│
├── .env                         # Environment config (DB, API keys)
├── .htaccess                    # Apache rewrite rules (if using Apache)
└── README.md                    # Project overview and setup instructions
