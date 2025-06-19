import React, { useState } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Image,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';

import logopaz from '../../assets/images/logopaz.png'; 

const pedidosExemplo = [
  {
    id: 'PED1234',
    data: '2025-04-29',
    formaPagamento: 'Cartão de Crédito',
    produtos: ['pão', 'Refrigerante'],
    precoTotal: 34.90,
  },
  {
    id: 'PED5678',
    data: '2025-04-28',
    formaPagamento: 'Dinheiro',
    produtos: ['bolo', 'Suco'],
    precoTotal: 50.00,
  },
  {
    id: 'PED9999',
    data: '2025-04-27',
    formaPagamento: 'Pix',
    produtos: ['Coxinha', 'Café'],
    precoTotal: 18.00,
  },
  {
    id: 'PED8888',
    data: '2025-04-29',
    formaPagamento: 'Cartão de Débito',
    produtos: ['Esfirra', 'Água'],
    precoTotal: 20.00,
  },
];

  const handleProfilePress = () => {
    navigation.navigate('ProfileScreen');
  };

export default function PedidoScreen() {
  const [selectedPedido, setSelectedPedido] = useState(null);
  const [filtroData, setFiltroData] = useState(null);
  const [mostrarCalendario, setMostrarCalendario] = useState(false);

  const pedidosFiltrados = filtroData
    ? pedidosExemplo.filter(p => p.data === filtroData.toISOString().split('T')[0])
    : pedidosExemplo;

  return (
    <View style={styles.container}>
      {/* Cabeçalho */}
      <View style={styles.header}>
        <Image source={logopaz} style={styles.logo} resizeMode="contain" />
        <Text style={styles.titulo}>Pedidos Recentes</Text>
        <View style={styles.iconesHeader}>
          <TouchableOpacity onPress={() => setMostrarCalendario(true)}>
            <Ionicons name="calendar-outline" size={24} color="black" style={{ marginRight: 15 }} />
          </TouchableOpacity>
          <TouchableOpacity onPress={handleProfilePress}>
            <Ionicons 
              name="person-outline" 
              size={28} 
              color="#333" 
              style={styles.profileIcon}
            />
          </TouchableOpacity>
        </View>
      </View>

      {/* Calendário */}
      {mostrarCalendario && (
        <DateTimePicker
          value={new Date()}
          mode="date"
          display="default"
          onChange={(event, selectedDate) => {
            setMostrarCalendario(false);
            if (selectedDate) setFiltroData(selectedDate);
          }}
        />
      )}

      {/* Lista de pedidos */}
      <ScrollView contentContainerStyle={styles.listaPedidos}>
        {pedidosFiltrados.map((pedido) => (
          <TouchableOpacity key={pedido.id} style={styles.cardPedido} onPress={() => setSelectedPedido(pedido)}>
            <Text style={styles.cardTitulo}>Pedido: <Text style={{ fontWeight: 'bold' }}>{pedido.id}</Text></Text>
            <Text>Total: R$ {pedido.precoTotal.toFixed(2)}</Text>
            <Ionicons name="arrow-forward-circle-outline" size={24} color="black" style={styles.seta} />
          </TouchableOpacity>
        ))}
      </ScrollView>

      {/* Nota Fiscal  */}
      {selectedPedido && (
        <View style={styles.notaFiscal}>
          <Text style={styles.notaTitulo}>Nota Fiscal</Text>
          <Text>ID: {selectedPedido.id}</Text>
          <Text>Data: {selectedPedido.data}</Text>
          <Text>Forma de pagamento: {selectedPedido.formaPagamento}</Text>
          <Text>Produtos:</Text>
          {selectedPedido.produtos.map((produto, index) => (
            <Text key={index}> - {produto}</Text>
          ))}
          <Text>Total: R$ {selectedPedido.precoTotal.toFixed(2)}</Text>

          <TouchableOpacity onPress={() => setSelectedPedido(null)} style={styles.fecharBtn}>
            <Ionicons name="close-circle-outline" size={28} color="black" />
          </TouchableOpacity>
        </View>
      )}

    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingTop: 40,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    justifyContent: 'space-between',
    marginBottom: 10,
  },
  logo: {
    width: 48,
    height: 48,
  },
  titulo: {
    fontSize: 18,
    fontWeight: 'bold',
    flex: 1,
    textAlign: 'center',
  },
  iconesHeader: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  listaPedidos: {
    paddingHorizontal: 20,
    paddingBottom: 80,
  },
  cardPedido: {
    backgroundColor: '#f2f2f2',
    padding: 15,
    borderRadius: 12,
    marginBottom: 15,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  cardTitulo: {
    fontSize: 16,
  },
  seta: {
    marginLeft: 10,
  },
  notaFiscal: {
    backgroundColor: '#fdf6e3',
    padding: 20,
    margin: 20,
    borderRadius: 10,
    borderColor: '#ccc',
    borderWidth: 1,
    elevation: 5,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowOffset: { width: 0, height: 2 },
  },
  notaTitulo: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 10,
  },
  fecharBtn: {
    position: 'absolute',
    top: 10,
    right: 10,
  },
});


