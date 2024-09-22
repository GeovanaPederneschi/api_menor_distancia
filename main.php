<?php
header("Content-Type: application/json");

// Definir as posições e arestas do grafo


// Função que implementa o algoritmo de Dijkstra
function dijkstra($graph, $start, $end) {
    $distances = [];
    $previous = [];
    $queue = new SplPriorityQueue();

    foreach ($graph as $node => $edges) {
        $distances[$node] = INF;
        $previous[$node] = null;
    }
    $distances[$start] = 0;
    $queue->insert($start, 0);

    while (!$queue->isEmpty()) {
        $current = $queue->extract();

        if ($current == $end) {
            // Caminho encontrado, construir o caminho
            $path = [];
            while ($previous[$current] !== null) {
                $path[] = $current;
                $current = $previous[$current];
            }
            $path[] = $start;
            return array_reverse($path);
        }

        foreach ($graph[$current] as $neighbor => $weight) {
            $alt = $distances[$current] + $weight;
            if ($alt < $distances[$neighbor]) {
                $distances[$neighbor] = $alt;
                $previous[$neighbor] = $current;
                $queue->insert($neighbor, -$alt);
            }
        }
    }
    return null; // Se não houver caminho
}

// Receber os parâmetros de origem e destino via requisição POST
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['start']) && isset($data['end'])) {
    $start = (int) $data['start'];
    $end = (int) $data['end'];

    if (array_key_exists($start, $nodes) && array_key_exists($end, $nodes)) {
        $path = dijkstra($nodes, $start, $end);
        if ($path !== null) {
            echo json_encode([
                'status' => 'success',
                'path' => $path,
                'message' => 'Caminho mais curto encontrado.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Não há caminho entre os nós especificados.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Os nós de origem ou destino não são válidos.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Parâmetros "start" e "end" são obrigatórios.'
    ]);
}
?>
