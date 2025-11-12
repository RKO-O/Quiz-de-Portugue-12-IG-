-- Base de Dados: quizportugues

CREATE DATABASE IF NOT EXISTS quizportugues;
USE quizportugues;

-- Tabela de utilizadores
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de perguntas
CREATE TABLE perguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pergunta TEXT NOT NULL,
    opcao_a VARCHAR(255) NOT NULL,
    opcao_b VARCHAR(255) NOT NULL,
    opcao_c VARCHAR(255) NOT NULL,
    opcao_d VARCHAR(255),
    resposta_correta CHAR(1) NOT NULL,
    dificuldade ENUM('facil', 'medio', 'dificil') DEFAULT 'medio',
    tipo ENUM('multipla', 'verdadeiro_falso') DEFAULT 'multipla'
);

-- Tabela de pontuações
CREATE TABLE pontuacoes (
    usuario_id INT NOT NULL,
    pontos INT NOT NULL,
    total_perguntas INT NOT NULL,
    data_quiz TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Perguntas Verdadeiro/Falso
INSERT INTO perguntas (pergunta, opcao_a, opcao_b, opcao_c, opcao_d, resposta_correta, dificuldade, tipo) VALUES
('Fernando Pessoa criou os heterónimos como forma de expressar diferentes personalidades e estilos literários.', 'Verdadeiro', 'Falso', '', '', 'a', 'facil', 'verdadeiro_falso'),
('Ricardo Reis é conhecido pelo seu estilo futurista e linguagem exaltada.', 'Verdadeiro', 'Falso', '', '', 'b', 'medio', 'verdadeiro_falso'),
('Alberto Caeiro é considerado o mestre dos outros heterónimos.', 'Verdadeiro', 'Falso', '', '', 'a', 'medio', 'verdadeiro_falso'),
('Álvaro de Campos expressa uma visão nostálgica e bucólica da vida no campo.', 'Verdadeiro', 'Falso', '', '', 'b', 'medio', 'verdadeiro_falso'),
('Fernando Pessoa assinava com o seu nome verdadeiro quando escrevia textos mais introspetivos e filosóficos.', 'Verdadeiro', 'Falso', '', '', 'a', 'medio', 'verdadeiro_falso');

-- Perguntas de Escolha Múltipla
INSERT INTO perguntas (pergunta, opcao_a, opcao_b, opcao_c, opcao_d, resposta_correta, dificuldade, tipo) VALUES
('Qual dos heterónimos é considerado o mais ligado à natureza e à simplicidade sensorial?', 'Ricardo Reis', 'Álvaro de Campos', 'Alberto Caeiro', '', 'c', 'medio', 'multipla'),
('Qual é o estilo poético predominante em Ricardo Reis?', 'O futurismo exaltado', 'O paganismo clássico e a contenção emocional', 'O simbolismo místico', '', 'b', 'medio', 'multipla'),
('Álvaro de Campos passou por várias fases estilísticas. Qual delas está ligada ao sensacionismo e ao futurismo?', 'Fase intimista', 'Fase bucólica', 'Fase futurista', '', 'c', 'medio', 'multipla'),
('Qual dos heterónimos era médico e escreveu com uma linguagem culta e clássica?', 'Alberto Caeiro', 'Ricardo Reis', 'Álvaro de Campos', '', 'b', 'medio', 'multipla'),
('Qual heterónimo escreveu o poema "Ode Triunfal"?', 'Fernando Pessoa', 'Alberto Caeiro', 'Álvaro de Campos', '', 'c', 'medio', 'multipla'),
('Fernando Pessoa nasceu em:', 'Coimbra, 1888', 'Lisboa, 1888', 'Lisboa, 1892', '', 'b', 'facil', 'multipla'),
('Qual heterónimo dizia "O poeta é um fingidor"?', 'Fernando Pessoa ortónimo', 'Álvaro de Campos', 'Ricardo Reis', '', 'a', 'medio', 'multipla'),
('Qual das seguintes características pertence a Alberto Caeiro?', 'Uso de metáforas sofisticadas', 'Defesa do racionalismo clássico', 'Rejeição da metafísica e valorização do presente', '', 'c', 'medio', 'multipla'),
('Ricardo Reis segue filosoficamente:', 'O romantismo de Rousseau', 'O niilismo de Nietzsche', 'O estoicismo e epicurismo dos clássicos', '', 'c', 'medio', 'multipla'),
('Qual é o tom dominante na poesia de Álvaro de Campos?', 'Contemplativo e sereno', 'Entusiástico e angustiado', 'Religioso e místico', '', 'b', 'medio', 'multipla'),
('"Nunca conheci quem tivesse levado porrada" é o verso inicial de um poema de:', 'Alberto Caeiro', 'Fernando Pessoa ortónimo', 'Álvaro de Campos', '', 'c', 'medio', 'multipla'),
('Quantos heterónimos principais Fernando Pessoa criou (com biografia e estilo próprios)?', '2', '3', '4', '', 'c', 'dificil', 'multipla'),
('A famosa expressão "Sentir tudo de todas as maneiras" está associada a:', 'Fernando Pessoa ortónimo', 'Ricardo Reis', 'Álvaro de Campos', '', 'c', 'medio', 'multipla'),
('Que heterónimo tem uma visão mais racional e defende a aceitação do destino com serenidade?', 'Álvaro de Campos', 'Fernando Pessoa', 'Ricardo Reis', '', 'c', 'medio', 'multipla'),
('Qual dos heterónimos está mais ligado à modernidade, às máquinas e à velocidade?', 'Alberto Caeiro', 'Álvaro de Campos', 'Fernando Pessoa ortónimo', '', 'b', 'medio', 'multipla');