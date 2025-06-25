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

import logopaz from '../../assets/images/logopaz.png';

export default function VendasScreen({ navigation }) {
  const [vendas, setVendas] = useState([]);
  const [selectedVenda, setSelectedVenda] = useState(null);
  const [vendaDetalhes, setVendaDetalhes] = useState(null);
  const [filtroDataInicio, setFiltroDataInicio] = useState(null);
  const [filtroDataFim, setFiltroDataFim] = useState(null);
  const [mostrarCalendarioInicio, setMostrarCalendarioInicio] = useState(false);
  const [mostrarCalendarioFim, setMostrarCalendarioFim] = useState(false);
  const [loading, setLoading] = useState(true);
  const [loadingDetalhes, setLoadingDetalhes] = useState(false);
  const [error, setError] = useState(null);
  const [filtroStatus, setFiltroStatus] = useState(null);
  const [mostrarFiltros, setMostrarFiltros] = useState(false);

  useEffect(() => {
    fetchVendas();
  }, []);

  // Função para buscar vendas da API
  const fetchVendas = async () => {
    try {
      setLoading(true);
      setError(null);
      let url = '/sales';

      // Aplicar filtros se existirem
      if (filtroStatus) {
        url = `/sales/status/${filtroStatus}`;
      } else if (filtroDataInicio && filtroDataFim) {
        const startDate = filtroDataInicio.toISOString().split('T')[0];
        const endDate = filtroDataFim.toISOString().split('T')[0];
        url = `/sales/date?start_date=${startDate}&end_date=${endDate}`;
      }

      const response = await api.get(url);

      if (response.data.status && response.data.content) {
        setVendas(response.data.content);
      } else {
        setError(response.data.message || 'Não foi possível carregar as vendas');
      }
    } catch (err) {
      console.error('Erro ao buscar vendas:', err);
      setError(err.message || 'Erro de conexão com o servidor');
    } finally {
      setLoading(false);
    }
  };

  // Função para buscar detalhes de uma venda específica
  const fetchDetalhesVenda = async (vendaId) => {
    try {
      setLoadingDetalhes(true);
      const response = await api.get(`/orders/sale-id/${vendaId}`);

      if (response.data.status && response.data.content) {
        setVendaDetalhes(response.data.content);
      } else {
        Alert.alert('Erro', response.data.message || 'Não foi possível carregar os detalhes da venda');
      }
    } catch (err) {
      console.error('Erro ao buscar detalhes da venda:', err);
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
        return 'Concluída';
      case 'cancelled':
        return 'Cancelada';
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

  const handleProfilePress = () => {
    navigation.navigate('ProfileScreen');
  };

  const handleVendaPress = (venda) => {
    setSelectedVenda(venda);
    fetchDetalhesVenda(venda.id);
  };

  const fecharDetalhes = () => {
    setSelectedVenda(null);
    setVendaDetalhes(null);
  };

  const handleRetry = () => {
    fetchVendas();
  };

  const limparFiltros = () => {
    setFiltroDataInicio(null);
    setFiltroDataFim(null);
    setFiltroStatus(null);
    fetchVendas();
  };

  const aplicarFiltroStatus = (status) => {
    setFiltroStatus(status);
    setFiltroDataInicio(null);
    setFiltroDataFim(null);
    setMostrarFiltros(false);
    fetchVendas();
  };

  const aplicarFiltroData = () => {
    if (filtroDataInicio && filtroDataFim) {
      setFiltroStatus(null);
      setMostrarFiltros(false);
      fetchVendas();
    } else {
      Alert.alert('Atenção', 'Selecione ambas as datas (início e fim) para filtrar');
    }
  };

  if (loading) {
    return (
      <View style={[styles.container, styles.centered]}>
        <ActivityIndicator size="large" color="#4CAF50" />
        <Text style={styles.loadingText}>Carregando vendas...</Text>
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
        <Image source={logopaz} style={styles.logo} resizeMode="contain" />
        <Text style={styles.titulo}>Vendas</Text>
        <View style={styles.iconesHeader}>
          <TouchableOpacity onPress={() => setMostrarFiltros(!mostrarFiltros)}>
            <Ionicons name="filter-outline" size={24} color="black" style={{ marginRight: 15 }} />
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

      {/* Filtros */}
      {mostrarFiltros && (
        <View style={styles.filtrosContainer}>
          <Text style={styles.filtrosTitulo}>Filtrar por:</Text>

          <View style={styles.filtrosStatusContainer}>
            <Text style={styles.filtrosSubtitulo}>Status:</Text>
            <View style={styles.filtrosStatusBotoes}>
              <TouchableOpacity
                style={[styles.filtroStatusBtn, filtroStatus === 'pending' && styles.filtroStatusBtnAtivo]}
                onPress={() => aplicarFiltroStatus('pending')}
              >
                <Text style={[styles.filtroStatusBtnTexto, filtroStatus === 'pending' && styles.filtroStatusBtnTextoAtivo]}>Pendentes</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.filtroStatusBtn, filtroStatus === 'completed' && styles.filtroStatusBtnAtivo]}
                onPress={() => aplicarFiltroStatus('completed')}
              >
                <Text style={[styles.filtroStatusBtnTexto, filtroStatus === 'completed' && styles.filtroStatusBtnTextoAtivo]}>Concluídas</Text>
              </TouchableOpacity>
              <TouchableOpacity
                style={[styles.filtroStatusBtn, filtroStatus === 'cancelled' && styles.filtroStatusBtnAtivo]}
                onPress={() => aplicarFiltroStatus('cancelled')}
              >
                <Text style={[styles.filtroStatusBtnTexto, filtroStatus === 'cancelled' && styles.filtroStatusBtnTextoAtivo]}>Canceladas</Text>
              </TouchableOpacity>
            </View>
          </View>

          <View style={styles.filtrosDataContainer}>
            <Text style={styles.filtrosSubtitulo}>Período:</Text>
            <View style={styles.filtrosDataInputs}>
              <TouchableOpacity
                style={styles.filtroDataBtn}
                onPress={() => setMostrarCalendarioInicio(true)}
              >
                <Text style={styles.filtroDataBtnTexto}>
                  {filtroDataInicio ? formatarData(filtroDataInicio) : 'Data inicial'}
                </Text>
              </TouchableOpacity>

              <Text style={styles.filtrosDataSeparador}>à</Text>

              <TouchableOpacity
                style={styles.filtroDataBtn}
                onPress={() => setMostrarCalendarioFim(true)}
              >
                <Text style={styles.filtroDataBtnTexto}>
                  {filtroDataFim ? formatarData(filtroDataFim) : 'Data final'}
                </Text>
              </TouchableOpacity>
            </View>

            <TouchableOpacity
              style={styles.aplicarFiltroDataBtn}
              onPress={aplicarFiltroData}
            >
              <Text style={styles.aplicarFiltroDataBtnTexto}>Aplicar</Text>
            </TouchableOpacity>
          </View>

          <TouchableOpacity
            style={styles.limparFiltrosBtn}
            onPress={limparFiltros}
          >
            <Text style={styles.limparFiltrosBtnTexto}>Limpar filtros</Text>
          </TouchableOpacity>
        </View>
      )}

      {/* Calendário para data inicial */}
      {mostrarCalendarioInicio && (
        <DateTimePicker
          value={filtroDataInicio || new Date()}
          mode="date"
          display="default"
          onChange={(event, selectedDate) => {
            setMostrarCalendarioInicio(false);
            if (selectedDate) setFiltroDataInicio(selectedDate);
          }}
        />
      )}

      {/* Calendário para data final */}
      {mostrarCalendarioFim && (
        <DateTimePicker
          value={filtroDataFim || new Date()}
          mode="date"
          display="default"
          onChange={(event, selectedDate) => {
            setMostrarCalendarioFim(false);
            if (selectedDate) setFiltroDataFim(selectedDate);
          }}
        />
      )}

      {/* Indicador de filtros ativos */}
      {(filtroStatus || (filtroDataInicio && filtroDataFim)) && (
        <View style={styles.filtroAtivoContainer}>
          <Text style={styles.filtroAtivoTexto}>
            Filtro: {filtroStatus ? `Status - ${traduzirStatus(filtroStatus)}` :
              `Período - ${formatarData(filtroDataInicio)} a ${formatarData(filtroDataFim)}`}
          </Text>
          <TouchableOpacity onPress={limparFiltros} style={styles.limparFiltroBtn}>
            <Text style={styles.limparFiltroTexto}>Aplicar</Text>
          </TouchableOpacity>
        </View>
      )}

      {/* Lista de vendas */}
      <ScrollView contentContainerStyle={styles.listaVendas}>
        {vendas.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="receipt-outline" size={64} color="#ccc" />
            <Text style={styles.emptyText}>Nenhuma venda encontrada</Text>
            {filtroStatus || (filtroDataInicio && filtroDataFim) ? (
              <TouchableOpacity onPress={limparFiltros} style={styles.limparFiltrosBtn}>
                <Text style={styles.limparFiltrosBtnTexto}>Limpar filtros</Text>
              </TouchableOpacity>
            ) : null}
          </View>
        ) : (
          vendas.map((venda) => (
            <TouchableOpacity
              key={venda.id}
              style={styles.cardVenda}
              onPress={() => handleVendaPress(venda)}
            >
              <View style={styles.cardContent}>
                <View style={styles.cardHeader}>
                  <Text style={styles.cardTitulo}>
                    Venda: <Text style={styles.cardCodigo}>{venda.code}</Text>
                  </Text>
                  <View style={[styles.statusBadge, { backgroundColor: getStatusColor(venda.status) }]}>
                    <Text style={styles.statusText}>{traduzirStatus(venda.status)}</Text>
                  </View>
                </View>

                <View style={styles.cardInfo}>
                  <Text style={styles.cardData}>
                    Data: {formatarData(venda.created_at)}
                  </Text>
                  <Text style={styles.cardData}>
                    Metodo: {(venda.method)}
                  </Text>
                  <Text style={styles.cardTotal}>
                    Total: {formatarValor(venda.total_amount_sale)}
                  </Text>
                </View>
              </View>

              <Ionicons name="chevron-forward" size={24} color="#666" />
            </TouchableOpacity>
          ))
        )}
      </ScrollView>

      {/* Modal de detalhes da venda */}
      {selectedVenda && (
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitulo}>Detalhes da Venda</Text>
              <TouchableOpacity onPress={fecharDetalhes} style={styles.fecharBtn}>
                <Ionicons name="close" size={28} color="#666" />
              </TouchableOpacity>

            </View>
            <ScrollView style={styles.modalScroll}>
              <View style={styles.detalhesContainer}>
                <Text style={styles.pedidosTitulo}>Pedidos Relacionados ({vendaDetalhes?.length})</Text>
                {loadingDetalhes ? (
                  <View style={styles.modalLoading}>
                    <ActivityIndicator size="large" color="#4CAF50" />
                    <Text>Carregando detalhes...</Text>
                  </View>
                ) : vendaDetalhes ? (
                  vendaDetalhes.map((pedido) => (
                    <View style={styles.pedidosContainer}>
                      <TouchableOpacity
                        key={pedido.id}
                        onPress={() => navigation.navigate('VerPedidos', { saleId: pedido.sale_id })}
                      >
                        <View key={pedido.id} style={styles.pedidoCard}>
                          <View style={styles.pedidoHeader}>
                            <Text style={styles.pedidoCodigo}>Pedido: {pedido.code}</Text>
                            <View style={[styles.statusBadge, { backgroundColor: getStatusColor(pedido.status) }]}>
                              <Text style={styles.statusText}>{traduzirStatus(pedido.status)}</Text>
                            </View>
                          </View>

                          <Text style={styles.pedidoData}>Data: {formatarData(pedido.created_at)}</Text>
                          <Text style={styles.pedidoTotal}>Total: {formatarValor(pedido.total_amount_order)}</Text>
                        </View>
                      </TouchableOpacity>
                    </View>
                  ))
                ) : (
                  <View style={styles.modalError}>
                    <Text>Erro ao carregar pedidos</Text>
                  </View>
                )
                }
              </View>
            </ScrollView>
          </View>
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
  filtrosContainer: {
    padding: 15,
    backgroundColor: '#f8f9fa',
    borderBottomWidth: 1,
    borderBottomColor: '#e0e0e0',
  },
  filtrosTitulo: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 10,
    color: '#333',
  },
  filtrosSubtitulo: {
    fontSize: 14,
    marginBottom: 8,
    color: '#666',
  },
  filtrosStatusContainer: {
    marginBottom: 15,
  },
  filtrosStatusBotoes: {
    flexDirection: 'row',
    justifyContent: 'space-between',
  },
  filtroStatusBtn: {
    flex: 1,
    marginHorizontal: 4,
    paddingVertical: 8,
    backgroundColor: '#f0f0f0',
    borderRadius: 8,
    alignItems: 'center',
  },
  filtroStatusBtnAtivo: {
    backgroundColor: '#4CAF50',
  },
  filtroStatusBtnTexto: {
    color: '#333',
    fontSize: 12,
  },
  filtroStatusBtnTextoAtivo: {
    color: 'white',
  },
  filtrosDataContainer: {
    marginBottom: 15,
  },
  filtrosDataInputs: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 10,
  },
  filtroDataBtn: {
    flex: 1,
    paddingVertical: 10,
    backgroundColor: '#f0f0f0',
    borderRadius: 8,
    alignItems: 'center',
  },
  filtroDataBtnTexto: {
    color: '#333',
    fontSize: 14,
  },
  filtrosDataSeparador: {
    marginHorizontal: 10,
    color: '#666',
  },
  aplicarFiltroDataBtn: {
    backgroundColor: '#4CAF50',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
  },
  aplicarFiltroDataBtnTexto: {
    color: 'white',
    fontWeight: 'bold',
  },
  limparFiltrosBtn: {
    marginTop: 10,
    padding: 10,
    backgroundColor: '#f0f0f0',
    borderRadius: 8,
    alignItems: 'center',
  },
  limparFiltrosBtnTexto: {
    color: '#333',
  },
  filtroAtivoContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 15,
    paddingVertical: 10,
    backgroundColor: '#e8f5e9',
  },
  filtroAtivoTexto: {
    fontSize: 14,
    color: '#2e7d32',
  },
  limparFiltroBtn: {
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
  listaVendas: {
    paddingHorizontal: 15,
    paddingBottom: 80,
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
  cardVenda: {
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
  cardPedidos: {
    fontSize: 14,
    color: '#666',
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
  pedidosContainer: {
    marginTop: 20,
  },
  pedidosTitulo: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 12,
    color: '#333',
  },
  pedidoCard: {
    backgroundColor: '#f8f9fa',
    padding: 12,
    borderRadius: 8,
    marginBottom: 12,
  },
  pedidoHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 8,
  },
  pedidoCodigo: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
  },
  pedidoData: {
    fontSize: 12,
    color: '#666',
    marginBottom: 4,
  },
  pedidoTotal: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#4CAF50',
    marginBottom: 8,
  },
  pedidoItensTitulo: {
    fontSize: 12,
    fontWeight: 'bold',
    color: '#666',
    marginBottom: 4,
  },
  pedidoItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 4,
  },
  pedidoItemNome: {
    fontSize: 12,
    color: '#666',
  },
  pedidoItemPreco: {
    fontSize: 12,
    color: '#666',
  },
});