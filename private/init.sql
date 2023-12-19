CREATE TABLE xeno_items(
    `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    'image_filename' VARCHAR(255) NOT NULL,
    'item_name' VARCHAR(255) NOT NULL,
    'item_type' VARCHAR(255) NOT NULL,
    'description' TEXT NOT NULL,
    'collectors_set' VARCHAR(255) NOT NULL,
    'buy_price' DECIMAL(6) NOT NULL,
    'sell_price' DECIMAL(6) NOT NULL,
    'retired' BOOLEAN NOT NULL,
    'release_year' INTEGER NOT NULL,
    'release_region' VARCHAR(255) NOT NULL,
    'favorite' BOOLEAN NOT NULL,
    `uploaded_on` datetime NOT NULL,
PRIMARY KEY(`id`)
);

CREATE TABLE catalogue_admin(
    'account_id' INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
    'users' VARCHAR(16) NOT NULL,
    'hashed_pass' VARCHAR(72) NOT NULL
PRIMARY KEY(`account_id`)
)

INSERT INTO users (users, hashed_pass)
VALUES 
('mihiri', '$2y$10$jRtgzbv.ct/VBqVg/Xdeb.fhmKKdQoZ42trCQxbIhtN0/ZcH6QWyO'),
('instructor', '$2y$10$r11rcu9fFXA3.bFD485l5ONbBy9Ce3/Q/1BoDW8Pnq7AS3CLI7vLO');

-- for random fun fact

CREATE TABLE xeno_facts(
    `fid` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `fact` TEXT NOT NULL,
PRIMARY KEY(`fid`)
    );
