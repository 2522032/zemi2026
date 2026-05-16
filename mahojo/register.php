<?php
declare(strict_types=1);

session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


register_shutdown_function(function () {
    $e = error_get_last();
    if ($e) {
        header('Content-Type: text/plain; charset=utf-8');
        echo "FATAL\n{$e['message']}\n{$e['file']} : {$e['line']}\n";
    }
});

require_once __DIR__ . '/connect_db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'ユーザー名とパスワードを入力してください';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO users (username, password_hash)
                VALUES (:u, :p)
                RETURNING id
            ");
            $stmt->execute([':u' => $username, ':p' => $hash]);
            $userId = (int)$stmt->fetchColumn();

            $stmt = $pdo->prepare("
                INSERT INTO user_profiles (user_id, display_name)
                VALUES (:id, :dn)
            ");
            $stmt->execute([':id' => $userId, ':dn' => $username]);

            $pdo->commit();

            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;

            header('Location: home.php');
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $e->getMessage(); 
        }
    }
}
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>新規登録</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="page-register">

  <main class="auth-shell">
    <section class="card auth-card">
      <h1 class="auth-title">新規登録</h1>

      <form method="post">
        <div class="field">
          <label>ユーザー名</label>
          <input name="username" required>
        </div>

        <div class="field">
          <label>パスワード</label>
          <input type="password" name="password" required>
        </div>

        <button class="btn btn-primary" type="submit">登録</button>
      </form>

      <div class="auth-links">
        <a href="login.php">ログインへ</a>
      </div>
    </section>
  </main>

</body>
</html>
