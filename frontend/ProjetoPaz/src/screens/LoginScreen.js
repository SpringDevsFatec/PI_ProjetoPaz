import React from 'react';
import { View, TextInput, TouchableOpacity, StyleSheet, SafeAreaView, Image, Text, Animated } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

const LoginScreen = ({ navigation }) => { // Adicionei "navigation" para redirecionar
  const [email, setEmail] = React.useState('');
  const [senha, setSenha] = React.useState('');
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

  const handleLogin = () => {
    console.log('Dados de login:', { email, senha });
      // Adicione aqui sua validação de login (ex: Firebase, API)
    navigation.navigate('Welcome'); // Redireciona para a tela de boas-vindas
  };

  const handleCadastro = () => {
    navigation.navigate('Cadastro'); // Redireciona para a tela de cadastro
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <SafeAreaView style={styles.safeArea}>
        <View style={styles.content}>
          {/* Logo */}
          <Image source={require('../../assets/images/logopaz.jpeg')} style={styles.logo} />
          
          <TextInput
            style={styles.input}
            placeholder="Email"
            placeholderTextColor="rgba(0, 0, 0, 0.6)"
            value={email}
            onChangeText={setEmail}
            keyboardType="email-address"
          />
          
          <TextInput
            style={styles.input}
            placeholder="Senha"
            placeholderTextColor="rgba(0, 0, 0, 0.6)"
            secureTextEntry
            value={senha}
            onChangeText={setSenha}
          />
          
          <Animated.View style={{ transform: [{ scale: scaleAnim }] }}>
            <TouchableOpacity
              style={styles.botao}
              onPress={handleLogin}
              onPressIn={handlePressIn}
              onPressOut={handlePressOut}
            >
              <Text style={styles.textoBotao}>Entrar</Text>
            </TouchableOpacity>
          </Animated.View>

          {/* Botão de Cadastro */}
          <TouchableOpacity 
            style={styles.botaoCadastro} 
            onPress={handleCadastro}
          >
            <Text style={styles.textoCadastro}>Criar uma conta</Text>
          </TouchableOpacity>
        </View>
      </SafeAreaView>
    </LinearGradient>
  );
};

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
  botaoCadastro: {
    marginTop: 20,
  },
  textoCadastro: {
    color: '#000',
    fontSize: 16,
    textDecorationLine: 'underline',
  },
});

export default LoginScreen;