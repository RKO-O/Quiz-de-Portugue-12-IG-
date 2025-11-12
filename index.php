<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Fernando Pessoa</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
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
            flex-direction: column;
        }

        .header {
            background: rgba(0, 0, 0, 0.3);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(10px);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            color: #fff;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 400;
            transition: all 0.3s;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
        }

        .hero {
            text-align: center;
            color: #fff;
            max-width: 800px;
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .quote {
            font-style: italic;
            margin: 2rem 0;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #fff;
            backdrop-filter: blur(5px);
        }

        .cta-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 3rem;
        }

        .btn {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
        }

        .btn-primary {
            background: #fff;
            color: #1e3c72;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #fff;
            border: 2px solid #fff;
        }

        .btn-secondary:hover {
            background: #fff;
            color: #1e3c72;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .feature {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature h3 {
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .cta-buttons {
                flex-direction: column;
            }

            .nav-links {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Quiz Pessoa</div>
        <nav class="nav-links">
            <a href="index.php">Início</a>
            <a href="ranking.php">Ranking</a>
            <?php if (isLoggedIn()): ?>
                <a href="quiz.php">Quiz</a>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
                <a href="registro.php">Registar</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="container">
        <div class="hero">
            <h1>Fernando Pessoa</h1>
            <p>Teste os seus conhecimentos sobre um dos maiores poetas da língua portuguesa</p>
            
            <div class="quote">
                "Não sou nada. Nunca serei nada. Não posso querer ser nada. À parte isso, tenho em mim todos os sonhos do mundo."
            </div>

            <div class="cta-buttons">
                <?php if (isLoggedIn()): ?>
                    <a href="quiz.php" class="btn btn-primary">Começar Quiz</a>
                    <a href="ranking.php" class="btn btn-secondary">Ver Ranking</a>
                <?php else: ?>
                    <a href="registro.php" class="btn btn-primary">Registar Agora</a>
                    <a href="login.php" class="btn btn-secondary">Já tenho conta</a>
                <?php endif; ?>
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">📚</div>
                    <h3>20 Perguntas</h3>
                    <p>Questões variadas sobre vida e obra</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🏆</div>
                    <h3>Ranking</h3>
                    <p>Compare-se com outros utilizadores</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">⭐</div>
                    <h3>Níveis</h3>
                    <p>Fácil, médio e difícil</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>