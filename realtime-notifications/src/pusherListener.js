const Pusher = require('pusher-js');
const dotenv = require('dotenv');
const { client, connectDB } = require('./config/db');

dotenv.config();

// Conectar a MongoDB
connectDB();

const pusher = new Pusher(process.env.PUSHER_APP_KEY, {
  cluster: process.env.PUSHER_APP_CLUSTER,
});

const channel = pusher.subscribe('orders');

channel.bind_global(function(event_name, data) {
  console.log('Received event: ', event_name, ' with data: ', data);
});

channel.bind('App\\Events\\OrderCreated', async function(data) {
  console.log('Order Created:', data);

  try {
    await client.connect();
    const database = client.db(process.env.MONGODB_DATABASE);
    const ordersCollection = database.collection(process.env.MONGODB_COLLECTION);

    const result = await ordersCollection.insertOne(data);
    console.log(`Order successfully saved with id: ${result.insertedId}`);
  } catch (error) {
    console.error('Error saving order:', error);
  } finally {
    await client.close();
  }
});

channel.bind('App\\Events\\OrderStatusChanged', async function(data) {
  console.log('Order Updated:', data);

  try {
    await client.connect();
    const database = client.db(process.env.MONGODB_DATABASE);
    const ordersCollection = database.collection(process.env.MONGODB_COLLECTION);

    const result = await ordersCollection.insertOne(data);
    console.log(`Updated order notification, id: ${result.insertedId}`);
  } catch (error) {
    console.error('Error saving notification:', error);
  } finally {
    await client.close();
  }
});
