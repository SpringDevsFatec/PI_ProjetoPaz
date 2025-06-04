import React, { useContext, useRef, useEffect, useState } from 'react';
import {
  View,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  SafeAreaView,
  Image,
  Text,
  Animated,
  Alert,
  ActivityIndicator,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import AsyncStorage from '@react-native-async-storage/async-storage';
import api from '../services/api';
import jwt_decode from 'jwt-decode';
import { AuthContext } from '../contexts/AuthContext';

const LoginScreen = ({ navigation }) => {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const scaleAnim = useRef(new Animated.Value(1)).current;
  const { login } = useContext(AuthContext);

  // Verificar se já está logado
  useEffect(() => {
    const checkLogin = async () => {
      const token = await AsyncStorage.getItem('@token');
      if (token) {
        navigation.navigate('Welcome');
      }
    };
    checkLogin();
  }, [navigation]);

  const handlePressIn = () => {
    Animated.spring(scaleAnim, {
      toValue: 0.95,
      useNativeDriver: true,
    }).start();
  };

  const handlePressOut = () => {
    Animated.spring(scaleAnim, {
      toValue: 1,
      friction: 3,
      useNativeDriver: true,
    }).start();
  };

  const handleLogin = async () => {
    if (!email || !password) {
      Alert.alert('Erro', 'Por favor, preencha todos os campos');
      return;
    }

    setLoading(true);

    try {
      const response = await api.post('/login', {
        email: email.toLowerCase().trim(),
        password,
      });

      console.log('Resposta do login:', response.data);

if (response.data.status && response.data.content) {
  const token = response.data.content;

await login(token);

  navigation.navigate('Welcome');
} else {
  Alert.alert('Erro', response.data.message || 'Credenciais inválidas');
}

    } catch (error) {
      console.error('Erro no login:', error.response?.data || error.message);
      Alert.alert(
        'Erro',
        error.response?.data?.message || 'Falha ao realizar login'
      );
    } finally {
      setLoading(false);
    }
  };

  const handleCadastro = () => {
    navigation.navigate('Cadastro');
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <SafeAreaView style={styles.safeArea}>
        <View style={styles.content}>
          <Image source={require('../../assets/images/logopaz.png')} style={styles.logo} />

          <TextInput
            style={styles.input}
            placeholder="Email"
            placeholderTextColor="rgba(0, 0, 0, 0.6)"
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
            autoComplete="email"
          />

          <TextInput
            style={styles.input}
            placeholder="Senha"
            placeholderTextColor="rgba(0, 0, 0, 0.6)"
            secureTextEntry
            value={password}
            onChangeText={setPassword}
            autoComplete="password"
          />

          <Animated.View style={{ transform: [{ scale: scaleAnim }] }}>
            <TouchableOpacity
              style={[styles.botao, loading && styles.botaoDisabled]}
              onPress={handleLogin}
              onPressIn={handlePressIn}
              onPressOut={handlePressOut}
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color="#FFFFFF" />
              ) : (
                <Text style={styles.textoBotao}>Entrar</Text>
              )}
            </TouchableOpacity>
          </Animated.View>

          <TouchableOpacity
            style={styles.botaoCadastro}
            onPress={handleCadastro}
            disabled={loading}
          >
            <Text style={styles.textoCadastro}>Criar uma conta</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1 },
  safeArea: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  content: {
    width: '85%',
    alignItems: 'center',
  },
  logo: {
    width: 120,
    height: 120,
    marginBottom: 30,
  },
  input: {
    width: '100%',
    height: 50,
    backgroundColor: 'rgba(255, 255, 255, 0.8)',
    borderRadius: 8,
    paddingHorizontal: 15,
    marginBottom: 15,
    fontSize: 16,
    color: 'rgba(0, 0, 0, 0.7)',
  },
  botao: {
    backgroundColor: '#000',
    padding: 15,
    borderRadius: 8,
    width: '100%',
    alignItems: 'center',
    marginTop: 10,
  },
  textoBotao: {
    color: 'white',
    fontSize: 18,
    fontWeight: 'bold',
  },
  botaoCadastro: {
    marginTop: 20,
  },
  textoCadastro: {
    color: '#000',
    fontSize: 16,
    textDecorationLine: 'underline',
  },
  botaoDisabled: {
    backgroundColor: '#666',
  },
});

export default LoginScreen;
