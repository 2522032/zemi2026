<?php
declare(strict_types=1);

session_start();
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/connect_db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'ユーザー名とパスワードを入力してください';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = :u");
            $stmt->execute([':u' => $username]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $error = 'ユーザー名またはパスワードが違います';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int)$user['id'];
                header('Location: select.php');
                exit;
            }
        } catch (Throwable $e) {
            $error = 'ログインに失敗しました';
        }
    }
}
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>ログイン</title>
  <link rel="stylesheet" href="styles.css">

</head>
<body class="page-login">
  <header class="site-header">
    <a class="brand" href="home.php">mahojo</a>
  </header>

  <main class="login-shell">
    <div class="side side-left" aria-hidden="true"></div>

    <div class="card login-card">
      <h1 class="login-title">ログイン</h1>


      <form method="post" class="login-form">
        <div class="field">
          <label>ユーザー名</label>
          <input name="username" required>
        </div>

        <div class="field">
          <label>パスワード</label>
          <input type="password" name="password" required>
        </div>

        <button class="btn btn-primary" type="submit">ログイン</button>
      </form>

      <div class="login-links">
        <a href="register.php">新規会員登録はこちら</a>
      </div>
    </div>

    <div class="side side-right" aria-hidden="true"></div>
  </main>
</body>


</html>