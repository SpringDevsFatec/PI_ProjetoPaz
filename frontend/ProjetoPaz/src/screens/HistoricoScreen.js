import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, Image } from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { Feather } from '@expo/vector-icons';
import { Ionicons } from '@expo/vector-icons';

const HistoricoScreen = ({ navigation }) => {
const handleDetalhes = () => {
  navigation.navigate('HistoricoDetalhes', {
  data: "15/06/2023",
  estoqueInicial: 20,
  estoqueFinal: 14,
  totalVendido: 6,
  totalArrecadado: "20$",
  totalLucro: "15$",
  produtosVendidos: {
    "Produto A": 3,
    "Produto B": 2,
    "Produto C": 1
  }
});
};
  const handleProfilePress = () => {
    navigation.navigate('ProfileScreen');
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.gradient}>
      <View style={styles.container}>
        {/* Cabeçalho */}
        <View style={styles.header}>
          <Image source={require('../../assets/images/logopaz.png')}  style={styles.logo} />
          <TouchableOpacity onPress={handleProfilePress}>
            <Ionicons 
              name="person-outline" 
              size={28} 
              color="#333" 
              style={styles.profileIcon}
            />
          </TouchableOpacity>
        </View>

        {/* Título */}
        <Text style={styles.title}>Histórico Geral</Text>

        {/* Card com dados vazios */}
        <View style={styles.card}>
          <Text style={styles.cardText}>Total vendido: </Text>
          <Text style={styles.cardText}>Total venda produto 1: </Text>
          <Text style={styles.cardText}>Total venda produto 2: </Text>
          <Text style={styles.cardText}>Lucro total: </Text>

          <TouchableOpacity style={styles.btn} onPress={handleDetalhes}>
            <Text style={styles.btnText}>Mais detalhes</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* Rodapé fixo */}
      <View style={styles.footer}>
        <Ionicons name="home-outline" size={24} color="white" />
      </View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  gradient: {
    flex: 1,
  },
  container: {
    paddingTop: 50,
    paddingHorizontal: 20,
    paddingBottom: 80,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  logo: {
    width: 70,
    height: 70,
    resizeMode: 'contain',
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
    alignSelf: 'center',
    marginVertical: 20,
  },
  card: {
    borderWidth: 1,
    borderColor: '#aaa',
    borderRadius: 10,
    padding: 15,
    backgroundColor: '#fff',
  },
  cardText: {
    fontSize: 14,
    marginVertical: 3,
  },
  btn: {
    backgroundColor: '#333',
    paddingVertical: 10,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 15,
  },
  btnText: {
    color: '#fff',
    fontWeight: 'bold',
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    width: '100%',
    backgroundColor: '#333',
    paddingVertical: 10,
    alignItems: 'center',
    borderTopLeftRadius: 15,
    borderTopRightRadius: 15,
  },
});

export default HistoricoScreen;

