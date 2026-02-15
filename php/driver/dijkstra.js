function dijkstra(graph, start) {
    let distances = {};
    let prev = {};
    let pq = [];

    for (let node in graph) {
        distances[node] = Infinity;
        prev[node] = null;
    }

    distances[start] = 0;
    pq.push({ node: start, dist: 0 });

    while (pq.length > 0) {
        pq.sort((a, b) => a.dist - b.dist);
        let { node } = pq.shift();

        for (let neighbor in graph[node]) {
            let alt = distances[node] + graph[node][neighbor];

            if (alt < distances[neighbor]) {
                distances[neighbor] = alt;
                prev[neighbor] = node;
                pq.push({ node: neighbor, dist: alt });
            }
        }
    }

    return { distances, prev };
}

function getShortestPath(graph, start, end) {
    let { prev } = dijkstra(graph, start);
    let path = [];
    let current = end;

    while (current !== null) {
        path.unshift(current);
        current = prev[current];
    }

    return path;
}

window.getShortestPath = getShortestPath;
