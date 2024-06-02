<?php
// Inicia a sessão
session_start();

// Definir usuário e senha fixos
$usuario_fixo = "joao";
$senha_fixa = "12";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verifica se o usuário e a senha correspondem aos valores fixos
    if ($username === $usuario_fixo && $password === $senha_fixa) {
        // Senha correta, define variável de sessão e redireciona para a página dash.php
        $_SESSION['username'] = $username;
        header("Location: dash.php");
        exit();
    } else {
        // Senha incorreta
        $error = "Usuário ou senha incorretos";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela de Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="input-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit">Entrar</button>
            <?php if (isset($error)) { ?>
                <p class="error"><?php echo $error; ?></p>
            <?php } ?>
        </form>
    </div>
</body>
</html>
