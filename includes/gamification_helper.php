<?php
// This is includes/gamification_helper.php
// Ensure config.php is included *before* this file.

/**
 * Grants an achievement to a user by its name.
 * Checks if the user already has it. If not, awards it and adds points.
 *
 * @param PDO $pdo The database connection object.
 * @param int $user_id The ID of the user.
 * @param string $achievement_name The 'name' of the achievement (from the 'achievements' table).
 * @return bool True if a new achievement was granted, false otherwise.
 */
function grant_achievement($pdo, $user_id, $achievement_name) {
    try {
        // 1. Find the achievement
        $stmt = $pdo->prepare("SELECT * FROM achievements WHERE name = ?");
        $stmt->execute([$achievement_name]);
        $achievement = $stmt->fetch();

        if (!$achievement) {
            // Achievement doesn't exist in the database
            error_log("Gamification Error: Achievement '$achievement_name' not found.");
            return false;
        }

        $achievement_id = $achievement['achievement_id'];
        $points_to_add = $achievement['points'];

        // 2. Check if user already has this achievement
        $check_stmt = $pdo->prepare("SELECT * FROM user_achievements WHERE user_id = ? AND achievement_id = ?");
        $check_stmt->execute([$user_id, $achievement_id]);
        
        if ($check_stmt->rowCount() > 0) {
            // User already has it
            return false;
        }

        // 3. Grant the achievement (insert into user_achievements)
        $grant_stmt = $pdo->prepare("INSERT INTO user_achievements (user_id, achievement_id) VALUES (?, ?)");
        $grant_stmt->execute([$user_id, $achievement_id]);

        // 4. Add points to the user's total score
        $point_stmt = $pdo->prepare("UPDATE users SET points = points + ? WHERE user_id = ?");
        $point_stmt->execute([$points_to_add, $user_id]);

        // 5. Check for meta-achievements (e.g., 'Engaged' at 50 points)
        check_meta_achievements($pdo, $user_id);

        return true; // Successfully granted

    } catch (PDOException $e) {
        error_log("Gamification DB Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Checks for and grants achievements based on user stats (like total points).
 */
function check_meta_achievements($pdo, $user_id) {
    try {
        // Get user's current points
        $stmt = $pdo->prepare("SELECT points FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if ($user && $user['points'] >= 50) {
            // Grant the 'Engaged' badge (it will only be granted once)
            grant_achievement($pdo, $user_id, 'Engaged');
        }
        
        // ... add more checks here (e.g., for 100 points, 500 points)
        
    } catch (PDOException $e) {
        error_log("Meta Achievement Error: " . $e->getMessage());
    }
}
?>