import React, { useState, useEffect } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Modal, ScrollView, ActivityIndicator, Alert, Image
} from 'react-native';
import { Ionicons, Feather, MaterialIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import api from '../services/api';


const ProductsScreen = ({ navigation }) => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchText, setSearchText] = useState('');
  const [filterVisible, setFilterVisible] = useState(false);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [onlyFavorites, setOnlyFavorites] = useState(false);
  const [onlyDonations, setOnlyDonations] = useState(false);
  const [sortByPrice, setSortByPrice] = useState(false);
  const [categories, setCategories] = useState([]);

  // Novos estados para a lógica de vendas e carrinho
  const [selectedProductsInCart, setSelectedProductsInCart] = useState([]); // [{ product: {}, quantity: 1 }]
  const [isSaleActive, setIsSaleActive] = useState(false);
  const [currentSaleId, setCurrentSaleId] = useState(null); // ID da venda ativa
  const [showCartModal, setShowCartModal] = useState(false);
  const [paymentMethod, setPaymentMethod] = useState(null);
  const [currentSaleOrders, setCurrentSaleOrders] = useState([]); // Para armazenar pedidos finalizados dentro de uma venda (localmente, antes de finalizar a venda)

  const extractProdutos = (response) => {
    if (Array.isArray(response?.data)) return response.data;
    if (Array.isArray(response?.data?.content)) return response.data.content;
    return [];
  };

  // Buscar produtos e categorias ao carregar a tela
  useEffect(() => {
    const fetchInitialData = async () => {
      try {
        setLoading(true);

        const productsResponse = await api.get('/products/active');
        const produtos = extractProdutos(productsResponse);

        setProducts(produtos);

        const uniqueCategories = [...new Set(produtos.map(p => p.category))];
        setCategories(uniqueCategories);

      } catch (error) {
        Alert.alert('Erro', 'Não foi possível carregar os produtos');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchInitialData();
  }, []);

  // Buscar produtos quando filtros mudam
  useEffect(() => {
    const fetchFilteredProducts = async () => {
      try {
        setLoading(true);
        let endpoint = '/products/active';

        if (onlyFavorites) {
          endpoint = '/products/favorites';
        } else if (onlyDonations) {
          endpoint = '/products/donations';
        } else if (selectedCategory) {
          endpoint = `/products/category/${selectedCategory}`;
        }

        const response = await api.get(endpoint);
        const produtosFiltrados = extractProdutos(response);
        setProducts(produtosFiltrados);

      } catch (error) {
        Alert.alert('Erro', 'Falha ao aplicar filtros');
        console.error(error);
      } finally {
        setLoading(false);
      }
    };

    fetchFilteredProducts();
  }, [selectedCategory, onlyFavorites, onlyDonations]);

  // Busca por texto (com debounce)
  useEffect(() => {
    if (searchText.length > 1) {
      const timer = setTimeout(async () => {
        try {
          const response = await api.get(`/products/search?q=${searchText}`);
          const produtosBuscados = extractProdutos(response);
          setProducts(produtosBuscados);
        } catch (error) {
          console.error('Busca falhou:', error);
        }
      }, 500);

      return () => clearTimeout(timer);
    }
  }, [searchText]);

  const sortedProducts = [...products].sort((a, b) => {
    if (sortByPrice) {
      return parseFloat(a.sale_price) - parseFloat(b.sale_price);
    }
    return 0;
  });

  // Lógica de seleção de produto para o carrinho
  const handleProductPress = (product) => {
    if (!isSaleActive) {
      Alert.alert('Venda Inativa', 'Por favor, inicie uma nova venda para adicionar produtos ao carrinho.');
      return;
    }

    setSelectedProductsInCart(prevCart => {
      const existingItem = prevCart.find(item => item.product.id === product.id);
      if (existingItem) {
        // Se o produto já está no carrinho, incrementa a quantidade
        return prevCart.map(item =>
          item.product.id === product.id ? { ...item, quantity: item.quantity + 1 } : item
        );
      } else {
        // Se o produto não está no carrinho, adiciona com quantidade 1
        return [...prevCart, { product, quantity: 1 }];
      }
    });
  };

  // Lógica para remover/diminuir quantidade do carrinho (usado no modal)
  const handleRemoveFromCart = (productId) => {
    setSelectedProductsInCart(prevCart => {
      const existingItem = prevCart.find(item => item.product.id === productId);
      if (existingItem && existingItem.quantity > 1) {
        return prevCart.map(item =>
          item.product.id === productId ? { ...item, quantity: item.quantity - 1 } : item
        );
      } else {
        return prevCart.filter(item => item.product.id !== productId);
      }
    });
  };

  const handleEditProduct = (productId) => {
    navigation.navigate('EditarProduto', { produtoId: productId });
  };

  const resetFilters = () => {
    setSelectedCategory(null);
    setOnlyFavorites(false);
    setOnlyDonations(false);
    setSearchText('');
  };

  const calculateCartTotal = () => {
    return selectedProductsInCart.reduce((total, item) => {
      return total + (parseFloat(item.product.sale_price) * item.quantity);
    }, 0).toFixed(2);
  };

  // Lógica para iniciar uma nova venda
  const handleStartSale = async () => {
    try {
      setLoading(true);
      const response = await api.post('/sales', { method: "manual" });
      const newSale = response.data;

      console.log('Resposta da API ao iniciar venda:', newSale); // LOG DE DEBUG

      // CORREÇÃO AQUI: Acessar o ID da venda dentro de 'content'
      if (newSale && newSale.content && newSale.content.id) {
        setCurrentSaleId(newSale.content.id); // Armazena o ID da nova venda
        setIsSaleActive(true);
        setSelectedProductsInCart([]);
        setCurrentSaleOrders([]);
        Alert.alert('Venda Iniciada', `Venda #${newSale.content.id} iniciada. Você pode começar a adicionar produtos ao carrinho.`);
      } else {
        Alert.alert('Erro', 'Não foi possível obter o ID da venda. Verifique a estrutura da resposta da API.');
        console.error('ID da venda não encontrado na resposta:', newSale);
      }
    } catch (error) {
      Alert.alert('Erro', 'Não foi possível iniciar a venda. Tente novamente.');
      console.error('Erro ao iniciar venda:', error);
    } finally {
      setLoading(false);
    }
  };

  // Lógica para finalizar um pedido (dentro de uma venda)
  const handleFinalizeOrder = async () => {
    console.log('currentSaleId ao finalizar pedido:', currentSaleId); // LOG DE DEBUG

    if (!paymentMethod) {
      Alert.alert('Erro', 'Por favor, selecione uma forma de pagamento.');
      return;
    }
    if (selectedProductsInCart.length === 0) {
      Alert.alert('Erro', 'O carrinho está vazio.');
      return;
    }
    if (!currentSaleId) {
      Alert.alert('Erro', 'Nenhuma venda ativa. Por favor, inicie uma venda primeiro.');
      return;
    }

    try {
      setLoading(true);
      const orderItemsPayload = selectedProductsInCart.map(item => ({
        product_id: item.product.id,
        quantity: item.quantity,
        price: parseFloat(item.product.sale_price),
      }));

      const orderPayload = {
        sale_id: currentSaleId,
        payment_method: paymentMethod,
        total_amount: parseFloat(calculateCartTotal()),
        items: orderItemsPayload,
      };

      const response = await api.post('/orders', orderPayload);
      const newOrder = response.data;

      setCurrentSaleOrders(prevOrders => [...prevOrders, newOrder]);
      setSelectedProductsInCart([]);
      setPaymentMethod(null);
      setShowCartModal(false);
      Alert.alert('Pedido Finalizado', `Pedido #${newOrder.id} concluído com sucesso!`);
    } catch (error) {
      Alert.alert('Erro', 'Não foi possível finalizar o pedido. Tente novamente.');
      console.error('Erro ao finalizar pedido:', error);
    } finally {
      setLoading(false);
    }
  };

  // Lógica para finalizar a venda atual
  const handleFinalizeSale = async () => {
    if (!isSaleActive) {
      Alert.alert('Erro', 'Nenhuma venda ativa para finalizar.');
      return;
    }
    if (!currentSaleId) {
      Alert.alert('Erro', 'ID da venda não encontrado. Algo deu errado.');
      return;
    }

    if (currentSaleOrders.length === 0) {
      Alert.alert('Atenção', 'Nenhum pedido foi finalizado nesta venda. Deseja finalizar a venda mesmo assim?',
        [
          { text: 'Não', style: 'cancel' },
          {
            text: 'Sim',
            onPress: async () => {
              try {
                setLoading(true);
                await api.put(`/sales/completed/${currentSaleId}`);

                Alert.alert('Venda Finalizada', `Venda #${currentSaleId} concluída sem pedidos.`);
                setIsSaleActive(false);
                setCurrentSaleId(null);
                setCurrentSaleOrders([]);
                setSelectedProductsInCart([]);
                setPaymentMethod(null);
              } catch (error) {
                Alert.alert('Erro', 'Não foi possível finalizar a venda. Tente novamente.');
                console.error('Erro ao finalizar venda (sem pedidos):', error);
              } finally {
                setLoading(false);
              }
            }
          }
        ]
      );
      return;
    }

    Alert.alert(
      'Finalizar Venda',
      `Deseja finalizar a venda atual? Total de pedidos: ${currentSaleOrders.length}`,
      [
        {
          text: 'Cancelar',
          style: 'cancel',
        },
        {
          text: 'Sim',
          onPress: async () => {
            try {
              setLoading(true);
              await api.put(`/sales/completed/${currentSaleId}`);

              console.log('Venda Finalizada. Pedidos:', currentSaleOrders);
              Alert.alert('Venda Finalizada', `Venda #${currentSaleId} concluída com ${currentSaleOrders.length} pedidos.`);
              setIsSaleActive(false);
              setCurrentSaleId(null);
              setCurrentSaleOrders([]);
              setSelectedProductsInCart([]);
              setPaymentMethod(null);
            } catch (error) {
              Alert.alert('Erro', 'Não foi possível finalizar a venda. Tente novamente.');
              console.error('Erro ao finalizar venda:', error);
            } finally {
              setLoading(false);
            }
          }
        }
      ]
    );
  };


  if (loading && products.length === 0) {
    return (
      <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={[styles.container, styles.loadingContainer]}>
        <ActivityIndicator size="large" color="#333" />
      </LinearGradient>
    );
  }

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <View style={styles.header}>
          <TouchableOpacity>
            <Ionicons name="menu" size={26} color="black" />
          </TouchableOpacity>
          <Text style={styles.title}>Seus Produtos</Text>
          <Ionicons
            name="person-outline"
            size={26}
            color="black"
            onPress={() => navigation.navigate('ProfileScreen')}
          />
        </View>

        <View style={styles.searchContainer}>
          <Ionicons name="search" size={18} color="#999" style={styles.searchIcon} />
          <TextInput
            placeholder="Buscar produtos..."
            placeholderTextColor="rgba(0,0,0,0.6)"
            style={styles.searchInput}
            value={searchText}
            onChangeText={setSearchText}
          />
          <TouchableOpacity onPress={() => setFilterVisible(true)}>
            <Feather name="filter" size={22} color="black" style={styles.filterIcon} />
          </TouchableOpacity>
        </View>

        {/* Botões Iniciar Venda e Finalizar Venda */}
        <View style={styles.saleButtonsContainer}>
          {!isSaleActive ? (
            <TouchableOpacity
              style={[styles.saleButton, styles.startButton]}
              onPress={handleStartSale}
              disabled={loading}
            >
              <Text style={styles.saleButtonText}>Iniciar Venda</Text>
            </TouchableOpacity>
          ) : (
            <TouchableOpacity
              style={[styles.saleButton, styles.finishButton]}
              onPress={handleFinalizeSale}
              disabled={loading}
            >
              <Text style={styles.saleButtonText}>Finalizar Venda</Text>
            </TouchableOpacity>
          )}
        </View>

        <View style={styles.productsGrid}>
          {sortedProducts.map((product, index) => (
            <TouchableOpacity
              key={product.id}
              style={[
                styles.productBox,
                selectedProductsInCart.some(item => item.product.id === product.id) && styles.selectedProductBox
              ]}
              onPress={() => handleProductPress(product)}
            >
              {product.is_favorite === '1' && (
                <Ionicons
                  name="heart"
                  size={16}
                  color="#FF6B6B"
                  style={styles.favoriteIcon}
                />
              )}

              {/* Imagem do produto */}
              {product.img_product ? (
                <Image
                  source={{ uri: product.img_product }}
                  style={styles.productImage}
                  resizeMode="contain"
                />
              ) : (
                <Text style={{ fontSize: 12, color: '#999', textAlign: 'center' }}>Sem imagem</Text>
              )}

              {/* Nome, preço, categoria */}
              <Text style={styles.productName}>{product.name}</Text>
              <Text style={styles.productPrice}>R$ {parseFloat(product.sale_price).toFixed(2)}</Text>
              <Text style={styles.productCategory}>{product.category}</Text>

              <MaterialIcons
                name="edit"
                size={18}
                color="#555"
                style={styles.editIcon}
                onPress={(e) => {
                  e.stopPropagation();
                  handleEditProduct(product.id);
                }}
              />
            </TouchableOpacity>
          ))}
        </View>

        {/* Botão para abrir o modal do carrinho */}
        {isSaleActive && selectedProductsInCart.length > 0 && (
          <TouchableOpacity
            style={styles.viewCartButton}
            onPress={() => setShowCartModal(true)}
          >
            <Text style={styles.viewCartButtonText}>Ver Carrinho ({selectedProductsInCart.length} itens)</Text>
            <Text style={styles.viewCartButtonText}>Total: R$ {calculateCartTotal()}</Text>
          </TouchableOpacity>
        )}

        <TouchableOpacity
          style={styles.button}
          onPress={() => navigation.navigate('AutoAtendimento')}
        >
          <Text style={styles.buttonText}>Iniciar Autoatendimento (Antigo)</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.button}
          onPress={() => navigation.navigate('VerPedidos')}
        >
          <Text style={styles.buttonText}>Histórico de Pedidos</Text>
        </TouchableOpacity>
      </ScrollView>

      <View style={styles.footer}>
        <Ionicons name="home-outline" size={24} color="white" />
      </View>

      {/* Modal de Filtros */}
      <Modal
        visible={filterVisible}
        animationType="slide"
        transparent
        onRequestClose={() => setFilterVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.filterTitle}>Filtros</Text>

            <TouchableOpacity
              style={styles.clearFiltersButton}
              onPress={resetFilters}
            >
              <Text style={styles.clearFiltersText}>Limpar Filtros</Text>
            </TouchableOpacity>

            <Text style={styles.filterSubtitle}>Categoria</Text>
            {categories.map((category, index) => (
              <TouchableOpacity
                key={index}
                onPress={() => setSelectedCategory(category === selectedCategory ? null : category)}
                style={styles.filterItem}
              >
                <Text style={[
                  styles.filterItemText,
                  selectedCategory === category && styles.selectedFilterItemText
                ]}>
                  {category}
                </Text>
              </TouchableOpacity>
            ))}

            <View style={styles.checkboxContainer}>
              <TouchableOpacity onPress={() => setOnlyFavorites(!onlyFavorites)}>
                <Feather
                  name={onlyFavorites ? 'check-square' : 'square'}
                  size={24}
                  color="#333"
                />
              </TouchableOpacity>
              <Text style={styles.checkboxLabel}>Apenas favoritos</Text>
            </View>

            <View style={styles.checkboxContainer}>
              <TouchableOpacity onPress={() => setOnlyDonations(!onlyDonations)}>
                <Feather
                  name={onlyDonations ? 'check-square' : 'square'}
                  size={24}
                  color="#333"
                />
              </TouchableOpacity>
              <Text style={styles.checkboxLabel}>Apenas doações</Text>
            </View>

            <View style={styles.checkboxContainer}>
              <TouchableOpacity onPress={() => setSortByPrice(!sortByPrice)}>
                <Feather
                  name={sortByPrice ? 'check-square' : 'square'}
                  size={24}
                  color="#333"
                />
              </TouchableOpacity>
              <Text style={styles.checkboxLabel}>Ordenar por preço</Text>
            </View>

            <TouchableOpacity
              style={styles.applyButton}
              onPress={() => setFilterVisible(false)}
            >
              <Text style={styles.applyButtonText}>Aplicar Filtros</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>

      {/* Modal do Carrinho */}
      <Modal
        visible={showCartModal}
        animationType="slide"
        transparent
        onRequestClose={() => setShowCartModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.cartModalContent}>
            <Text style={styles.cartTitle}>Seu Pedido</Text>

            <ScrollView style={styles.cartItemsContainer}>
              {selectedProductsInCart.length === 0 ? (
                <Text style={styles.emptyCartText}>Nenhum item no carrinho.</Text>
              ) : (
                selectedProductsInCart.map((item, index) => (
                  <View key={item.product.id} style={styles.cartItem}>
                    <View style={styles.cartItemInfo}>
                      <Text style={styles.cartItemName}>{item.product.name}</Text>
                      <Text style={styles.cartItemPrice}>R$ {parseFloat(item.product.sale_price).toFixed(2)}</Text>
                    </View>
                    <View style={styles.cartItemControls}>
                      <TouchableOpacity onPress={() => handleRemoveFromCart(item.product.id)}>
                        <Text style={styles.cartQuantityButton}>-</Text>
                      </TouchableOpacity>
                      <Text style={styles.cartQuantity}>{item.quantity}</Text>
                      <TouchableOpacity onPress={() => handleProductPress(item.product)}>
                        <Text style={styles.cartQuantityButton}>+</Text>
                      </TouchableOpacity>
                      <Text style={styles.cartItemTotal}>R$ {(parseFloat(item.product.sale_price) * item.quantity).toFixed(2)}</Text>
                    </View>
                  </View>
                ))
              )}
            </ScrollView>

            <View style={styles.divider} />

            <Text style={styles.cartTotal}>Total: R$ {calculateCartTotal()}</Text>

            {/* Forma de pagamento */}
            <View style={styles.paymentContainer}>
              <Text style={styles.paymentTitle}>Forma de pagamento:</Text>

              <TouchableOpacity
                style={[
                  styles.paymentOption,
                  paymentMethod === 'Cartão' && styles.selectedPayment
                ]}
                onPress={() => setPaymentMethod('Cartão')}
              >
                <Feather name="credit-card" size={20} color="#333" />
                <Text style={styles.paymentText}>Cartão</Text>
                {paymentMethod === 'Cartão' && (
                  <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
                )}
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.paymentOption,
                  paymentMethod === 'Pix' && styles.selectedPayment
                ]}
                onPress={() => setPaymentMethod('Pix')}
              >
                <Feather name="dollar-sign" size={20} color="#333" />
                <Text style={styles.paymentText}>Pix</Text>
                {paymentMethod === 'Pix' && (
                  <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
                )}
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.paymentOption,
                  paymentMethod === 'Dinheiro' && styles.selectedPayment
                ]}
                onPress={() => setPaymentMethod('Dinheiro')}
              >
                <MaterialIcons name="attach-money" size={20} color="#333" />
                <Text style={styles.paymentText}>Dinheiro</Text>
                {paymentMethod === 'Dinheiro' && (
                  <Feather name="check" size={20} color="#333" style={styles.paymentCheck} />
                )}
              </TouchableOpacity>
            </View>

            <TouchableOpacity
              style={styles.finalizeOrderButton}
              onPress={handleFinalizeOrder}
              disabled={loading}
            >
              <Text style={styles.finalizeOrderButtonText}>Finalizar Pedido</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.closeCartModalButton}
              onPress={() => setShowCartModal(false)}
            >
              <Text style={styles.closeCartModalButtonText}>Continuar Comprando</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </LinearGradient>
  );
};


