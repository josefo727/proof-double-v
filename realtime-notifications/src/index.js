const { connectDB } = require('./config/db');

connectDB();

require('./pusherListener');
