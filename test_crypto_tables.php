<?php
/**
 * Test file to check if cryptocurrency tables exist and have data
 */

require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Cryptocurrency Tables Test</h1>";
echo "<pre>";

// Test 1: Check if cryptocurrencies table exists
echo "\n=== Test 1: Check cryptocurrencies table ===\n";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'cryptocurrencies'");
    $result = $stmt->fetch();
    if ($result) {
        echo "✓ Table 'cryptocurrencies' EXISTS\n";
        
        // Count rows
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM cryptocurrencies");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  Rows: " . $count['count'] . "\n";
        
        // Show sample data
        $stmt = $pdo->query("SELECT * FROM cryptocurrencies LIMIT 5");
        $cryptos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "  Sample data:\n";
        foreach ($cryptos as $crypto) {
            echo "    - {$crypto['symbol']}: {$crypto['name']} (Active: {$crypto['is_active']})\n";
        }
    } else {
        echo "✗ Table 'cryptocurrencies' DOES NOT EXIST\n";
        echo "  Run: mysql < admin/migrations/005_create_crypto_and_network_tables.sql\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking cryptocurrencies table: " . $e->getMessage() . "\n";
}

// Test 2: Check if crypto_networks table exists
echo "\n=== Test 2: Check crypto_networks table ===\n";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'crypto_networks'");
    $result = $stmt->fetch();
    if ($result) {
        echo "✓ Table 'crypto_networks' EXISTS\n";
        
        // Count rows
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM crypto_networks");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  Rows: " . $count['count'] . "\n";
        
        // Show sample data
        $stmt = $pdo->query("SELECT cn.*, c.symbol FROM crypto_networks cn JOIN cryptocurrencies c ON cn.crypto_id = c.id LIMIT 5");
        $networks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "  Sample data:\n";
        foreach ($networks as $network) {
            echo "    - {$network['symbol']}: {$network['network_name']} (Active: {$network['is_active']})\n";
        }
    } else {
        echo "✗ Table 'crypto_networks' DOES NOT EXIST\n";
        echo "  Run: mysql < admin/migrations/005_create_crypto_and_network_tables.sql\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking crypto_networks table: " . $e->getMessage() . "\n";
}

// Test 3: Test AJAX endpoint (user)
echo "\n=== Test 3: Test user AJAX endpoint ===\n";
try {
    // Simulate fetching from the endpoint
    $sql = "SELECT 
                c.id, 
                c.symbol, 
                c.name, 
                c.icon,
                c.description
            FROM cryptocurrencies c
            WHERE c.is_active = 1
            ORDER BY c.sort_order ASC, c.name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $cryptos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Active cryptocurrencies: " . count($cryptos) . "\n";
    
    // Fetch networks for first crypto
    if (count($cryptos) > 0) {
        $firstCrypto = $cryptos[0];
        $networkSql = "SELECT network_name FROM crypto_networks WHERE crypto_id = ? AND is_active = 1";
        $networkStmt = $pdo->prepare($networkSql);
        $networkStmt->execute([$firstCrypto['id']]);
        $networks = $networkStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Sample: {$firstCrypto['symbol']} has " . count($networks) . " networks\n";
        foreach ($networks as $network) {
            echo "  - {$network['network_name']}\n";
        }
    }
} catch (Exception $e) {
    echo "✗ Error testing endpoint: " . $e->getMessage() . "\n";
}

// Test 4: Check database connection
echo "\n=== Test 4: Database connection info ===\n";
echo "Database connection: OK\n";
echo "PDO available: " . (class_exists('PDO') ? 'YES' : 'NO') . "\n";

echo "\n=== All Tests Complete ===\n";
echo "</pre>";
?>
