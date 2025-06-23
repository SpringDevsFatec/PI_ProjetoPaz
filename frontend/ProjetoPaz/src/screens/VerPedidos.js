import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  ScrollView,
  TouchableOpacity,
  StyleSheet,
  Image,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import api from '../services/api';

export default function VerPedidos({ navigation }) {
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

  const handleProfilePress = () => {
    if (navigation) {
      navigation.navigate('ProfileScreen');
    }
  };

  const handleHomePress = () => {
    if (navigation) {
      navigation.goBack();
    }
  };

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

  return (
    <View style={styles.container}>
      {/* Cabeçalho */}
      <View style={styles.header}>
        <Image 
          source={require('../../assets/images/logopaz.png')} 
          style={styles.logo} 
          resizeMode="contain" 
        />
        <Text style={styles.titulo}>Pedidos Recentes</Text>
        <View style={styles.iconesHeader}>
          <TouchableOpacity onPress={() => setMostrarCalendario(true)}>
            <Ionicons name="calendar-outline" size={24} color="black" style={{ marginRight: 15 }} />
          </TouchableOpacity>
          <TouchableOpacity onPress={handleProfilePress}>
            <Ionicons name="person-outline" size={24} color="black" />
          </TouchableOpacity>
        </View>
      </View>

      {/* Calendário */}
      {mostrarCalendario && (
        <DateTimePicker
          value={filtroData || new Date()}
          mode="date"
          display="default"
          onChange={(event, selectedDate) => {
            setMostrarCalendario(false);
            if (selectedDate) setFiltroData(selectedDate);
          }}
        />
      )}

      {/* Botão para limpar filtro */}
      {filtroData && (
        <View style={styles.filtroContainer}>
          <Text style={styles.filtroTexto}>
            Filtrado por: {formatarData(filtroData)}
          </Text>
          <TouchableOpacity onPress={() => setFiltroData(null)} style={styles.limparFiltro}>
            <Text style={styles.limparFiltroTexto}>Limpar filtro</Text>
          </TouchableOpacity>
        </View>
      )}

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

      {/* Nota Fiscal / Modal de detalhes */}
      {selectedPedido && (
        <View style={styles.notaFiscal}>
          <View style={styles.notaHeader}>
            <Text style={styles.notaTitulo}>Detalhes do Pedido</Text>
            <TouchableOpacity 
              onPress={fecharDetalhes} 
              style={styles.fecharBtn}
            >
              <Ionicons name="close-circle-outline" size={28} color="black" />
            </TouchableOpacity>
          </View>

          {loadingDetalhes ? (
            <View style={styles.notaLoading}>
              <ActivityIndicator size="large" color="#4CAF50" />
              <Text>Carregando detalhes...</Text>
            </View>
          ) : pedidoDetalhes ? (
            <ScrollView style={styles.notaScroll} showsVerticalScrollIndicator={false}>
              <View style={styles.notaContent}>
                <Text style={styles.notaInfo}>Código: {pedidoDetalhes.code}</Text>
                <Text style={styles.notaInfo}>Data: {formatarData(pedidoDetalhes.created_at)}</Text>
                <Text style={styles.notaInfo}>
                  Status: <Text style={{ color: getStatusColor(pedidoDetalhes.status) }}>
                    {traduzirStatus(pedidoDetalhes.status)}
                  </Text>
                </Text>
                <Text style={styles.notaInfo}>Pagamento: {traduzirPagamento(pedidoDetalhes.payment_method)}</Text>
                
                {/* Lista de itens */}
                {pedidoDetalhes.items && pedidoDetalhes.items.content && (
                  <View style={styles.itensContainer}>
                    <Text style={styles.itensTitulo}>Itens do Pedido:</Text>
                    
                    {pedidoDetalhes.items.content.map((item) => (
                      <View key={item.id} style={styles.itemContainer}>
                        <View style={styles.itemHeader}>
                          <Text style={styles.itemNome}>{item.product_name}</Text>
                          <Text style={styles.itemQuantidade}>Qtd: {item.quantity}</Text>
                        </View>
                        <Text style={styles.itemDescricao}>{item.product_description}</Text>
                        <Text style={styles.itemCategoria}>Categoria: {item.product_category}</Text>
                        <View style={styles.itemPrecoContainer}>
                          <Text style={styles.itemPreco}>
                            {formatarValor(item.unit_price)} cada
                          </Text>
                          <Text style={styles.itemSubtotal}>
                            Subtotal: {formatarValor(parseFloat(item.unit_price) * item.quantity)}
                          </Text>
                        </View>
                      </View>
                    ))}
                  </View>
                )}
                
                <Text style={styles.notaTotal}>
                  Total: {formatarValor(pedidoDetalhes.total_amount_order)}
                </Text>
              </View>
            </ScrollView>
          ) : (
            <View style={styles.notaError}>
              <Text>Erro ao carregar detalhes do pedido</Text>
            </View>
          )}
        </View>
      )}

      {/* Rodapé */}
      <View style={styles.footer}>
        <TouchableOpacity onPress={handleHomePress}>
          <Ionicons name="home-outline" size={28} color="white" />
        </TouchableOpacity>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingTop: 40,
  },
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
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    justifyContent: 'space-between',
    marginBottom: 20,
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
    marginHorizontal: 10,
  },
  iconesHeader: {
    flexDirection: 'row',
    alignItems: 'center',
  },
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
  listaPedidos: {
    paddingHorizontal: 20,
    paddingBottom: 140, // Aumentado para acomodar o modal
  },
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
  notaFiscal: {
    position: 'absolute',
    bottom: 70,
    left: 20,
    right: 20,
    backgroundColor: '#fdf6e3',
    borderRadius: 10,
    borderColor: '#ccc',
    borderWidth: 1,
    elevation: 5,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowOffset: { width: 0, height: 2 },
    zIndex: 10,
    maxHeight: 400,
  },
  notaHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#ddd',
  },
  notaTitulo: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  fecharBtn: {
    padding: 4,
  },
  notaLoading: {
    padding: 40,
    alignItems: 'center',
    gap: 10,
  },
  notaError: {
    padding: 40,
    alignItems: 'center',
  },
  notaScroll: {
    maxHeight: 300,
  },
  notaContent: {
    padding: 20,
  },
  notaInfo: {
    fontSize: 14,
    marginBottom: 8,
    color: '#333',
  },
  notaTotal: {
    fontWeight: 'bold',
    marginTop: 15,
    fontSize: 18,
    color: '#4CAF50',
    textAlign: 'center',
  },
  itensContainer: {
    marginTop: 15,
    marginBottom: 10,
  },
  itensTitulo: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 10,
    color: '#333',
  },
  itemContainer: {
    backgroundColor: '#f8f9fa',
    padding: 12,
    borderRadius: 8,
    marginBottom: 10,
  },
  itemHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 4,
  },
  itemNome: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
    flex: 1,
  },
  itemQuantidade: {
    fontSize: 12,
    color: '#666',
    fontWeight: 'bold',
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
    alignItems: 'center',
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
  footer: {
    position: 'absolute',
    bottom: 0,
    backgroundColor: '#333',
    width: '100%',
    height: 60,
    alignItems: 'center',
    justifyContent: 'center',
  },
});

