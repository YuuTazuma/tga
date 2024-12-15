<?php
// Sample game data - in practice this would likely come from a database
$games = [
    [
        'id' => 3,
        'title' => 'SUPER MARIO PARTY JAMBOREE',
        'developer' => 'Nintendo',
        'status' => 'closed',
        'image' => 'imagens/mario.avif'
    ],
    [
        'id' => 4,
        'title' => 'MARVEL RIVALS',
        'developer' => 'NetEase',
        'status' => 'closed',
        'image' => 'imagens/marvel.png'
    ],
    [
        'id' => 2,
        'title' => 'TEKKEN 8',
        'developer' => 'BANDAI NAMCO',
        'status' => 'closed',
        'image' => 'imagens/tekken.avif'
    ],
    [
        'id' => 4,
        'title' => 'DRAGON BALL SPARKING ZERO',
        'developer' => 'Bandai Namco',
        'status' => 'closed',
        'image' => 'imagens/sparking.jpg'
    ],
    [
        'id' => 2,
        'title' => 'HELLDIVERS 2',
        'developer' => 'Arrowhead',
        'status' => 'closed',
        'image' => 'imagens/hell.avif'
    ],

];

// Conexão com o banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cadastro', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão com o banco: ' . $e->getMessage();
    exit;
}

// Verificar se o voto foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['voto_id'], $_POST['nome'])) {
    $jogoId = $_POST['voto_id'];
    $nomeUsuario = htmlspecialchars(trim($_POST['nome'])); // Nome do usuário
    $gameTitle = '';

    // Busca o título do jogo correspondente ao ID
    foreach ($games as $game) {
        if ($game['id'] == $jogoId) {
            $gameTitle = $game['title'];
            break;
        }
    }

    if (!empty($nomeUsuario) && !empty($gameTitle)) {
        // Insere o voto no banco
        $stmt = $pdo->prepare("INSERT INTO votos_melhormulti (game_title, user_name, voted_at) VALUES (:game_title, :user_name, NOW())");
        $stmt->execute([
            'game_title' => $gameTitle,
            'user_name' => $nomeUsuario
        ]);
        echo 'Voto registrado com sucesso!';
    } else {
        echo 'Erro ao registrar o voto. Verifique os dados fornecidos.';
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game of the Year Awards</title>
    <style>
        /* Estilização básica */
        body {
            background: url('imagens/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            font-family: Arial, sans-serif;
            padding: 20px;
            text-align: center;
        }
        h1 {
            margin-bottom: 30px;
        }

        /* Barra de nome alinhada à esquerda */
        #voto-form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 30px;
        }
        #nome {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            margin-top: 10px;
            border-radius: 15px;
            border: 2px solid #FFB580;
            background: rgba(255, 255, 255, 0.3); /* Transparente */
            color: white;
            font-size: 16px;
            transition: border-color 0.3s, background-color 0.3s;
        }
        #nome:focus {
            background: rgba(255, 255, 255, 0.5);
            border-color: #4CAF50; /* Verde ao focar */
            outline: none;
        }

        /* Ajustando a grid dos jogos para ficarem lado a lado */
        .games-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center; /* Centraliza os jogos */
            gap: 20px;
        }
        .game-card {
            border: 1px solid #fff;
            padding: 15px;
            text-align: center;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 270px; /* Definido tamanho fixo */
            height: 350px; /* Ajuste na altura da caixa */
            overflow: hidden; /* Evita que o conteúdo ultrapasse a caixa */
        }
        .game-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .game-card:hover {
            transform: scale(1.05);
        }

        /* Botões de votar */
        .vote-button {
            background-color: #FFB580;
            border: none;
            color: white;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .vote-button.clicked {
            background-color: #4CAF50; /* Verde */
        }
        .vote-button:hover {
            background-color: #FFA500;
        }

        /* Botão próximo */
        .next-button {
            background-color: #008CBA;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }
        .next-button:hover {
            background-color: #005F73;
        }
    </style>
</head>
<body>
    <h1>Vote no Melhor Jogo Multiplayer</h1>

    <!-- Botão próximo no topo -->
    <button class="next-button" onclick="window.location.href = 'melhornarrativa.php';">Próximo</button>

    <form id="voto-form">
        <label for="nome">Seu Nome:</label>
        <input type="text" id="nome" name="nome" required>
        <div class="games-grid">
            <?php foreach($games as $game): ?>
                <div class="game-card">
                    <img src="<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>">
                    <h3><?php echo htmlspecialchars($game['title']); ?></h3>
                    <p>Desenvolvedor: <?php echo htmlspecialchars($game['developer']); ?></p>
                    <button type="button" class="vote-button" onclick="vote(<?php echo $game['id']; ?>, this)">
                        Votar
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </form>

    <script>
        let voted = false; // Flag para verificar se já foi votado

        function vote(gameId, button) {
            if (voted) {
                alert('Você já votou em um jogo!');
                return;
            }

            const nome = document.getElementById('nome').value;
            if (!nome) {
                alert('Por favor, insira seu nome antes de votar.');
                return;
            }

            // Marca o voto e desabilita os outros botões
            voted = true;
            document.querySelectorAll('.vote-button').forEach(btn => {
                btn.disabled = true;
            });
            button.classList.add('clicked');

            const formData = new FormData();
            formData.append('voto_id', gameId);
            formData.append('nome', nome);

            fetch('melhormulti.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(message => alert(message));
        }
    </script>
</body>
</html>