const styles = StyleSheet.create({
  container: {
    flex: 1,
    paddingTop: 40,
  },
  scrollContent: {
    paddingBottom: 80,
  },
  header: {
    flexDirection: 'row',
    width: '90%',
    justifyContent: 'space-between',
    marginBottom: 15,
    alignItems: 'center',
    alignSelf: 'center',
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f0f0f0',
    width: '90%',
    borderRadius: 8,
    paddingHorizontal: 10,
    height: 40,
    marginBottom: 10,
    alignSelf: 'center',
  },
  searchIcon: {
    marginRight: 5,
  },
  searchInput: {
    flex: 1,
    fontSize: 14,
  },
  filterIcon: {
    marginLeft: 5,
  },
  saleButtonsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    width: '90%',
    alignSelf: 'center',
    marginBottom: 15,
  },
  saleButton: {
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
    flex: 1,
    marginHorizontal: 5,
  },
  startButton: {
    backgroundColor: '#4CAF50', // Green for start
  },
  finishButton: {
    backgroundColor: '#F44336', // Red for finish
  },
  saleButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  productsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    width: '90%',
    justifyContent: 'space-between',
    marginBottom: 20,
    alignSelf: 'center',
  },
  productBox: {
    width: '48%', // Ajustado para 2 colunas
    height: 160,
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    marginBottom: 15,
    position: 'relative',
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#fff',
    padding: 10,
  },
  selectedProductBox: {
    borderColor: '#4CAF50', // Green border for selected products
    borderWidth: 2,
  },
  productName: {
    fontSize: 14,
    fontWeight: 'bold',
    textAlign: 'center',
    marginTop: 10,
  },
  productPrice: {
    fontSize: 14,
    color: '#333',
    marginVertical: 5,
  },
  productCategory: {
    fontSize: 12,
    color: '#666',
    marginTop: 4,
  },
  favoriteIcon: {
    position: 'absolute',
    top: 10,
    right: 10,
  },
  editIcon: {
    position: 'absolute',
    bottom: 10,
    right: 10,
  },
  productImage: {
    width: 70,
    height: 70,
    marginBottom: 5,
    borderRadius: 6,
  },
  button: {
    backgroundColor: '#333',
    width: '90%',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
    marginBottom: 10,
    alignSelf: 'center',
  },
  buttonText: {
    color: '#fff',
    fontWeight: 'bold',
  },
  viewCartButton: {
    backgroundColor: '#007BFF', // Blue for view cart
    width: '90%',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginBottom: 10,
    alignSelf: 'center',
  },
  viewCartButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    backgroundColor: '#333',
    width: '100%',
    alignItems: 'center',
    paddingVertical: 10,
  },
  modalOverlay: {
    flex: 1,
    justifyContent: 'flex-end',
    backgroundColor: 'rgba(0,0,0,0.3)',
  },
  modalContent: {
    backgroundColor: '#fff',
    padding: 20,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    minHeight: 300,
  },
  filterTitle: {
    fontWeight: 'bold',
    fontSize: 16,
    marginBottom: 10,
  },
  clearFiltersButton: {
    alignSelf: 'flex-end',
    marginBottom: 10,
  },
  clearFiltersText: {
    color: '#333',
    textDecorationLine: 'underline',
    fontSize: 14,
  },
  filterSubtitle: {
    fontWeight: 'bold',
    marginTop: 10,
    marginBottom: 5,
  },
  filterItem: {
    paddingVertical: 8,
  },
  filterItemText: {
    fontSize: 14,
  },
  selectedFilterItemText: {
    fontWeight: 'bold',
    color: '#333',
  },
  checkboxContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 10,
    gap: 8,
  },
  checkboxLabel: {
    fontSize: 14,
  },
  applyButton: {
    backgroundColor: '#333',
    padding: 14,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 20,
  },
  applyButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  loadingContainer: {
    justifyContent: 'center',
    alignItems: 'center',
    flex: 1,
  },

  // Estilos do Modal do Carrinho
  cartModalContent: {
    backgroundColor: '#fff',
    padding: 20,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    maxHeight: '80%', // Limita a altura do modal
  },
  cartTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 15,
    textAlign: 'center',
  },
  cartItemsContainer: {
    maxHeight: 200, // Altura máxima para a lista de itens do carrinho
    marginBottom: 10,
  },
  emptyCartText: {
    textAlign: 'center',
    color: '#666',
    fontSize: 16,
    paddingVertical: 20,
  },
  cartItem: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#eee',
  },
  cartItemInfo: {
    flex: 1,
  },
  cartItemName: {
    fontSize: 16,
    fontWeight: 'bold',
  },
  cartItemPrice: {
    fontSize: 14,
    color: '#666',
  },
  cartItemControls: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  cartQuantityButton: {
    fontSize: 20,
    fontWeight: 'bold',
    paddingHorizontal: 8,
    color: '#333',
  },
  cartQuantity: {
    fontSize: 16,
    fontWeight: 'bold',
  },
  cartItemTotal: {
    fontSize: 16,
    fontWeight: 'bold',
    marginLeft: 15,
  },
  divider: {
    height: 1,
    backgroundColor: '#ccc',
    marginVertical: 10,
  },
  cartTotal: {
    fontSize: 18,
    fontWeight: 'bold',
    textAlign: 'right',
    marginBottom: 15,
  },
  paymentContainer: {
    marginBottom: 20,
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
  finalizeOrderButton: {
    backgroundColor: '#4CAF50', // Green for finalize order
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginBottom: 10,
  },
  finalizeOrderButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 18,
  },
  closeCartModalButton: {
    backgroundColor: '#6c757d', // Gray for close
    padding: 10,
    borderRadius: 8,
    alignItems: 'center',
  },
  closeCartModalButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },

});

export default ProductsScreen;