DROP
DATABASE IF EXISTS alf;
CREATE
DATABASE alf;
USE
alf;

CREATE TABLE specialties
(
    kspecialty  INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

CREATE TABLE members
(
    kmember       INT PRIMARY KEY AUTO_INCREMENT,
    first_name    VARCHAR(50)  NOT NULL,
    last_name     VARCHAR(50)  NOT NULL,
    fkspecialty   INT,
    photo_url     VARCHAR(255),
    bio           TEXT,
    email         VARCHAR(100),
    password_hash VARCHAR(255) NOT NULL,
    is_active     BOOLEAN   DEFAULT true,
    is_admin      BOOLEAN   DEFAULT false,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fkspecialty) REFERENCES specialties (kspecialty)
);

CREATE TABLE contact
(
    kmessage     INT PRIMARY KEY AUTO_INCREMENT,
    subject      VARCHAR(255) NOT NULL,
    body         TEXT         NOT NULL,
    sender_email VARCHAR(100),
    fkmember     INT,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (fkmember) REFERENCES members (kmember)
);

INSERT INTO specialties (name, description)
VALUES ('Feline Advocacy', 'Champions of cat freedom, fighting for every purr''s right to roam and rule the home!'),
       ('Digital Purr-motion', 'Masters of the web, spreading the gospel of feline fabulousness one click at a time.'),
       ('Cat-egorical Finance', 'Keepers of the kitty, ensuring every whisker is accounted for in the budget.'),
       ('Whisker Events', 'Organizers of purr-fect gatherings, from adoption parties to catnip soirees.'),
       ('Paw-tography & Design', 'Artists who capture the essence of cats in colorful, claw-some creations.'),
       ('Kitty Counsel', 'Wise cats whispering truths about independence and the art of choosing one''s humans.'),
       ('Meow-tivation', 'Cheerleaders of the team, keeping spirits high with purrs and playful pounces.'),
       ('Claw-some Tech', 'Tech wizards making sure the digital litter box runs smoothly and scratch-free.');

INSERT INTO members (first_name, last_name, fkspecialty, bio, email, password_hash, is_active, is_admin)
VALUES
-- Non-admin members
('Whiskers', 'McPaws', 7, 'I cheer on the team with a purr and a pounce—motivation is my catnip!',
 'whiskers.mcpaws@alf.org', 'hashedpassword1', true, false),
('Tabby', 'Ledger', 3, 'Counting whiskers and coins to keep our kitty kingdom thriving!', 'tabby.ledger@alf.org',
 'hashedpassword2', true, false),
('Paws', 'Picasso', 5, 'Turning feline beauty into colorful art—no cat too sassy to capture!', 'paws.picasso@alf.org',
 'hashedpassword3', true, false),
-- Admin members
('Albert', 'Féral', 1, 'Founder and feline freedom fighter—leading the purr-revolution since 1986!',
 'albert.feral@alf.org', 'hashedpassword4', true, true),
('Clawdia', 'Techpaws', 8, 'Keeping our digital scratching post sharp and purr-forming!', 'clawdia.techpaws@alf.org',
 'hashedpassword5', true, true);