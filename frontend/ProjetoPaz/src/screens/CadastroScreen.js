import React from 'react';
import { View, TextInput, TouchableOpacity, StyleSheet, SafeAreaView, Image, Text, Animated, Alert, ActivityIndicator } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import api from '../services/api'; // Importe a instância do axios que já configuramos

const CadastroScreen = ({ navigation }) => {
  const [nome, setNome] = React.useState('');
  const [email, setEmail] = React.useState('');
  const [senha, setSenha] = React.useState('');
  const [loading, setLoading] = React.useState(false);
  const scaleAnim = React.useRef(new Animated.Value(1)).current;

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

  const handleCadastro = async () => {
  if (!nome || !email || !senha) {
    Alert.alert('Erro', 'Por favor, preencha todos os campos');
    return;
  }

  setLoading(true);
  
  try {
    console.log('Enviando dados para cadastro:', { 
      name: nome, 
      email: email.toLowerCase().trim(), 
      password: senha 
    });
    
    const response = await api.post('/users', {
      name: nome,    // Corrigido para 'name'
      email: email.toLowerCase().trim(), // Normaliza o email
      password: senha // Corrigido para 'password'
    });

    console.log('Resposta do servidor:', response.data);

    if (response.data.status === 'success') {
      Alert.alert('Sucesso', 'Cadastro realizado com sucesso!', [
        { text: 'OK', onPress: () => navigation.navigate('Login') }
      ]);
    } else {
      Alert.alert('Erro', response.data.message || 'Erro ao cadastrar');
    }
  } catch (error) {
    console.error('Erro completo:', error);
    console.error('Resposta do erro:', error.response?.data);
    
    let errorMessage = 'Erro ao conectar com o servidor';
    if (error.response) {
      errorMessage = error.response.data?.message || 
                   `Erro ${error.response.status}: ${JSON.stringify(error.response.data)}`;
    }
    
    Alert.alert('Erro no Cadastro', errorMessage);
  } finally {
    setLoading(false);
  }
};

  const handleVoltarLogin = () => {
    navigation.navigate('Login');
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <SafeAreaView style={styles.safeArea}>
        <View style={styles.content}>
          <Image 
            source={require('../../assets/images/logopaz.png')}
            style={styles.logo} 
          />
          
          <TextInput
            style={styles.input}
            placeholder="Nome completo"
            placeholderTextColor="rgba(0, 0, 0, 0.6)"
            value={nome}
            onChangeText={setNome}
            autoCapitalize="words"
          />
          
          <TextInput
            style={styles.input}
            placeholder="Email"
            placeholderTextColor="rgba(0, 0, 0, 0.6)"
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
            autoCapitalize="none"
          />
          
          <TextInput
            style={styles.input}
            placeholder="Senha"
            placeholderTextColor="rgba(0, 0, 0, 0.6)"
            secureTextEntry
            value={senha}
            onChangeText={setSenha}
            minLength={6}
          />
          
          <Animated.View style={{ transform: [{ scale: scaleAnim }] }}>
            <TouchableOpacity
              style={[styles.botao, loading && styles.botaoDisabled]}
              onPress={handleCadastro}
              onPressIn={handlePressIn}
              onPressOut={handlePressOut}
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color="#FFFFFF" />
              ) : (
                <Text style={styles.textoBotao}>Cadastrar</Text>
              )}
            </TouchableOpacity>
          </Animated.View>

          <TouchableOpacity 
            style={styles.botaoVoltar} 
            onPress={handleVoltarLogin}
            disabled={loading}
          >
            <Text style={styles.textoVoltar}>Já tem uma conta? Faça login</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    </LinearGradient>
  );
};

// Estilos (mantive o mesmo padrão do LoginScreen)
const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
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
  botaoVoltar: {
    marginTop: 20,
  },
  textoVoltar: {
    color: '#000',
    fontSize: 16,
    textDecorationLine: 'underline',
  },
  botaoDisabled: {
    backgroundColor: '#666',
  },
});

export default CadastroScreen;