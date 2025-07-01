import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const api = axios.create({
<<<<<<< HEAD
  baseURL: 'https://fe4c-2804-14c-d7-8131-54d8-e5cc-5f57-fb4e.ngrok-free.app',
=======
  baseURL: 'https://8043-2804-18-817-8e82-39e2-3dc7-166b-aa80.ngrok-free.app/',
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  headers: {
    'Content-Type': 'application/json',
  },
});

// Interceptor para adicionar o token às requisições
api.interceptors.request.use(async (config) => {
  const token = await AsyncStorage.getItem('@token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
}, (error) => {
  return Promise.reject(error);
});

export default api;