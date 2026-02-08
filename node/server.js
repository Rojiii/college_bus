const express = require('express');
const cors = require('cors');
const app = express();
const port = 3000;

// Allow requests from your PHP localhost
app.use(cors());

// Simulated bus coordinates (can be updated dynamically)
let bus1 = { lat: 27.6945, lng: 85.3285 };

// API endpoint to fetch current bus positions
app.get('/api/buses', (req, res) => {
    res.json({ bus1 });
});

// Simulate bus movement
setInterval(() => {
    bus1.lat += 0.0001; // move slightly
    bus1.lng += 0.0001;
}, 2000);

app.listen(port, () => {
    console.log(`Bus server running at http://localhost:${port}`);
});
