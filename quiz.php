<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}




// Processar resposta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar'])) {
    // Verificar se o usuário está logado
    if (!isset($_SESSION['usuario_id'])) {
        die("Erro: usuário não está logado. Faça login antes de realizar o quiz.");
    }

    $respostas = $_POST['respostas'] ?? [];
    $pontos = 0;

    foreach ($respostas as $pergunta_id => $resposta) {
        $stmt = $conn->prepare("SELECT resposta_correta FROM perguntas WHERE id = ?");
        $stmt->execute([$pergunta_id]);
        $pergunta = $stmt->fetch();

        if ($pergunta && $pergunta['resposta_correta'] == $resposta) {
            $pontos++;
        }
    }

    // Verificar se o usuário realmente existe na tabela `usuarios`
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $usuarioExiste = $stmt->fetch();

    if (!$usuarioExiste) {
        die("Erro: o usuário associado à sessão não existe no banco de dados.");
    }

    // Salvar pontuação
    $stmt = $conn->prepare("INSERT INTO pontuacoes (usuario_id, pontos, total_perguntas) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['usuario_id'], $pontos, count($respostas)]);

    $_SESSION['quiz_resultado'] = [
        'pontos' => $pontos,
        'total' => count($respostas)
    ];

    redirect('resultado.php');
}



// Buscar perguntas
$stmt = $conn->query("SELECT * FROM perguntas ORDER BY RAND()");
$perguntas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - Fernando Pessoa</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e8ba3 100%);
            min-height: 100vh;
            padding: 2rem;
        }

        .header {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #fff;
        }

        .user-info {
            color: #fff;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .quiz-header {
            text-align: center;
            color: #fff;
            margin-bottom: 2rem;
        }

        .quiz-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .progress-bar {
            background: rgba(255, 255, 255, 0.2);
            height: 10px;
            border-radius: 10px;
            margin: 1rem 0;
            overflow: hidden;
        }

        .progress-fill {
            background: #fff;
            height: 100%;
            transition: width 0.3s;
        }

        .question-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .question-number {
            color: #1e3c72;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .question-text {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .difficulty-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 1rem;
        }

        .difficulty-facil {
            background: #d4edda;
            color: #155724;
        }

        .difficulty-medio {
            background: #fff3cd;
            color: #856404;
        }

        .difficulty-dificil {
            background: #f8d7da;
            color: #721c24;
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .option {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .option:hover {
            border-color: #1e3c72;
            background: #f8f9fa;
        }

        .option input[type="radio"] {
            margin-right: 1rem;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .option label {
            cursor: pointer;
            flex: 1;
            font-size: 1.1rem;
        }

        .submit-btn {
            width: 100%;
            padding: 1.2rem;
            background: #1e3c72;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 2rem;
        }

        .submit-btn:hover {
            background: #2a5298;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .back-link {
            display: inline-block;
            color: #fff;
            text-decoration: none;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .quiz-header h1 {
                font-size: 2rem;
            }

            .question-card {
                padding: 1.5rem;
            }

            .option label {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Quiz Pessoa</div>
        <div class="user-info">
            Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?> | 
            <a href="logout.php" style="color: #fff;">Sair</a>
        </div>
    </div>

    <div class="container">
        <a href="index.php" class="back-link">← Voltar</a>
        
        <div class="quiz-header">
            <h1>Quiz Fernando Pessoa</h1>
            <p>Teste os seus conhecimentos sobre o poeta e os seus heterónimos</p>
        </div>

        <form method="POST" id="quizForm">
            <?php foreach ($perguntas as $index => $pergunta): ?>   
                <div class="question-card">
                    <div class="question-number">
                        Pergunta <?php echo $index + 1; ?> de 20
                        <span class="difficulty-badge difficulty-<?php echo $pergunta['dificuldade']; ?>">
                            <?php echo ucfirst($pergunta['dificuldade']); ?>
                        </span>
                    </div>
                    
                    <div class="question-text">
                        <?php echo htmlspecialchars($pergunta['pergunta']); ?>
                    </div>

                    <div class="options">
                        <div class="option">
                            <input type="radio" 
                                   id="q<?php echo $pergunta['id']; ?>_a" 
                                   name="respostas[<?php echo $pergunta['id']; ?>]" 
                                   value="a" 
                                   required>
                            <label for="q<?php echo $pergunta['id']; ?>_a">
                                <?php echo htmlspecialchars($pergunta['opcao_a']); ?>
                            </label>
                        </div>

                        <div class="option">
                            <input type="radio" 
                                   id="q<?php echo $pergunta['id']; ?>_b" 
                                   name="respostas[<?php echo $pergunta['id']; ?>]" 
                                   value="b">
                            <label for="q<?php echo $pergunta['id']; ?>_b">
                                <?php echo htmlspecialchars($pergunta['opcao_b']); ?>
                            </label>
                        </div>

                        <?php if (!empty($pergunta['opcao_c'])): ?>
                        <div class="option">
                            <input type="radio" 
                                   id="q<?php echo $pergunta['id']; ?>_c" 
                                   name="respostas[<?php echo $pergunta['id']; ?>]" 
                                   value="c">
                            <label for="q<?php echo $pergunta['id']; ?>_c">
                                <?php echo htmlspecialchars($pergunta['opcao_c']); ?>
                            </label>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($pergunta['opcao_d'])): ?>
                        <div class="option">
                            <input type="radio" 
                                   id="q<?php echo $pergunta['id']; ?>_d" 
                                   name="respostas[<?php echo $pergunta['id']; ?>]" 
                                   value="d">
                            <label for="q<?php echo $pergunta['id']; ?>_d">
                                <?php echo htmlspecialchars($pergunta['opcao_d']); ?>
                            </label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button type="submit" name="finalizar" class="submit-btn">
                🎯 Finalizar Quiz
            </button>
        </form>
    </div>

    <script>
        // Scroll suave para próxima pergunta ao selecionar resposta
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const currentCard = this.closest('.question-card');
                const nextCard = currentCard.nextElementSibling;
                if (nextCard && nextCard.classList.contains('question-card')) {
                    setTimeout(() => {
                        nextCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            });
        });
    </script>
</body>
</html>