import React, { createContext, useState, useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import api from '../services/api';

export const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loadingUser, setLoadingUser] = useState(true);

  useEffect(() => {
    const loadUser = async () => {
      try {
        const token = await AsyncStorage.getItem('@token');

        if (token) {
          api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
          const response = await api.get('/user');
          if (response.data.status) {
            setUser(response.data.content);
          } else {
            console.log('Erro ao buscar usuário:', response.data.message);
          }
        }
      } catch (err) {
        console.error('Erro ao carregar dados do usuário:', err);
      } finally {
        setLoadingUser(false);
      }
    };

    loadUser();
  }, []);

  const login = async (token) => {
    try {
      await AsyncStorage.setItem('@token', token);
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      const response = await api.get('/user');
      if (response.data.status) {
        setUser(response.data.content);
      }
    } catch (err) {
      console.error('Erro ao fazer login:', err);
    } finally {
      setLoadingUser(false);
    }
  };

  const logout = async () => {
    await AsyncStorage.removeItem('@token');
    delete api.defaults.headers.common['Authorization'];
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, setUser, loadingUser, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
};
