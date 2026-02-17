-- Migration: Create cryptocurrencies and crypto_networks tables
-- Purpose: Allow dynamic management of supported cryptocurrencies and their networks
-- Date: 2026-02-17

-- Create cryptocurrencies table
CREATE TABLE IF NOT EXISTS `cryptocurrencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol` (`symbol`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create crypto_networks table
CREATE TABLE IF NOT EXISTS `crypto_networks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crypto_id` int(11) NOT NULL,
  `network_name` varchar(100) NOT NULL,
  `network_type` varchar(50) NOT NULL,
  `chain_id` varchar(50) DEFAULT NULL,
  `explorer_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `crypto_id` (`crypto_id`),
  KEY `is_active` (`is_active`),
  CONSTRAINT `crypto_networks_ibfk_1` FOREIGN KEY (`crypto_id`) REFERENCES `cryptocurrencies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial cryptocurrencies
INSERT INTO `cryptocurrencies` (`symbol`, `name`, `icon`, `description`, `is_active`, `sort_order`) VALUES
('BTC', 'Bitcoin', 'fab fa-bitcoin', 'Bitcoin - The first and most popular cryptocurrency', 1, 1),
('ETH', 'Ethereum', 'fab fa-ethereum', 'Ethereum - Smart contract platform', 1, 2),
('USDT', 'Tether', 'fas fa-dollar-sign', 'Tether - Stablecoin pegged to USD', 1, 3),
('USDC', 'USD Coin', 'fas fa-dollar-sign', 'USD Coin - Stablecoin backed by Circle', 1, 4),
('BNB', 'Binance Coin', 'fas fa-coins', 'Binance Coin - Native token of Binance', 1, 5),
('XRP', 'Ripple', 'fas fa-coins', 'Ripple - Digital payment protocol', 1, 6),
('ADA', 'Cardano', 'fas fa-coins', 'Cardano - Proof-of-stake blockchain', 1, 7),
('SOL', 'Solana', 'fas fa-coins', 'Solana - High-performance blockchain', 1, 8),
('DOT', 'Polkadot', 'fas fa-coins', 'Polkadot - Multi-chain protocol', 1, 9),
('DOGE', 'Dogecoin', 'fas fa-dog', 'Dogecoin - Meme cryptocurrency', 1, 10);

-- Insert networks for each cryptocurrency
-- Bitcoin networks
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'BTC'), 'Bitcoin', 'Bitcoin', 'https://blockchain.com/btc/tx/', 1, 1);

-- Ethereum networks
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `chain_id`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'ETH'), 'Ethereum (ERC-20)', 'ERC-20', '1', 'https://etherscan.io/tx/', 1, 1);

-- USDT networks
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `chain_id`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDT'), 'Ethereum (ERC-20)', 'ERC-20', '1', 'https://etherscan.io/tx/', 1, 1),
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDT'), 'Tron (TRC-20)', 'TRC-20', NULL, 'https://tronscan.org/#/transaction/', 1, 2),
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDT'), 'BSC (BEP-20)', 'BEP-20', '56', 'https://bscscan.com/tx/', 1, 3),
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDT'), 'Polygon', 'Polygon', '137', 'https://polygonscan.com/tx/', 1, 4),
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDT'), 'Solana', 'Solana', NULL, 'https://solscan.io/tx/', 1, 5);

-- USDC networks
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `chain_id`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDC'), 'Ethereum (ERC-20)', 'ERC-20', '1', 'https://etherscan.io/tx/', 1, 1),
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDC'), 'Polygon', 'Polygon', '137', 'https://polygonscan.com/tx/', 1, 2),
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDC'), 'Solana', 'Solana', NULL, 'https://solscan.io/tx/', 1, 3),
((SELECT id FROM cryptocurrencies WHERE symbol = 'USDC'), 'Avalanche C-Chain', 'Avalanche', '43114', 'https://snowtrace.io/tx/', 1, 4);

-- BNB networks
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `chain_id`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'BNB'), 'BSC (BEP-20)', 'BEP-20', '56', 'https://bscscan.com/tx/', 1, 1),
((SELECT id FROM cryptocurrencies WHERE symbol = 'BNB'), 'BNB Beacon Chain (BEP-2)', 'BEP-2', NULL, 'https://explorer.binance.org/tx/', 1, 2);

-- XRP network
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'XRP'), 'XRP Ledger', 'XRP', 'https://xrpscan.com/tx/', 1, 1);

-- ADA network
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'ADA'), 'Cardano', 'Cardano', 'https://cardanoscan.io/transaction/', 1, 1);

-- SOL network
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'SOL'), 'Solana', 'Solana', 'https://solscan.io/tx/', 1, 1);

-- DOT network
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'DOT'), 'Polkadot', 'Polkadot', 'https://polkadot.subscan.io/extrinsic/', 1, 1);

-- DOGE network
INSERT INTO `crypto_networks` (`crypto_id`, `network_name`, `network_type`, `explorer_url`, `is_active`, `sort_order`) VALUES
((SELECT id FROM cryptocurrencies WHERE symbol = 'DOGE'), 'Dogecoin', 'Dogecoin', 'https://dogechain.info/tx/', 1, 1);
