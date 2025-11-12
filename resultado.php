<?php
require_once 'config.php';

if (!isLoggedIn() || !isset($_SESSION['quiz_resultado'])) {
    redirect('index.php');
}

$resultado = $_SESSION['quiz_resultado'];
$pontos = $resultado['pontos'];
$total = $resultado['total'];
$percentagem = round(($pontos / $total) * 100);

// Limpar resultado da sessão
unset($_SESSION['quiz_resultado']);

// Determinar mensagem baseada na pontuação
if ($percentagem >= 90) {
    $titulo = "Excelente! 🌟";
    $mensagem = "É um verdadeiro especialista em Fernando Pessoa!";
    $cor = "#28a745";
} elseif ($percentagem >= 70) {
    $titulo = "Muito Bem! 👏";
    $mensagem = "Tem um bom conhecimento sobre Fernando Pessoa!";
    $cor = "#17a2b8";
} elseif ($percentagem >= 50) {
    $titulo = "Bom Trabalho! 📚";
    $mensagem = "Está no caminho certo, continue a estudar!";
    $cor = "#ffc107";
} else {
    $titulo = "Continue a Tentar! 💪";
    $mensagem = "A poesia de Pessoa merece ser mais explorada!";
    $cor = "#dc3545";
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Quiz Fernando Pessoa</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        .resultado-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 1s ease;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: #1e3c72;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .mensagem {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .score-circle {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 10px solid <?php echo $cor; ?>;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 2rem auto;
            background: white;
        }

        .score-number {
            font-size: 3.5rem;
            font-weight: 700;
            color: <?php echo $cor; ?>;
        }

        .score-label {
            font-size: 1rem;
            color: #666;
            margin-top: 0.5rem;
        }

        .detalhes {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 2rem 0;
        }

        .detalhes p {
            font-size: 1.1rem;
            margin: 0.5rem 0;
            color: #333;
        }

        .detalhes strong {
            color: #1e3c72;
        }

        .buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #1e3c72;
            color: white;
        }

        .btn-primary:hover {
            background: #2a5298;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .buttons {
                flex-direction: column;
            }

            h1 {
                font-size: 2rem;
            }

            .score-circle {
                width: 150px;
                height: 150px;
            }

            .score-number {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="resultado-icon"><?php echo explode(' ', $titulo)[1]; ?></div>
        
        <h1><?php echo explode(' ', $titulo)[0]; ?></h1>
        <p class="mensagem"><?php echo $mensagem; ?></p>

        <div class="score-circle">
            <div class="score-number"><?php echo $percentagem; ?>%</div>
            <div class="score-label">Pontuação</div>
        </div>

        <div class="detalhes">
            <p><strong>Respostas Corretas:</strong> <?php echo $pontos; ?> de <?php echo $total; ?></p>
            <p><strong>Respostas Erradas:</strong> <?php echo $total - $pontos; ?></p>
        </div>

        <div class="buttons">
            <a href="quiz.php" class="btn btn-primary">🔄 Tentar Novamente</a>
            <a href="ranking.php" class="btn btn-secondary">🏆 Ver Ranking</a>
        </div>

        <div style="margin-top: 2rem;">
            <a href="index.php" style="color: #1e3c72; text-decoration: none; font-weight: 600;">← Voltar ao Início</a>
        </div>
    </div>
</body>
</html>