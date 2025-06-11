import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';

const api = axios.create({
  baseURL: 'https://fe4c-2804-14c-d7-8131-54d8-e5cc-5f57-fb4e.ngrok-free.app',
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

/**
 * Função para fazer requisições com imagens em base64
 * @param {string} method - Método HTTP (POST, PUT, etc.)
 * @param {string} url - URL da requisição
 * @param {Object} data - Dados da requisição
 * @param {string} imageBase64 - String base64 da imagem (opcional)
 * @param {string} imageType - Tipo da imagem (opcional)
 * @returns {Promise} - Promise da requisição
 */
export const apiWithImage = async (method, url, data = {}, imageBase64 = null, imageType = 'image/jpeg') => {
  try {
    const token = await AsyncStorage.getItem('@token');
    
    const headers = {
      'Content-Type': 'application/json',
    };

    if (token) {
      headers.Authorization = `Bearer ${token}`;
    }

    // Adiciona a imagem em base64 no header se fornecida
    if (imageBase64) {
      headers['X-Image-Data'] = imageBase64;
      headers['X-Image-Type'] = imageType;
    }

    const config = {
      method: method.toLowerCase(),
      url: `${api.defaults.baseURL}${url}`,
      headers,
      data,
    };

    return await axios(config);
  } catch (error) {
    throw error;
  }
};

/**
 * Função específica para cadastrar produto com imagem
 * @param {Object} produtoData - Dados do produto
 * @param {string} imageBase64 - String base64 da imagem (opcional)
 * @param {string} imageType - Tipo da imagem (opcional)
 * @returns {Promise} - Promise da requisição
 */
export const cadastrarProdutoComImagem = async (produtoData, imageBase64 = null, imageType = 'image/jpeg') => {
  return apiWithImage('POST', '/produtos', produtoData, imageBase64, imageType);
};

/**
 * Função específica para atualizar produto com imagem
 * @param {string} produtoId - ID do produto
 * @param {Object} produtoData - Dados do produto
 * @param {string} imageBase64 - String base64 da imagem (opcional)
 * @param {string} imageType - Tipo da imagem (opcional)
 * @returns {Promise} - Promise da requisição
 */
export const atualizarProdutoComImagem = async (produtoId, produtoData, imageBase64 = null, imageType = 'image/jpeg') => {
  return apiWithImage('PUT', `/produtos/${produtoId}`, produtoData, imageBase64, imageType);
};

export default api;