<<<<<<< HEAD
import React, { useState } from 'react';
=======
import React, { useState, useEffect } from 'react';
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Image,
<<<<<<< HEAD
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
=======
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import api from '../services/api';

import logopaz from '../../assets/images/logopaz.png'; 

export default function PedidoScreen({ navigation }) {
  const [pedidos, setPedidos] = useState([]);
  const [selectedPedido, setSelectedPedido] = useState(null);
  const [pedidoDetalhes, setPedidoDetalhes] = useState(null);
  const [filtroData, setFiltroData] = useState(null);
  const [mostrarCalendario, setMostrarCalendario] = useState(false);
  const [loading, setLoading] = useState(true);
  const [loadingDetalhes, setLoadingDetalhes] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchPedidos();
  }, []);

  // Função para buscar pedidos da API
  const fetchPedidos = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await api.get('/orders');
      
      if (response.data.status && response.data.content) {
        setPedidos(response.data.content);
      } else {
        setError(response.data.message || 'Não foi possível carregar os pedidos');
      }
    } catch (err) {
      console.error('Erro ao buscar pedidos:', err);
      setError(err.message || 'Erro de conexão com o servidor');
    } finally {
      setLoading(false);
    }
  };

  // Função para buscar detalhes de um pedido específico
  const fetchDetalhesPedido = async (pedidoId) => {
    try {
      setLoadingDetalhes(true);
      const response = await api.get(`/orders/with-items/${pedidoId}`);
      
      if (response.data.status && response.data.content) {
        setPedidoDetalhes(response.data.content);
      } else {
        Alert.alert('Erro', response.data.message || 'Não foi possível carregar os detalhes do pedido');
      }
    } catch (err) {
      console.error('Erro ao buscar detalhes do pedido:', err);
      Alert.alert('Erro', err.message || 'Erro de conexão com o servidor');
    } finally {
      setLoadingDetalhes(false);
    }
  };

  // Função para formatar data
  const formatarData = (dataString) => {
    const data = new Date(dataString);
    return data.toLocaleDateString('pt-BR');
  };

  // Função para formatar valor monetário
  const formatarValor = (valor) => {
    return parseFloat(valor).toLocaleString('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    });
  };

  // Função para obter cor do status
  const getStatusColor = (status) => {
    switch (status) {
      case 'completed':
        return '#4CAF50';
      case 'cancelled':
        return '#F44336';
      case 'pending':
        return '#FF9800';
      default:
        return '#757575';
    }
  };

  // Função para traduzir status
  const traduzirStatus = (status) => {
    switch (status) {
      case 'completed':
        return 'Concluído';
      case 'cancelled':
        return 'Cancelado';
      case 'pending':
        return 'Pendente';
      default:
        return status;
    }
  };

  // Função para traduzir método de pagamento
  const traduzirPagamento = (metodo) => {
    switch (metodo) {
      case 'credito':
        return 'Cartão de Crédito';
      case 'debito':
        return 'Cartão de Débito';
      case 'pix':
        return 'PIX';
      case 'dinheiro':
        return 'Dinheiro';
      default:
        return metodo;
    }
  };

  // Filtrar pedidos por data se houver filtro
  const pedidosFiltrados = filtroData
    ? pedidos.filter(p => {
        const dataPedido = new Date(p.created_at).toDateString();
        const dataFiltro = filtroData.toDateString();
        return dataPedido === dataFiltro;
      })
    : pedidos;
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430

  const handleProfilePress = () => {
    navigation.navigate('ProfileScreen');
  };

<<<<<<< HEAD
export default function PedidoScreen() {
  const [selectedPedido, setSelectedPedido] = useState(null);
  const [filtroData, setFiltroData] = useState(null);
  const [mostrarCalendario, setMostrarCalendario] = useState(false);

  const pedidosFiltrados = filtroData
    ? pedidosExemplo.filter(p => p.data === filtroData.toISOString().split('T')[0])
    : pedidosExemplo;
=======
  const handlePedidoPress = (pedido) => {
    setSelectedPedido(pedido);
    fetchDetalhesPedido(pedido.id);
  };

  const fecharDetalhes = () => {
    setSelectedPedido(null);
    setPedidoDetalhes(null);
  };

  const handleRetry = () => {
    fetchPedidos();
  };

  if (loading) {
    return (
      <View style={[styles.container, styles.centered]}>
        <ActivityIndicator size="large" color="#4CAF50" />
        <Text style={styles.loadingText}>Carregando pedidos...</Text>
      </View>
    );
  }

  if (error) {
    return (
      <View style={[styles.container, styles.centered]}>
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity onPress={handleRetry} style={styles.retryButton}>
          <Text style={styles.retryButtonText}>Tentar novamente</Text>
        </TouchableOpacity>
      </View>
    );
  }
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430

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

<<<<<<< HEAD
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
=======
      {/* Botão para limpar filtro */}
      {filtroData && (
        <View style={styles.filtroContainer}>
          <Text style={styles.filtroTexto}>
            Filtrado por: {formatarData(filtroData)}
          </Text>
          <TouchableOpacity onPress={() => setFiltroData(null)} style={styles.limparFiltro}>
            <Text style={styles.limparFiltroTexto}>Limpar filtro</Text>
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
          </TouchableOpacity>
        </View>
      )}

<<<<<<< HEAD
=======
      {/* Lista de pedidos */}
      <ScrollView contentContainerStyle={styles.listaPedidos}>
        {pedidosFiltrados.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="receipt-outline" size={64} color="#ccc" />
            <Text style={styles.emptyText}>Nenhum pedido encontrado</Text>
          </View>
        ) : (
          pedidosFiltrados.map((pedido) => (
            <TouchableOpacity 
              key={pedido.id} 
              style={styles.cardPedido} 
              onPress={() => handlePedidoPress(pedido)}
            >
              <View style={styles.cardContent}>
                <View style={styles.cardHeader}>
                  <Text style={styles.cardTitulo}>
                    Pedido: <Text style={styles.cardCodigo}>{pedido.code}</Text>
                  </Text>
                  <View style={[styles.statusBadge, { backgroundColor: getStatusColor(pedido.status) }]}>
                    <Text style={styles.statusText}>{traduzirStatus(pedido.status)}</Text>
                  </View>
                </View>
                
                <View style={styles.cardInfo}>
                  <Text style={styles.cardData}>
                    Data: {formatarData(pedido.created_at)}
                  </Text>
                  <Text style={styles.cardPagamento}>
                    Pagamento: {traduzirPagamento(pedido.payment_method)}
                  </Text>
                  <Text style={styles.cardTotal}>
                    Total: {formatarValor(pedido.total_amount_order)}
                  </Text>
                </View>
              </View>
              
              <Ionicons name="chevron-forward" size={24} color="#666" />
            </TouchableOpacity>
          ))
        )}
      </ScrollView>

      {/* Modal de detalhes do pedido */}
      {selectedPedido && (
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitulo}>Detalhes do Pedido</Text>
              <TouchableOpacity onPress={fecharDetalhes} style={styles.fecharBtn}>
                <Ionicons name="close" size={28} color="#666" />
              </TouchableOpacity>
            </View>

            {loadingDetalhes ? (
              <View style={styles.modalLoading}>
                <ActivityIndicator size="large" color="#4CAF50" />
                <Text>Carregando detalhes...</Text>
              </View>
            ) : pedidoDetalhes ? (
              <ScrollView style={styles.modalScroll}>
                <View style={styles.detalhesContainer}>
                  <Text style={styles.detalhesTitulo}>Informações do Pedido</Text>
                  
                  <View style={styles.infoRow}>
                    <Text style={styles.infoLabel}>Código:</Text>
                    <Text style={styles.infoValue}>{pedidoDetalhes.code}</Text>
                  </View>
                  
                  <View style={styles.infoRow}>
                    <Text style={styles.infoLabel}>Data:</Text>
                    <Text style={styles.infoValue}>{formatarData(pedidoDetalhes.created_at)}</Text>
                  </View>
                  
                  <View style={styles.infoRow}>
                    <Text style={styles.infoLabel}>Status:</Text>
                    <View style={[styles.statusBadge, { backgroundColor: getStatusColor(pedidoDetalhes.status) }]}>
                      <Text style={styles.statusText}>{traduzirStatus(pedidoDetalhes.status)}</Text>
                    </View>
                  </View>
                  
                  <View style={styles.infoRow}>
                    <Text style={styles.infoLabel}>Pagamento:</Text>
                    <Text style={styles.infoValue}>{traduzirPagamento(pedidoDetalhes.payment_method)}</Text>
                  </View>
                  
                  <View style={styles.infoRow}>
                    <Text style={styles.infoLabel}>Total:</Text>
                    <Text style={[styles.infoValue, styles.totalValue]}>
                      {formatarValor(pedidoDetalhes.total_amount_order)}
                    </Text>
                  </View>

                  {/* Lista de itens */}
                  {pedidoDetalhes.items && pedidoDetalhes.items.content && (
                    <View style={styles.itensContainer}>
                      <Text style={styles.itensTitulo}>Itens do Pedido</Text>
                      
                      {pedidoDetalhes.items.content.map((item) => (
                        <View key={item.id} style={styles.itemCard}>
                          <View style={styles.itemImageContainer}>
                            {item.img_product ? (
                              <Image 
                                source={{ uri: item.img_product }} 
                                style={styles.itemImage}
                                onError={() => console.log('Erro ao carregar imagem do produto')}
                              />
                            ) : (
                              <View style={styles.placeholderContainer}>
                                <Ionicons name="image-outline" size={30} color="#ccc" />
                              </View>
                            )}
                          </View>
                          
                          <View style={styles.itemInfo}>
                            <Text style={styles.itemNome}>{item.product_name}</Text>
                            <Text style={styles.itemDescricao}>{item.product_description}</Text>
                            <Text style={styles.itemCategoria}>Categoria: {item.product_category}</Text>
                            
                            <View style={styles.itemPrecoContainer}>
                              <Text style={styles.itemQuantidade}>Qtd: {item.quantity}</Text>
                              <Text style={styles.itemPreco}>
                                {formatarValor(item.unit_price)} cada
                              </Text>
                            </View>
                            
                            <Text style={styles.itemSubtotal}>
                              Subtotal: {formatarValor(parseFloat(item.unit_price) * item.quantity)}
                            </Text>
                          </View>
                        </View>
                      ))}
                    </View>
                  )}
                </View>
              </ScrollView>
            ) : (
              <View style={styles.modalError}>
                <Text>Erro ao carregar detalhes do pedido</Text>
              </View>
            )}
          </View>
        </View>
      )}
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingTop: 40,
  },
