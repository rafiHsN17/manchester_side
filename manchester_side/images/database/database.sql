-- Letakkan di ROOT folder
CREATE DATABASE IF NOT EXISTS manchester_side;
USE manchester_side;

-- Table untuk berita
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    team ENUM('manchester-united', 'manchester-city') NOT NULL,
    category ENUM('news', 'transfer', 'injury', 'match', 'analysis') DEFAULT 'news',
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table untuk admin
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table untuk jadwal pertandingan
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    home_team VARCHAR(100) NOT NULL,
    away_team VARCHAR(100) NOT NULL,
    match_date DATE NOT NULL,
    match_time TIME,
    competition VARCHAR(100),
    venue VARCHAR(100),
    score VARCHAR(20),
    status ENUM('upcoming', 'completed', 'live') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table untuk statistik head-to-head
CREATE TABLE head_to_head (
    id INT AUTO_INCREMENT PRIMARY KEY,
    match_date DATE NOT NULL,
    home_team VARCHAR(100),
    away_team VARCHAR(100),
    competition VARCHAR(100),
    score VARCHAR(20),
    venue VARCHAR(100)
);

-- Insert admin default (password: admin123)
INSERT INTO admins (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample berita
INSERT INTO articles (title, content, team, category) VALUES
('Rashford Cetak Gol Kemenangan Melawan Chelsea', 'Marcus Rashford kembali menjadi pahlawan Manchester United dengan gol kemenangan melawan Chelsea di Old Trafford. Performa impresif Rashford membawa MU meraih 3 poin penting.', 'manchester-united', 'news'),
('Haaland Raih Hat-trick di Etihad Stadium', 'Erling Haaland menunjukkan kelas dunia dengan mencetak hat-trick melawan Tottenham. Striker Norwegia ini semakin memantapkan diri sebagai top scorer Premier League.', 'manchester-city', 'news'),
('MU Incar Striker Bayern Munich', 'Manchester United dikabarkan tertarik merekrut striker Bayern Munich untuk memperkuat lini serang musim depan.', 'manchester-united', 'transfer'),
('Pogba Cedera Hamstring', 'Paul Pogba dipastikan absen selama 4 minggu akibat cedera hamstring yang dialaminya saat latihan.', 'manchester-united', 'injury');

-- Insert sample jadwal pertandingan
INSERT INTO matches (home_team, away_team, match_date, match_time, competition, venue, status) VALUES
('Manchester United', 'Liverpool', '2024-02-01', '16:30:00', 'Premier League', 'Old Trafford', 'upcoming'),
('Manchester City', 'Chelsea', '2024-02-02', '15:00:00', 'Premier League', 'Etihad Stadium', 'upcoming'),
('Manchester United', 'Manchester City', '2024-03-05', '17:30:00', 'Premier League', 'Old Trafford', 'upcoming'),
('Manchester United', 'Arsenal', '2024-01-15', '15:00:00', 'Premier League', 'Emirates Stadium', 'completed'),
('Manchester City', 'Tottenham', '2024-01-14', '14:00:00', 'Premier League', 'Tottenham Hotspur Stadium', 'completed');

-- Insert sample head-to-head
INSERT INTO head_to_head (match_date, home_team, away_team, competition, score, venue) VALUES
('2023-10-29', 'Manchester United', 'Manchester City', 'Premier League', '0-3', 'Old Trafford'),
('2023-06-03', 'Manchester City', 'Manchester United', 'FA Cup Final', '2-1', 'Wembley Stadium'),
('2023-01-14', 'Manchester United', 'Manchester City', 'Premier League', '2-1', 'Old Trafford');