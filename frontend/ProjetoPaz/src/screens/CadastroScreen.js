import React from 'react';
import { View, TextInput, TouchableOpacity, StyleSheet, SafeAreaView, Image, Text, Animated } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';

const CadastroScreen = ({ navigation }) => { // Adicionei "navigation" para voltar ao login
  const [nome, setNome] = React.useState('');
  const [email, setEmail] = React.useState('');
  const [senha, setSenha] = React.useState('');
  const scaleAnim = React.useRef(new Animated.Value(1)).current;

  // Animação do botão (igual ao login)
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

  const handleCadastro = () => {
    console.log('Dados de cadastro:', { nome, email, senha });
    // Aqui você pode adicionar a lógica para salvar no Firebase/API
  };

  const handleVoltarLogin = () => {
    navigation.navigate('Login'); // Volta para a tela de login
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <SafeAreaView style={styles.safeArea}>
        <View style={styles.content}>
          {/* Logo - Atualize o caminho conforme sua estrutura */}
          <Image 
            source={require('../../assets/images/logopaz.jpeg')}
            style={styles.logo} 
          />
          
          <TextInput
            style={styles.input}
            placeholder="Nome completo"
            placeholderTextColor="rgba(0, 0, 0, 0.6)"
            value={nome}
            onChangeText={setNome}
          />
          
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
          
          {/* Botão de Cadastro */}
          <Animated.View style={{ transform: [{ scale: scaleAnim }] }}>
            <TouchableOpacity
              style={styles.botao}
              onPress={handleCadastro}
              onPressIn={handlePressIn}
              onPressOut={handlePressOut}
            >
              <Text style={styles.textoBotao}>Cadastrar</Text>
            </TouchableOpacity>
          </Animated.View>

          {/* Link para voltar ao login */}
          <TouchableOpacity 
            style={styles.botaoVoltar} 
            onPress={handleVoltarLogin}
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
});

export default CadastroScreen;