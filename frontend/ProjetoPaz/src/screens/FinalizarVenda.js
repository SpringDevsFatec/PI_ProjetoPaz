import React, { useState } from 'react';
import {
  View,
  TouchableOpacity,
  StyleSheet,
  Text,
  ScrollView,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useNavigation } from '@react-navigation/native';
import api from '../services/api';

const FinalizarVendaScreen = ({ route }) => {
  const navigation = useNavigation();
  const { saleId } = route.params || {};

  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);

  const handleCancelarVenda = async () => {
    try {
      setLoading(true);
      const response = await api.put(`/sales/cancelled/${saleId}`);
      if (response.data.status && response.data.content) {
        navigation.goBack();
      } else {
        setError(response.data.message || 'Não foi possível cancelar a venda');
      }
    } catch (err) {
      console.error('Erro ao cancelar venda:', err);
      setError(err.message || 'Erro de conexão com o servidor');
    } finally {
      setLoading(false);
    }
  };

  const handleFinalizarVenda = async () => {
    try {
      setLoading(true);
      const response = await api.put(`/sales/completed/${saleId}`);
      if (response.data.status && response.data.content) {
        navigation.navigate('Vendas');
      } else {
        setError(response.data.message || 'Não foi possível concluir a venda');
      }
    } catch (err) {
      console.error('Erro ao concluir venda:', err);
      setError(err.message || 'Erro de conexão com o servidor');
    } finally {
      setLoading(false);
    }
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContainer}>
        <View style={styles.header}>
          <Text style={styles.title}>Finalizar Venda</Text>
        </View>

        <View style={styles.avatarContainer}>
          <Ionicons name="receipt-outline" size={100} color="#aaa" />
        </View>

        <View style={styles.infoContainer}>
          <Text style={styles.description}>Escolha uma opção para prosseguir com a venda</Text>
        </View>

        <TouchableOpacity onPress={handleFinalizarVenda} style={[styles.button, styles.finalizarButton]}>
          <Text style={styles.buttonText}>Finalizar Venda</Text>
        </TouchableOpacity>

        <TouchableOpacity onPress={handleCancelarVenda} style={[styles.button, styles.cancelarButton]}>
          <Text style={styles.buttonText}>Cancelar Venda</Text>
        </TouchableOpacity>

        <View style={{ height: 80 }} />
      </ScrollView>

      <View style={styles.footer}>
        <Ionicons name="home-outline" size={24} color="white" />
        <Ionicons name="person" size={24} color="white" />
      </View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1 },
  scrollContainer: {
    paddingTop: 50,
    alignItems: 'center',
    paddingBottom: 100,
  },
  header: { 
    width: '90%', 
    marginBottom: 20,
    alignItems: 'center' 
  },
  title: { 
    fontSize: 22, 
    fontWeight: 'bold' 
  },
  avatarContainer: { 
    marginBottom: 30 
  },
  infoContainer: { 
    width: '90%',
    marginBottom: 30,
    alignItems: 'center'
  },
  description: {
    fontSize: 16,
    color: '#555',
    textAlign: 'center'
  },
  button: {
    width: '90%',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 15,
    flexDirection: 'row',
    justifyContent: 'center'
  },
  finalizarButton: {
    backgroundColor: '#28a745',
  },
  cancelarButton: {
    backgroundColor: '#dc3545',
  },
  buttonText: { 
    color: '#fff', 
    fontWeight: 'bold',
    fontSize: 16
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    width: '100%',
    backgroundColor: '#333',
    flexDirection: 'row',
    justifyContent: 'space-around',
    paddingVertical: 15,
  },
});

export default FinalizarVendaScreen;