<<<<<<< HEAD
=======
  centered: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 10,
    fontSize: 16,
    color: '#666',
  },
  errorText: {
    color: 'red',
    marginBottom: 20,
    textAlign: 'center',
    fontSize: 16,
  },
  retryButton: {
    backgroundColor: '#333',
    padding: 15,
    borderRadius: 8,
  },
  retryButtonText: {
    color: 'white',
    fontWeight: 'bold',
  },
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
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
<<<<<<< HEAD
=======
  filtroContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 10,
    backgroundColor: '#f0f0f0',
    marginBottom: 10,
  },
  filtroTexto: {
    fontSize: 14,
    color: '#666',
  },
  limparFiltro: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    backgroundColor: '#4CAF50',
    borderRadius: 15,
  },
  limparFiltroTexto: {
    color: 'white',
    fontSize: 12,
    fontWeight: 'bold',
  },
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  listaPedidos: {
    paddingHorizontal: 20,
    paddingBottom: 80,
  },
<<<<<<< HEAD
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


=======
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingTop: 100,
  },
  emptyText: {
    fontSize: 16,
    color: '#666',
    marginTop: 10,
  },
  cardPedido: {
    backgroundColor: '#f8f9fa',
    padding: 16,
    borderRadius: 12,
    marginBottom: 12,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    elevation: 2,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowOffset: { width: 0, height: 2 },
    shadowRadius: 4,
  },
  cardContent: {
    flex: 1,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  cardTitulo: {
    fontSize: 16,
    color: '#333',
  },
  cardCodigo: {
    fontWeight: 'bold',
    color: '#4CAF50',
  },
  statusBadge: {
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    color: 'white',
    fontSize: 12,
    fontWeight: 'bold',
  },
  cardInfo: {
    gap: 4,
  },
  cardData: {
    fontSize: 14,
    color: '#666',
  },
  cardPagamento: {
    fontSize: 14,
    color: '#666',
  },
  cardTotal: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#4CAF50',
  },
  modalOverlay: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalContent: {
    backgroundColor: 'white',
    margin: 20,
    borderRadius: 12,
    maxHeight: '80%',
    width: '90%',
    elevation: 5,
    shadowColor: '#000',
    shadowOpacity: 0.25,
    shadowOffset: { width: 0, height: 2 },
    shadowRadius: 4,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  modalTitulo: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#333',
  },
  fecharBtn: {
    padding: 4,
  },
  modalLoading: {
    padding: 40,
    alignItems: 'center',
    gap: 10,
  },
  modalError: {
    padding: 40,
    alignItems: 'center',
  },
  modalScroll: {
    maxHeight: 400,
  },
  detalhesContainer: {
    padding: 20,
  },
  detalhesTitulo: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 16,
    color: '#333',
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  infoLabel: {
    fontSize: 14,
    color: '#666',
    fontWeight: '500',
  },
  infoValue: {
    fontSize: 14,
    color: '#333',
    fontWeight: 'bold',
  },
  totalValue: {
    fontSize: 16,
    color: '#4CAF50',
  },
  itensContainer: {
    marginTop: 20,
  },
  itensTitulo: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 12,
    color: '#333',
  },
  itemCard: {
    flexDirection: 'row',
    backgroundColor: '#f8f9fa',
    padding: 12,
    borderRadius: 8,
    marginBottom: 12,
  },
  itemImageContainer: {
    width: 60,
    height: 60,
    borderRadius: 8,
    overflow: 'hidden',
    backgroundColor: '#f0f0f0',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
    borderWidth: 1,
    borderColor: '#e0e0e0',
  },
  itemImage: {
    width: '100%',
    height: '100%',
  },
  placeholderContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    width: '100%',
    height: '100%',
  },
  itemInfo: {
    flex: 1,
  },
  itemNome: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 4,
  },
  itemDescricao: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
  },
  itemCategoria: {
    fontSize: 12,
    color: '#888',
    marginBottom: 8,
  },
  itemPrecoContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  itemQuantidade: {
    fontSize: 12,
    color: '#666',
  },
  itemPreco: {
    fontSize: 12,
    color: '#666',
  },
  itemSubtotal: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#4CAF50',
  },
});

>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
