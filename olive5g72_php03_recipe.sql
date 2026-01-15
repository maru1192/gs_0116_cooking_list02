-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: mysql3112.db.sakura.ne.jp
-- 生成日時: 2026 年 1 月 15 日 13:33
-- サーバのバージョン： 8.0.43
-- PHP のバージョン: 8.2.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `olive5g72_php03_recipe`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int NOT NULL,
  `uid` int NOT NULL,
  `recipe_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `favorites`
--

INSERT INTO `favorites` (`favorite_id`, `uid`, `recipe_id`, `created_at`) VALUES
(3, 2, 4, '2026-01-10 10:51:33'),
(4, 2, 3, '2026-01-10 17:22:57'),
(6, 2, 6, '2026-01-11 19:14:50'),
(7, 8, 4, '2026-01-11 20:24:30'),
(9, 10, 6, '2026-01-12 11:19:04');

-- --------------------------------------------------------

--
-- テーブルの構造 `recipe_list`
--

CREATE TABLE `recipe_list` (
  `recipe_id` int NOT NULL,
  `uid` int NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '画像パス',
  `work_time` int NOT NULL COMMENT '実働時間（分）',
  `dish_level` int NOT NULL COMMENT '洗い物レベル（0-3）',
  `materials` text COLLATE utf8mb4_general_ci COMMENT '材料',
  `steps` text COLLATE utf8mb4_general_ci NOT NULL COMMENT '手順（配列）',
  `tools` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '使用道具（配列）',
  `faction` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '派閥名',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `recipe_list`
--

INSERT INTO `recipe_list` (`recipe_id`, `uid`, `title`, `image`, `work_time`, `dish_level`, `materials`, `steps`, `tools`, `faction`, `created_at`) VALUES
(3, 2, 'ハンバーグ', 'uploads/recipes/recipe_69604636557f57.49277653.webp', 10, 0, 'ひき肉\r\nたまねぎ', '[\"テスト\",\"テスト\",\"テスト\"]', '[\"まな板\",\"レンジ\"]', '炊飯器派', '2026-01-09 09:05:10'),
(4, 2, '混ぜ込みおみぎり', 'uploads/recipes/recipe_696047679bfd59.73481411.webp', 10, 1, 'ご飯\r\n鮭フレーク\r\n枝豆', '[\"テスト\",\"テスト\",\"テスト\"]', '[\"ボウル\"]', '炊飯器派', '2026-01-09 09:10:15'),
(6, 2, '肉じゃがver2', 'uploads/recipes/recipe_69644b225c4fe0.83695039.jpeg', 10, 2, 'テスト', '[\"テスト\",\"テスト\",\"テスト\"]', '[\"鍋\",\"ガスコンロ（IHコンロ）\",\"炊飯器\"]', '炊飯器派', '2026-01-09 10:55:34'),
(8, 9, '親子丼（別アカウントテスト）ver3', 'uploads/recipes/recipe_69644c91816da5.14308106.jpg', 10, 2, 'テスト', '[\"テスト\",\"テスト\",\"テスト\"]', '[\"まな板\",\"レンジ\"]', 'ワンパン派', '2026-01-12 10:21:21'),
(9, 9, 'オムライス', 'uploads/recipes/recipe_6964518e033c12.84026208.jpg', 10, 1, 'テスト', '[\"テスト\"]', '[\"包丁\",\"まな板\"]', 'まとめ調理冷凍派', '2026-01-12 10:42:38');

-- --------------------------------------------------------

--
-- テーブルの構造 `user_table`
--

CREATE TABLE `user_table` (
  `id` int NOT NULL,
  `name_sei` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `name_mei` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `lid` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lpw` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `user_table`
--

INSERT INTO `user_table` (`id`, `name_sei`, `name_mei`, `lid`, `lpw`) VALUES
(2, '丸山', '郁美', 'mixedhoneymilk@gmail.com', '$2y$10$4Cm04.E015OSG7c/dlWZoOdou1XbrSr1Zi0bFU1XotFUNx.gwi/UC'),
(3, 'テスト', 'テスト', 'gstest@gmail.com', '$2y$10$XsxXJ2CnYCTpDcwQKA6f3OB/m7ycFCWmG3LgxNcM.lKR0Fh6d9Wei'),
(4, '藤田', '善弘', 'fujita@vicle.jp', '$2y$10$WC0IFJt5P5tXeQ/708v.xeH6S5SjO8Nt5sSbk5Z5KPgKO1xCd3hwu'),
(5, 'テスト', 'テスト', 'aaa@example.com', '$2y$10$dMTrW0wy0FQP5X5G93KjZeQgdEVfOsmjCNxQdHLPH5.hdpMb06TTm'),
(6, 'テスト', 'テスト', 'ppp@example.com', '$2y$10$mdnPPxghRHcRiWQv7rJmE.yZHwye9QZGi95chcf2U4.aVhhrBrGVi'),
(7, 'ooo', 'ooo', 'ooo@example.com', '$2y$10$5YHFYjTAAU4Vhg1Xz9FPr.Oaa/GJuECEKPe6P5E1JsF/4ND2Wrn0i'),
(8, 'testtest', 'testtest', 'testtesttesttest@example.com', '$2y$10$P0lzxbe1Wy0sQ1DcDdHtzOLIo0Gdw8p9DaDxRzBiSdEPXSjN/agy2'),
(9, 'test', 'test', 'test111@example.com', '$2y$10$IoW6/a3oLmhO1ITujv6bzOc/nNbwz8yi25uRrZEaOdEZQqVtgarni'),
(10, 'aaa', 'aaa', 'abshkf@example.com', '$2y$10$Z4yPO/7ZDGtITlgcZdd7i.qjXg1kT256qYZLt.5Yu2M7VjLs5yS5i');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD UNIQUE KEY `unique_favorite` (`uid`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- テーブルのインデックス `recipe_list`
--
ALTER TABLE `recipe_list`
  ADD PRIMARY KEY (`recipe_id`);

--
-- テーブルのインデックス `user_table`
--
ALTER TABLE `user_table`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- テーブルの AUTO_INCREMENT `recipe_list`
--
ALTER TABLE `recipe_list`
  MODIFY `recipe_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- テーブルの AUTO_INCREMENT `user_table`
--
ALTER TABLE `user_table`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipe_list` (`recipe_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
