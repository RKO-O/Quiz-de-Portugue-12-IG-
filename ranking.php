<?php
// config.php - Incluir no início
session_start();
$host = 'localhost';
$dbname = 'quizportugues';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Verificar se o utilizador está logado
function isLoggedIn() {
    return isset($_SESSION['usuario_id']);
}

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Buscar ranking dos melhores jogadores
$stmt = $conn->query("
    SELECT 
        u.id,
        u.nome,
        MAX(p.pontos) as melhor_pontuacao,
        p.total_perguntas,
        ROUND((MAX(p.pontos) / p.total_perguntas) * 100) as percentagem,
        COUNT(p.usuario_id) as num_tentativas,
        MAX(p.data_quiz) as ultima_tentativa
    FROM usuarios u
    LEFT JOIN pontuacoes p ON u.id = p.usuario_id
    WHERE p.pontos IS NOT NULL
    GROUP BY u.id, u.nome, p.total_perguntas
    ORDER BY melhor_pontuacao DESC, percentagem DESC, ultima_tentativa ASC
    LIMIT 20
");
$ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar estatísticas do utilizador atual
$stmt_user = $conn->prepare("
    SELECT 
        MAX(pontos) as melhor_pontuacao,
        total_perguntas,
        ROUND((MAX(pontos) / total_perguntas) * 100) as percentagem,
        COUNT(*) as num_tentativas,
        ROUND(AVG(pontos)) as media_pontos
    FROM pontuacoes
    WHERE usuario_id = ?
    GROUP BY total_perguntas
    ORDER BY melhor_pontuacao DESC
    LIMIT 1
");
$stmt_user->execute([$_SESSION['usuario_id']]);
$minhas_stats = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Calcular posição do utilizador no ranking
$minha_posicao = null;
if ($minhas_stats) {
    $stmt_pos = $conn->prepare("
        SELECT COUNT(DISTINCT u.id) + 1 as posicao
        FROM usuarios u
        JOIN pontuacoes p ON u.id = p.usuario_id
        WHERE (
            SELECT MAX(pontos)
            FROM pontuacoes
            WHERE usuario_id = u.id
        ) > (
            SELECT MAX(pontos)
            FROM pontuacoes
            WHERE usuario_id = ?
        )
    ");
    $stmt_pos->execute([$_SESSION['usuario_id']]);
    $pos_result = $stmt_pos->fetch(PDO::FETCH_ASSOC);
    $minha_posicao = $pos_result['posicao'];
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - Quiz Português</title>
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .nav-links a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .page-header {
            text-align: center;
            color: #fff;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Card de Estatísticas Pessoais */
        .my-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .my-stats h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            text-align: center;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Card do Ranking */
        .ranking-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .ranking-card h2 {
            font-family: 'Playfair Display', serif;
            color: #2a5298;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Tabela do Ranking */
        .ranking-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .ranking-table thead th {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .ranking-table thead th:first-child {
            border-radius: 10px 0 0 10px;
        }

        .ranking-table thead th:last-child {
            border-radius: 0 10px 10px 0;
        }

        .ranking-table tbody tr {
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .ranking-table tbody tr:hover {
            background: #e3f2fd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .ranking-table tbody td {
            padding: 1.2rem 1rem;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }

        .ranking-table tbody td:first-child {
            border-left: 1px solid #e0e0e0;
            border-radius: 10px 0 0 10px;
        }

        .ranking-table tbody td:last-child {
            border-right: 1px solid #e0e0e0;
            border-radius: 0 10px 10px 0;
        }

        /* Posições */
        .position {
            font-weight: bold;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .medal {
            font-size: 1.5rem;
        }

        .gold { color: #FFD700; }
        .silver { color: #C0C0C0; }
        .bronze { color: #CD7F32; }
        .regular { color: #2a5298; }

        /* Linha do Utilizador Atual */
        .my-row {
            background: #fff3cd !important;
            border-left: 4px solid #ffc107 !important;
        }

        .my-row:hover {
            background: #ffe69c !important;
        }

        .you-badge {
            background: #ffc107;
            color: #000;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-left: 0.5rem;
        }

        /* Badge de Percentagem */
        .percentage-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
        }

        .percentage-high { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .percentage-medium { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .percentage-low { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

        /* Estado Vazio */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #999;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #666;
            margin-bottom: 1rem;
        }

        .empty-state p {
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        /* Responsivo */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }

            .ranking-table {
                font-size: 0.9rem;
            }

            .ranking-table thead th,
            .ranking-table tbody td {
                padding: 0.8rem 0.5rem;
            }

            .you-badge {
                display: block;
                margin: 0.5rem 0 0 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                📚 Quiz Português
            </div>
            <nav class="nav-links">
                <a href="index.php">Início</a>
                <a href="quiz.php">Fazer Quiz</a>
                <a href="logout.php">Sair</a>
            </nav>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <h1>🏆 Ranking</h1>
            <p>Os melhores jogadores do Quiz Português</p>
        </div>

        <!-- Minhas Estatísticas -->
        <?php if ($minhas_stats): ?>
        <div class="my-stats">
            <h2>📊 As Tuas Estatísticas</h2>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-value"><?php echo $minha_posicao; ?>º</span>
                    <span class="stat-label">Posição</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo $minhas_stats['melhor_pontuacao']; ?>/<?php echo $minhas_stats['total_perguntas']; ?></span>
                    <span class="stat-label">Melhor Pontuação</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo $minhas_stats['percentagem']; ?>%</span>
                    <span class="stat-label">Percentagem</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?php echo $minhas_stats['num_tentativas']; ?></span>
                    <span class="stat-label">Tentativas</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ranking Card -->
        <div class="ranking-card">
            <h2>Top 20 Jogadores</h2>

            <?php if (count($ranking) > 0): ?>
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Jogador</th>
                        <th>Pontuação</th>
                        <th>Percentagem</th>
                        <th>Tentativas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ranking as $index => $jogador): 
                        $posicao = $index + 1;
                        $is_current_user = ($jogador['id'] == $_SESSION['usuario_id']);
                        
                        $position_class = 'regular';
                        $medal = '';
                        if ($posicao === 1) {
                            $position_class = 'gold';
                            $medal = '🥇';
                        } elseif ($posicao === 2) {
                            $position_class = 'silver';
                            $medal = '🥈';
                        } elseif ($posicao === 3) {
                            $position_class = 'bronze';
                            $medal = '🥉';
                        }
                        
                        $percentage_class = 'percentage-low';
                        if ($jogador['percentagem'] >= 80) {
                            $percentage_class = 'percentage-high';
                        } elseif ($jogador['percentagem'] >= 60) {
                            $percentage_class = 'percentage-medium';
                        }
                    ?>
                    <tr <?php echo $is_current_user ? 'class="my-row"' : ''; ?>>
                        <td>
                            <div class="position <?php echo $position_class; ?>">
                                <?php if ($medal): ?>
                                    <span class="medal"><?php echo $medal; ?></span>
                                <?php else: ?>
                                    <?php echo $posicao; ?>º
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($jogador['nome']); ?></strong>
                            <?php if ($is_current_user): ?>
                                <span class="you-badge">TU</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $jogador['melhor_pontuacao']; ?>/<?php echo $jogador['total_perguntas']; ?></td>
                        <td>
                            <span class="percentage-badge <?php echo $percentage_class; ?>">
                                <?php echo $jogador['percentagem']; ?>%
                            </span>
                        </td>
                        <td><?php echo $jogador['num_tentativas']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <h3>Ainda não há resultados!</h3>
                <p>Seja o primeiro a testar os seus conhecimentos sobre Fernando Pessoa.</p>
                <a href="quiz.php" class="btn">Fazer o Quiz Agora</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>