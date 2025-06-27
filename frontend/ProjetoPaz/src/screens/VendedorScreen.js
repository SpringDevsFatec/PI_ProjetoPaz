import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Image,
  ScrollView,
  Modal,
  Alert,
  FlatList,
  Platform
} from 'react-native';
import { Feather, Ionicons, MaterialIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import * as ImagePicker from 'expo-image-picker';
import api from '../services/api';

const VendedorScreen = ({ route, navigation }) => {
  const { selectedProducts = [], saleId } = route.params || {};

  const [products, setProducts] = useState(selectedProducts);
  const [selectedItems, setSelectedItems] = useState([]);
  const [carrinho, setCarrinho] = useState({});
  const [carrinhoVisivel, setCarrinhoVisivel] = useState(false);
  const [carrinhoMinimizado, setCarrinhoMinimizado] = useState(false);
  const [formaPagamento, setFormaPagamento] = useState(null);
  const [termoPesquisa, setTermoPesquisa] = useState('');
  const [loading, setLoading] = useState(false);
  const [currentSaleOrders, setCurrentSaleOrders] = useState([]);
  const [imagem, setImagem] = useState(null);
  const [imagemBase64, setImagemBase64] = useState('');
  const [error, setError] = useState(null);

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

  const pickImage = async () => {
    try {
      let result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.All,
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
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
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

  const handleAdicionarItem = (nome) => {
    setCarrinho((prev) => ({
      ...prev,
      [nome]: (prev[nome] || 0) + 1,
    }));
    setCarrinhoVisivel(true);
    setCarrinhoMinimizado(false);
  };

  const handleRemoverItem = (nome) => {
    setCarrinho((prev) => {
      const novo = { ...prev };
      if (novo[nome] > 1) novo[nome]--;
      else delete novo[nome];
      return novo;
    });
  };

  const calcularTotal = () => {
    return Object.entries(carrinho).reduce((total, [item, quantidade]) => {
      const product = products.find(p => p.name === item);
      const precoItem = product?.preco || 0;
      return total + precoItem * quantidade;
    }, 0).toFixed(2);
  };

  const handleFinalizarCompra = async () => {
    if (!formaPagamento) {
      Alert.alert('Atenção', 'Selecione uma forma de pagamento');
      return;
    }

    if (!Object.keys(carrinho).length) {
      Alert.alert('Erro', 'O carrinho está vazio.');
      return;
    }

    try {
      setLoading(true);
      const orderItemsPayload = Object.entries(carrinho).map(([name, quantity]) => {
        const product = products.find(p => p.name === name);
        return {
          product_id: product.id,
          quantity,
          unit_price: parseFloat(product.preco),
        };
      });

      const orderPayload = {
        payment_method: formaPagamento,
        itens: orderItemsPayload,
      };

      const response = await api.post(`/orders/${saleId}`, orderPayload);
      const newOrder = response.data;

      setCurrentSaleOrders(prev => [...prev, newOrder]);
      Alert.alert(
        `Pedido #${newOrder.content.code} Finalizado`,
        `Total: R$ ${calcularTotal()}\nForma de pagamento: ${formaPagamento}`,
        [
          {
            text: 'OK',
            onPress: () => {
              setCarrinho({});
              setCarrinhoVisivel(false);
              //navigation.goBack();
            }
          }
        ]
      );
    } catch (error) {
      Alert.alert('Erro', 'Não foi possível finalizar o pedido. Tente novamente.');
      console.error('Erro ao finalizar pedido:', error);
    } finally {
      setLoading(false);
    }
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

  // Alterna seleção do produto
  const toggleProductSelection = (productId) => {
    setSelectedItems(prev => {
      if (prev.includes(productId)) {
        return prev.filter(id => id !== productId);
      } else {
        return [...prev, productId];
      }
    });
  };

  // Renderiza cada item da lista de produtos
  const renderProductItem = ({ item }) => (
    <TouchableOpacity
      style={[
        styles.produtoCard,
        selectedItems.includes(item.id) && styles.selectedProduct
      ]}
      onPress={() => toggleProductSelection(item.id)}
    >
      {item.img_product ? (
        <Image
          source={{ uri: item.img_product }}
          style={styles.productImage}
          resizeMode="contain"
        />
      ) : (
        <Text style={styles.noImageText}>Sem imagem</Text>
      )}

      <Text style={styles.produtoNome}>{item.name}</Text>
      <Text style={styles.precoTexto}>R$ {item.preco.toFixed(2)}</Text>
      <Text style={styles.produtoCategoria}>{item.categoria}</Text>
      
      {selectedItems.includes(item.id) && (
        <Ionicons 
          name="checkmark-circle" 
          size={24} 
          color="#4CAF50" 
          style={styles.checkIcon} 
        />
      )}
    </TouchableOpacity>
  );

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <ScrollView contentContainerStyle={styles.content}>
        <View style={styles.header}>
          <TouchableOpacity onPress={() => navigation.goBack()}>
            <Feather name="arrow-left" size={24} color="black" />
          </TouchableOpacity>
          <Text style={styles.title}>Venda</Text>
          <TouchableOpacity onPress={() => navigation.navigate('ProfileScreen')}>
            <Feather name="user" size={24} color="black" />
          </TouchableOpacity>
        </View>

        <View style={styles.searchContainer}>
          <Feather name="search" size={18} color="#999" />
          <TextInput
            style={styles.searchInput}
            placeholder="Buscar produto..."
            placeholderTextColor="#aaa"
            value={termoPesquisa}
            onChangeText={setTermoPesquisa}
          />
        </View>

        <Text style={styles.sectionTitle}>Produtos Selecionados</Text>
        
        <FlatList
          data={products}
          renderItem={renderProductItem}
          keyExtractor={item => item.id.toString()}
          numColumns={2}
          scrollEnabled={false}
          contentContainerStyle={styles.produtoGrid}
        />

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

        <View style={styles.botaoAddCarrinhoContainer}>
          <TouchableOpacity 
            style={[styles.botaoAcao, styles.botaoAdicionar]}
            onPress={() => {
              // Adiciona apenas os produtos selecionados ao carrinho
              const newCart = {...carrinho};
              products
                .filter(product => selectedItems.includes(product.id))
                .forEach(product => {
                  newCart[product.name] = (newCart[product.name] || 0) + 1;
                });
              setCarrinho(newCart);
              setCarrinhoVisivel(true);
            }}
            disabled={selectedItems.length === 0}
          >
            <Text style={styles.textoBotaoAcao}>Adicionar ao Carrinho</Text>
          </TouchableOpacity>
        </View>
        <View style={styles.botoesVendaContainer}>
          <TouchableOpacity 
            style={[styles.botaoAcao, styles.botaoFinalizar]}
            onPress={handleFinalizarVenda}
          >
            <Text style={styles.textoBotaoAcao}>Finalizar Venda</Text>
          </TouchableOpacity>
          <TouchableOpacity 
            style={[styles.botaoAcao, styles.botaoCancelar]}
            onPress={handleCancelarVenda}
          >
            <Text style={styles.textoBotaoAcao}>Cancelar Venda</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>

      {/* Carrinho flutuante */}
      {carrinhoVisivel && !carrinhoMinimizado && (
        <View style={styles.carrinhoFlutuante}>
          <Text style={styles.carrinhoTitulo}>Carrinho</Text>
          
          {Object.entries(carrinho).map(([nome, quantidade]) => {
            const product = selectedProducts.find(p => p.name === nome);
            const precoItem = product?.preco || 0;
            const totalItem = (precoItem * quantidade).toFixed(2);
            
            return (
              <View key={nome} style={styles.itemCarrinho}>
                <View style={styles.itemInfo}>
                  <Text style={styles.itemNome}>{nome}</Text>
                  <Text style={styles.itemPreco}>R$ {precoItem.toFixed(2)}</Text>
                </View>
                <View style={styles.controlesItem}>
                  <TouchableOpacity onPress={() => handleRemoverItem(nome)}>
                    <Text style={styles.botaoQuantidade}>-</Text>
                  </TouchableOpacity>
                  <Text style={styles.quantidade}>{quantidade}</Text>
                  <TouchableOpacity onPress={() => handleAdicionarItem(nome)}>
                    <Text style={styles.botaoQuantidade}>+</Text>
                  </TouchableOpacity>
                  <Text style={styles.totalItem}>R$ {totalItem}</Text>
                </View>
              </View>
            );
          })}

          <View style={styles.divider} />

          <Text style={styles.total}>Total: R$ {calcularTotal()}</Text>

          {/* Forma de pagamento */}
          <View style={styles.paymentContainer}>
            <Text style={styles.paymentTitle}>Forma de pagamento:</Text>
            
            <TouchableOpacity 
              style={[
                styles.paymentOption,
                formaPagamento === 'Cartão' && styles.selectedPayment
              ]} 
              onPress={() => setFormaPagamento('Cartão')}
            >
              <Feather name="credit-card" size={20} color="#333" />
              <Text style={styles.paymentText}>Cartão</Text>
              {formaPagamento === 'Cartão' && (
                <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
              )}
            </TouchableOpacity>
            
            <TouchableOpacity 
              style={[
                styles.paymentOption,
                formaPagamento === 'Pix' && styles.selectedPayment
              ]} 
              onPress={() => setFormaPagamento('Pix')}
            >
              <Feather name="dollar-sign" size={20} color="#333" />
              <Text style={styles.paymentText}>Pix</Text>
              {formaPagamento === 'Pix' && (
                <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
              )}
            </TouchableOpacity>
            
            <TouchableOpacity 
              style={[
                styles.paymentOption,
                formaPagamento === 'Dinheiro' && styles.selectedPayment
              ]} 
              onPress={() => setFormaPagamento('Dinheiro')}
            >
              <Feather name="money" size={20} color="#333" />
              <Text style={styles.paymentText}>Dinheiro</Text>
              {formaPagamento === 'Dinheiro' && (
                <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
              )}
            </TouchableOpacity>
          </View>

          <TouchableOpacity 
            style={styles.finalizarButton}
            onPress={handleFinalizarCompra}
            disabled={Object.keys(carrinho).length === 0}
          >
            <Text style={styles.finalizarButtonText}>Finalizar Pedido</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={styles.minimizarBotao} 
            onPress={() => setCarrinhoMinimizado(true)}
          >
            <Feather name="chevron-down" size={20} color="#333" />
          </TouchableOpacity>
        </View>
      )}

      {carrinhoMinimizado && (
        <TouchableOpacity 
          style={styles.botaoAbrirCarrinho} 
          onPress={() => setCarrinhoMinimizado(false)}
        >
          <Feather name="shopping-cart" size={24} color="#fff" />
          {Object.keys(carrinho).length > 0 && (
            <View style={styles.contadorCarrinho}>
              <Text style={styles.contadorTexto}>{Object.keys(carrinho).length}</Text>
            </View>
          )}
        </TouchableOpacity>
      )}
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: { 
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  content: { 
    padding: 20,
    paddingBottom: 100,
  },
  header: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center',
    marginBottom: 20,
  },
  title: { 
    fontSize: 24, 
    fontWeight: 'bold', 
    color: '#333',
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginBottom: 15,
    color: '#333',
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#fff',
    padding: 12,
    borderRadius: 10,
    marginBottom: 20,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  searchInput: { 
    flex: 1, 
    marginHorizontal: 10,
    fontSize: 16,
    color: '#333',
  },
  produtoGrid: { 
    flexDirection: 'row', 
    flexWrap: 'wrap', 
    justifyContent: 'space-between',
  },
produtoCard: {
  width: '48%', // Mantém a largura para 2 colunas
  minWidth: 160, // Define uma largura mínima para evitar que fique muito estreito
  backgroundColor: '#fff',
  padding: 15,
  borderRadius: 12,
  alignItems: 'center',
  shadowColor: '#000',
  shadowOpacity: 0.1,
  shadowRadius: 4,
  elevation: 2,
  marginBottom: 16,
  borderWidth: 1,
  borderColor: '#ccc',
  position: 'relative',
},
  selectedProduct: {
    borderColor: '#4CAF50',
    borderWidth: 2,
  },
  productImage: {
    width: 60,
    height: 60,
    alignSelf: 'center',
    marginBottom: 10,
  },
  noImageText: {
    fontSize: 12,
    color: '#999',
    textAlign: 'center',
    marginBottom: 10,
  },
  produtoNome: {
    fontWeight: 'bold',
    marginBottom: 6,
    textAlign: 'center',
    fontSize: 16,
    color: '#333',
  },
  produtoCategoria: {
    fontSize: 14,
    color: '#666',
    marginBottom: 5,
  },
  precoTexto: { 
    marginBottom: 10,
    fontSize: 16,
    fontWeight: '600',
    color: '#333',
  },
  checkIcon: {
    position: 'absolute',
    top: 5,
    right: 5,
  },
  carrinhoFlutuante: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: '#fff',
    padding: 20,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    shadowColor: '#000',
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 10,
    borderWidth: 1,
    borderColor: '#ccc',
  },
  carrinhoTitulo: { 
    fontSize: 18, 
    fontWeight: 'bold', 
    marginBottom: 15,
    color: '#333',
  },
  itemCarrinho: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center',
    marginVertical: 8,
  },
  itemInfo: {
    flex: 1,
  },
  itemNome: {
    fontSize: 16,
    color: '#333',
  },
  itemPreco: {
    fontSize: 14,
    color: '#555',
    marginTop: 2,
  },
  controlesItem: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    gap: 12,
  },
  botaoQuantidade: { 
    fontSize: 20, 
    paddingHorizontal: 8,
    color: '#333',
  },
  quantidade: { 
    fontWeight: 'bold',
    fontSize: 16,
    color: '#333',
  },
  totalItem: {
    marginLeft: 15,
    fontWeight: 'bold',
    color: '#333',
  },
  divider: {
    height: 1,
    backgroundColor: '#ccc',
    marginVertical: 10,
  },
  total: { 
    fontSize: 18, 
    fontWeight: 'bold', 
    marginTop: 5,
    color: '#333',
    textAlign: 'right',
  },
  paymentContainer: {
    marginTop: 15,
  },
  paymentTitle: {
    fontWeight: 'bold',
    marginBottom: 8,
    color: '#333',
  },
  paymentOption: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 8,
    paddingHorizontal: 10,
    borderRadius: 6,
    backgroundColor: '#f5f5f5',
    marginBottom: 8,
  },
  selectedPayment: {
    backgroundColor: '#e0e0e0',
  },
  paymentText: {
    marginLeft: 10,
    color: '#333',
    flex: 1,
  },
  paymentCheck: {
    marginLeft: 10,
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
  finalizarButton: {
    backgroundColor: '#333',
    padding: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 15,
  },
  finalizarButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  minimizarBotao: { 
    position: 'absolute',
    top: 10,
    right: 10,
    padding: 8,
    zIndex: 1,
  },
  botaoAbrirCarrinho: {
    position: 'absolute',
    bottom: 20,
    right: 20,
    backgroundColor: '#333',
    borderRadius: 30,
    width: 60,
    height: 60,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 8,
  },
  contadorCarrinho: {
    position: 'absolute',
    top: -5,
    right: -5,
    backgroundColor: '#666',
    borderRadius: 10,
    width: 20,
    height: 20,
    justifyContent: 'center',
    alignItems: 'center',
  },
  contadorTexto: {
    color: '#fff',
    fontSize: 12,
    fontWeight: 'bold',
  },
  botaoAddCarrinhoContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    width: '100%',
    marginTop: 20,
    marginBottom: 30,
  },
  botoesVendaContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    width: '100%',
    marginTop: 20,
    marginBottom: 30,
  },
  botaoAcao: {
    width: '48%',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  botaoFinalizar: {
    backgroundColor: '#28a745',
  },
  botaoCancelar: {
    backgroundColor: '#dc3545',
  },
  botaoAdicionar: {
    backgroundColor: '#333',
  },
  textoBotaoAcao: {
    color: 'white',
    fontWeight: 'bold',
    fontSize: 16,
  },
});

export default VendedorScreen;