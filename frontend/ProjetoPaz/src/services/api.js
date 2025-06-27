import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const api = axios.create({
  baseURL: 'https://0281-2804-18-1006-d3eb-ac13-99b8-1c4e-35bb.ngrok-free.app/',
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