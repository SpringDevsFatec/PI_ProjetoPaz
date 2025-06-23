import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const api = axios.create({
  baseURL: 'https://8f59-2804-14c-d7-8131-65ec-62d3-f47b-8a70.ngrok-free.app',
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