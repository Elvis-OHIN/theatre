<?php
$databasePath = __DIR__ . '/var/database.sqlite';
$databaseDir = dirname($databasePath);

if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0777, true);
}

$pdo = new \PDO('sqlite:' . $databasePath);
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);


$pdo->exec("CREATE TABLE IF NOT EXISTS spectacles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    capacity INTEGER NOT NULL
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    spectacle_id INTEGER NOT NULL,
    seatNumber INTEGER NOT NULL,
    rang TEXT NOT NULL,
    FOREIGN KEY (spectacle_id) REFERENCES spectacles (id)
)");

$spectacles = [
    ['name' => 'Spectacle 1', 'description' => 'Description du Spectacle 1', 'capacity' => 127],
    ['name' => 'Spectacle 2', 'description' => 'Description du Spectacle 2', 'capacity' => 163],
    ['name' => 'Spectacle 3', 'description' => 'Description du Spectacle 3', 'capacity' => 197],
];

$stmt = $pdo->prepare("INSERT INTO spectacles (name, description, capacity) VALUES (:name, :description, :capacity)");

foreach ($spectacles as $spectacle) {
    $stmt->execute([
        ':name' => $spectacle['name'],
        ':description' => $spectacle['description'],
        ':capacity' => $spectacle['capacity'],
    ]);
}

