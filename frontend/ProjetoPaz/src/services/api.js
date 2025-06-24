import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const api = axios.create({
  baseURL: 'https://da3b-2804-14c-d7-8131-6d67-173b-1dbf-93d.ngrok-free.app',
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