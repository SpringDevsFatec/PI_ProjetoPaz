import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet, SafeAreaView, Image } from 'react-native';
import { Ionicons, MaterialCommunityIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

const WelcomeScreen = ({ navigation }) => {
  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <SafeAreaView style={styles.safeArea}>
        <View style={styles.content}>
          {/* Logo */}
          <Image source={require('../../assets/images/logopaz.jpeg')} style={styles.logo} />
          
          {/* Caixa escura com texto e botões */}
          <View style={styles.card}>
            <Text style={styles.titulo}>Seja bem-vindo!</Text>
            <Text style={styles.subtitulo}>
              Nosso aplicativo visa facilitar e organizar suas vendas na igreja Santo Agostinho.
            </Text>
            
            {/* Seções do sistema */}
            <View style={styles.opcoesContainer}>
              <TouchableOpacity style={styles.opcao} onPress={() => navigation.navigate('Products')} >
                <Ionicons name="person-circle-outline" size={55} color="white" />
                <Text style={styles.textoOpcao}>Sistema</Text>
              </TouchableOpacity>

              <TouchableOpacity style={styles.opcao}>
                <MaterialCommunityIcons name="cart-outline" size={55} color="white" />
                <Text style={styles.textoOpcao}>Comece a vender</Text>
              </TouchableOpacity>
            </View>
          </View>
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
    width: 180, // Aumentei a logo
    height: 180,
    marginBottom: 25,
  },
  card: {
    backgroundColor: '#333', // Cor do fundo escuro
    padding: 20,
    borderRadius: 10,
    width: '100%',
    alignItems: 'center',
  },
  titulo: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#fff',
    textAlign: 'center',
    marginBottom: 5,
  },
  subtitulo: {
    fontSize: 14,
    color: '#ccc', // Tornando o texto mais visível
    textAlign: 'center',
    marginBottom: 20,
  },
  opcoesContainer: {
    flexDirection: 'row',
    width: '100%',
    justifyContent: 'space-between',
  },
  opcao: {
    alignItems: 'center',
    flex: 1,
  },
  textoOpcao: {
    color: 'white',
    marginTop: 5,
    fontSize: 14,
  },
});

export default WelcomeScreen;
