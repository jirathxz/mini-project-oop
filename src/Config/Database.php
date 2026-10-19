<?php

namespace CardVault\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static string $driver = '';

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
            self::bootstrap(self::$instance);
        }

        return self::$instance;
    }

    public static function getDriver(): string
    {
        return self::$driver;
    }

    private static function createConnection(): PDO
    {
        // 1. Attempt MySQL connection (WAMP default: root without password)
        $mysqlHost = getenv('DB_HOST') ?: '127.0.0.1';
        $mysqlPort = getenv('DB_PORT') ?: '3306';
        $mysqlDb   = getenv('DB_NAME') ?: 'cardvault';
        $mysqlUser = getenv('DB_USER') ?: 'root';
        $mysqlPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

        try {
            $dsn = "mysql:host={$mysqlHost};port={$mysqlPort};charset=utf8mb4";
            $pdo = new PDO($dsn, $mysqlUser, $mysqlPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 1,
            ]);

            // Ensure database exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$mysqlDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$mysqlDb}`");

            self::$driver = 'mysql';
            return $pdo;
        } catch (PDOException $e) {
            // MySQL unavailable; fall back to SQLite
        }

        // 2. SQLite fallback (database/cardvault.sqlite)
        $dbDir = dirname(__DIR__, 2) . '/database';
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        $sqliteFile = $dbDir . '/cardvault.sqlite';
        $pdo = new PDO("sqlite:{$sqliteFile}", null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $pdo->exec("PRAGMA foreign_keys = ON;");
        self::$driver = 'sqlite';

        return $pdo;
    }

    public static function bootstrap(PDO $pdo): void
    {
        // Check if categories table already exists
        try {
            $stmt = $pdo->query("SELECT 1 FROM categories LIMIT 1");
            if ($stmt !== false) {
                self::ensureAdminsTable($pdo);
                return; // Already bootstrapped
            }
        } catch (PDOException $e) {
            // Table doesn't exist, proceed with schema migration
        }

        self::createSchema($pdo);
        self::seedData($pdo);
        self::ensureAdminsTable($pdo);
    }

    private static function ensureAdminsTable(PDO $pdo): void
    {
        $isSqlite = (self::$driver === 'sqlite');
        $pk = $isSqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';
        $timestamp = $isSqlite ? 'DATETIME DEFAULT CURRENT_TIMESTAMP' : 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP';

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admins (
                id {$pk},
                username VARCHAR(100) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                name VARCHAR(100) DEFAULT 'Store Manager',
                role VARCHAR(50) DEFAULT 'admin',
                created_at {$timestamp}
            );
        ");

        $checkStmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
        if ((int)$checkStmt->fetchColumn() === 0) {
            $adminStmt = $pdo->prepare("INSERT INTO admins (username, password_hash, name, role) VALUES (?, ?, ?, 'admin')");
            $defaultHash = password_hash('admin1234', PASSWORD_BCRYPT);
            $adminStmt->execute(['admin', $defaultHash, 'ผู้ดูแลระบบ CARDVAULT']);
        }
    }

    private static function createSchema(PDO $pdo): void
    {
        $isSqlite = (self::$driver === 'sqlite');
        $pk = $isSqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT AUTO_INCREMENT PRIMARY KEY';
        $timestamp = $isSqlite ? 'DATETIME DEFAULT CURRENT_TIMESTAMP' : 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP';

        $schema = "
        CREATE TABLE IF NOT EXISTS categories (
            id {$pk},
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            created_at {$timestamp}
        );

        CREATE TABLE IF NOT EXISTS platforms (
            id {$pk},
            category_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            region VARCHAR(20) DEFAULT 'TH',
            delivery_type VARCHAR(50) DEFAULT 'Instant Digital Key',
            description TEXT,
            logo_url VARCHAR(255),
            created_at {$timestamp}
        );

        CREATE TABLE IF NOT EXISTS denominations (
            id {$pk},
            platform_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            face_value DECIMAL(10, 2) NOT NULL,
            currency VARCHAR(10) DEFAULT 'THB',
            created_at {$timestamp}
        );

        CREATE TABLE IF NOT EXISTS vouchers (
            id {$pk},
            denomination_id INT NOT NULL,
            code VARCHAR(255) NOT NULL,
            serial VARCHAR(100),
            status VARCHAR(20) DEFAULT 'available',
            order_id INT NULL,
            reserved_until DATETIME NULL,
            created_at {$timestamp}
        );

        CREATE TABLE IF NOT EXISTS orders (
            id {$pk},
            order_ref VARCHAR(50) NOT NULL UNIQUE,
            guest_email VARCHAR(255) NOT NULL,
            total_amount DECIMAL(10, 2) NOT NULL,
            payment_method VARCHAR(50) DEFAULT 'promptpay',
            status VARCHAR(20) DEFAULT 'pending',
            paid_at DATETIME NULL,
            expires_at DATETIME NOT NULL,
            created_at {$timestamp}
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id {$pk},
            order_id INT NOT NULL,
            denomination_id INT NOT NULL,
            quantity INT NOT NULL,
            unit_price DECIMAL(10, 2) NOT NULL,
            subtotal DECIMAL(10, 2) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS otp_sessions (
            id {$pk},
            email VARCHAR(255) NOT NULL,
            otp_code VARCHAR(10) NOT NULL,
            is_verified INT DEFAULT 0,
            expires_at DATETIME NOT NULL,
            created_at {$timestamp}
        );
        ";

        if ($isSqlite) {
            $pdo->exec($schema);
        } else {
            // Split and run statements for MySQL PDO
            $queries = array_filter(array_map('trim', explode(';', $schema)));
            foreach ($queries as $query) {
                if (!empty($query)) {
                    $pdo->exec($query);
                }
            }
        }
    }

    private static function seedData(PDO $pdo): void
    {
        // 1. Categories
        $categories = [
            ['Gift Cards', 'gift-cards'],
            ['Streaming', 'streaming'],
            ['Gaming', 'gaming'],
        ];

        $catStmt = $pdo->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        foreach ($categories as $cat) {
            $catStmt->execute($cat);
        }

        // 2. Platforms
        $platforms = [
            [1, 'Google Play', 'google-play', 'TH', 'Instant Digital Key', 'เติมเงิน Google Play ซื้อแอปและเกม', 'google-play.png'],
            [1, 'Apple Gift Card', 'apple', 'TH', 'Instant Digital Key', 'เติมเงิน Apple ID, App Store และ iCloud+', 'apple.png'],
            [2, 'Netflix', 'netflix', 'TH', 'Instant Digital Key', 'แพ็กเกจรับชมภาพยนตร์และซีรีส์ Netflix', 'netflix.png'],
            [3, 'Steam Wallet', 'steam', 'GLOBAL', 'Instant Digital Key', 'เติมเงิน Steam Wallet ซื้อเกม PC ทั่วโลก', 'steam.png'],
        ];

        $platStmt = $pdo->prepare("INSERT INTO platforms (category_id, name, slug, region, delivery_type, description, logo_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($platforms as $plat) {
            $platStmt->execute($plat);
        }

        // 3. Denominations
        $denominations = [
            // Google Play (Platform 1)
            [1, 'Google Play 100 THB', 100.00, 100.00],
            [1, 'Google Play 300 THB', 300.00, 300.00],
            [1, 'Google Play 500 THB', 500.00, 500.00],

            // Apple (Platform 2)
            [2, 'Apple Gift Card 300 THB', 300.00, 300.00],
            [2, 'Apple Gift Card 500 THB', 500.00, 500.00],
            [2, 'Apple Gift Card 1,000 THB', 1000.00, 1000.00],

            // Netflix (Platform 3)
            [3, 'Netflix Mobile 169 THB', 169.00, 169.00],
            [3, 'Netflix Standard 419 THB', 419.00, 419.00],

            // Steam (Platform 4)
            [4, 'Steam Wallet 200 THB', 200.00, 200.00],
            [4, 'Steam Wallet 350 THB', 350.00, 350.00],
            [4, 'Steam Wallet 1,000 THB', 1000.00, 1000.00],
        ];

        $denomStmt = $pdo->prepare("INSERT INTO denominations (platform_id, name, price, face_value) VALUES (?, ?, ?, ?)");
        foreach ($denominations as $denom) {
            $denomStmt->execute($denom);
        }

        // 4. Vouchers (5 sample vouchers for each denomination)
        $voucherStmt = $pdo->prepare("INSERT INTO vouchers (denomination_id, code, serial, status) VALUES (?, ?, ?, 'available')");

        for ($denomId = 1; $denomId <= count($denominations); $denomId++) {
            for ($v = 1; $v <= 5; $v++) {
                $randCode = strtoupper(bin2hex(random_bytes(8))); // 16-char code
                $formattedCode = substr($randCode, 0, 4) . '-' . substr($randCode, 4, 4) . '-' . substr($randCode, 8, 4) . '-' . substr($randCode, 12, 4);
                $serial = 'SN' . str_pad((string)($denomId * 100 + $v), 6, '0', STR_PAD_LEFT);
                $voucherStmt->execute([$denomId, $formattedCode, $serial]);
            }
        }
    }
}
