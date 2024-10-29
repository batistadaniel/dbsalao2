<?php 
session_start(); 
require 'conexao.php'; 

// Verifica se o método da requisição é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") { 
    $acao = $_POST['acao'];

    // Lógica de cadastro
    if ($acao === 'cadastro') {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING); 
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); 
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING); 

        if (empty($nome) || empty($email) || empty($senha)) { 
            $_SESSION['msg'] = "<p class='msg'>Por favor, preencha tudo.</p>"; 
            header("Location: index.php"); 
            exit(); 
        } 

        // Verifica se o email já está cadastrado
        $query_verifica_email = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = mysqli_prepare($conexao, $query_verifica_email);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) { 
            $_SESSION['msg'] = "<p class='msg'>Email já cadastrado.</p>"; 
            header("Location: index.php"); 
            exit(); 
        }

        // Insere o novo usuário
        $create_user = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conexao, $create_user);
        mysqli_stmt_bind_param($stmt, 'sss', $nome, $email, $senha);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['msg'] = "<p class='msg' style='color:green'>Usuário cadastrado com sucesso.</p>"; 
            header("Location: index.php"); 
        } else {
            $_SESSION['msg'] = "<p class='msg'>Erro ao cadastrar usuário.</p>"; 
            header("Location: index.php"); 
        }
    }

    // Lógica de login
    elseif ($acao === 'login') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL); 
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

        $verifica = "SELECT * FROM usuarios WHERE email = ? AND senha = ?";
        $stmt = mysqli_prepare($conexao, $verifica);
        mysqli_stmt_bind_param($stmt, 'ss', $email, $senha);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($resultado) > 0) { 
            $_SESSION['usuario'] = $email; 
            header("Location: home.php"); 
            exit(); 
        } else { 
            $_SESSION['msg'] = "<p class='msg'>E-mail ou senha inválidos!</p>"; 
            header("Location: index.php"); 
            exit(); 
        }
    }
} else { 
    header("Location: index.php"); 
    exit(); 
}
