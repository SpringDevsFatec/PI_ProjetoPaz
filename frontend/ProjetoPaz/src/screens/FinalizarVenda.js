import React, { useState, useEffect } from 'react';
import {
  View,
  TouchableOpacity,
  StyleSheet,
  Image,
  Text,
  ScrollView,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Ionicons, MaterialIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useNavigation } from '@react-navigation/native';
import * as ImagePicker from 'expo-image-picker';
import api from '../services/api';

const FinalizarVendaScreen = ({ route }) => {
  const navigation = useNavigation();
  const { saleId } = route.params || {};

  const [pedidos, setPedidos] = useState([]);
  const [selectedPedido, setSelectedPedido] = useState(null);
  const [pedidoDetalhes, setPedidoDetalhes] = useState(null);
  const [carrinho, setCarrinho] = useState({});
  const [loading, setLoading] = useState(true);
  const [loadingDetalhes, setLoadingDetalhes] = useState(false);
  const [imagem, setImagem] = useState(null);
  const [imagemBase64, setImagemBase64] = useState('');
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchPedidosPorVenda(saleId);
    }, [saleId]);

  useEffect(() => {
      (async () => {
        if (Platform.OS !== 'web') {
          const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
          if (status !== 'granted') {
            Alert.alert('Permissão necessária', 'Precisamos da permissão para acessar suas fotos!');
          }
        }
      })();
    }, []);

    const fetchPedidosPorVenda = async (saleId) => {
    try {
      setLoading(true);
      setError(null);
      const response = await api.get(`/orders/sale-id/${saleId}`);
      
      if (response.data.status && response.data.content) {
        setPedidos(response.data.content);
      } else {
        setError(response.data.message || 'Não foi possível carregar os pedidos da venda');
      }
    } catch (err) {
      console.error('Erro ao buscar pedidos da venda:', err);
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
  
    const pickImage = async () => {
      try {
        let result = await ImagePicker.launchImageLibraryAsync({
          mediaTypes: ["images"],
          allowsEditing: true,
          aspect: [4, 3],
          quality: 0.5,
          base64: true,
        });
  
        if (!result.canceled) {
          setImagem(result.assets[0].uri);
          setImagemBase64(`data:image/jpeg;base64,${result.assets[0].base64}`);
        }
      } catch (error) {
        console.error('Erro ao selecionar imagem:', error);
        Alert.alert('Erro', 'Não foi possível selecionar a imagem');
      }
    };
  
    const takePhoto = async () => {
      try {
        let result = await ImagePicker.launchCameraAsync({
          mediaTypes: ["images"],
          allowsEditing: true,
          aspect: [4, 3],
          quality: 0.5,
          base64: true,
        });
  
        if (!result.canceled) {
          setImagem(result.assets[0].uri);
          setImagemBase64(`data:image/jpeg;base64,${result.assets[0].base64}`);
        }
      } catch (error) {
        console.error('Erro ao tirar foto:', error);
        Alert.alert('Erro', 'Não foi possível tirar a foto');
      }
    };
  
    const editarImagem = () => {
      Alert.alert('Editar Imagem', 'Escolha uma opção:', [
        { text: 'Tirar Foto', onPress: takePhoto },
        { text: 'Escolher da Galeria', onPress: pickImage },
        {
          text: 'Remover Imagem',
          onPress: () => {
            setImagem(null);
            setImagemBase64('');
          },
          style: 'destructive',
        },
        { text: 'Cancelar', style: 'cancel' },
      ], { cancelable: true });
    };

  const handleCancelarVenda = () => {
    Alert.alert(
      'Cancelar Venda',
      'Tem certeza que deseja cancelar esta venda?',
      [
        { text: 'Não', style: 'cancel' },
        {
          text: 'Sim',
          onPress: async () => {
            setLoading(true);
            try {
              const response = await api.put(`/sales/cancelled/${saleId}`);
              if (response.data.status && response.data.content) {
                setCarrinho({});
                navigation.navigate('Products');
              } else {
                setError(response.data.message || 'Não foi possível cancelar a venda');
              }
            } catch (err) {
              console.error('Erro ao cancelar venda:', err);
              setError(err.message || 'Erro de conexão com o servidor');
            } finally {
              setLoading(false);
            }
          },
        },
      ]
    );
  };

  const handleFinalizarVenda = () => {
    Alert.alert(
      'Concluir Venda',
      'Tem certeza que deseja concluir esta venda?',
      [
        { text: 'Não', style: 'cancel' },
        {
          text: 'Sim',
          onPress: async () => {
            setLoading(true);
            try {
              const payload = { image: imagemBase64 };
              const response = await api.put(`/sales/completed/${saleId}`, payload);
              if (response.data.status && response.data.content) {
                setCarrinho({});
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
          },
        },
      ]
    );
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
    fetchPedidosPorVenda();
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
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContainer}>
        <View style={styles.header}>
          <Text style={styles.title}>Finalizar Venda</Text>
        </View>

        <View style={styles.avatarContainer}>
          <Ionicons name="receipt-outline" size={100} color="#aaa" />
        </View>

        {/* Lista de pedidos */}
        {pedidos.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="receipt-outline" size={64} color="#ccc" />
            <Text style={styles.emptyText}>Nenhum pedido encontrado</Text>
          </View>
        ) : (
          pedidos.map((pedido) => (
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

        {/* Imagem */}
        <Text style={styles.label}>Imagem dos Comprovantes</Text>
        <View style={styles.imageUploadContainer}>
          {imagem ? (
            <Image 
              source={{ uri: imagem }} 
              style={{ width: '100%', height: '100%', borderRadius: 8 }} 
              resizeMode="cover"
            />
          ) : (
            <>
              <Ionicons name="cloud-upload-outline" size={40} color="#888" />
              <Text style={styles.uploadText}>Clique para adicionar imagem</Text>
            </>
          )}
          
          {/* Ícone de editar */}
          <TouchableOpacity style={styles.editIcon} onPress={editarImagem}>
            <MaterialIcons name={imagem ? "edit" : "add-a-photo"} size={18} color="#555" />
          </TouchableOpacity>
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
  label: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 5,
  },
  imageUploadContainer: {
  width: '100%',
  height: 140,
  borderWidth: 1,
  borderColor: '#ddd',
  borderRadius: 10,
  justifyContent: 'center',
  alignItems: 'center',
  marginBottom: 20,
  position: 'relative',
  backgroundColor: '#fafafa',
  overflow: 'hidden',
  },
  uploadText: {
    marginTop: 8,
    color: '#888',
    fontSize: 13,
    fontStyle: 'italic',
  },
  editIcon: {
    position: 'absolute',
    bottom: 10,
    right: 10,
    backgroundColor: '#fff',
    borderColor: '#ccc',
    borderWidth: 1,
    padding: 6,
    borderRadius: 20,
    elevation: 2,
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