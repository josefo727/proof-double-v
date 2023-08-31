const { MongoClient, ServerApiVersion } = require('mongodb');
const dotenv = require('dotenv');

dotenv.config();

const uri = process.env.MONGODB_URI;

const client = new MongoClient(uri, {
  serverApi: {
    version: ServerApiVersion.v1,
    strict: true,
    deprecationErrors: true,
  },
});

const connectDB = async () => {
  try {
    await client.connect();
    await client.db("admin").command({ ping: 1 });
    console.log("MongoDB connected");
  } catch (error) {
    console.error('Could not connect to MongoDB', error);
    process.exit(1);
  } finally {
    // Si deseas cerrar la conexión después de confirmar que todo funciona, descomenta la siguiente línea
    // await client.close();
  }
};

module.exports = { client, connectDB };
