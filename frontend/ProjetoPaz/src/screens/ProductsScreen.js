import React, { useState, useEffect } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Modal, ScrollView, ActivityIndicator, Alert, Image, FlatList
} from 'react-native';
import { Ionicons, Feather, MaterialIcons } from '@expo/vector-icons';
import api from '../services/api';

const ProductsScreen = ({ navigation }) => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchText, setSearchText] = useState('');
  const [selectedProducts, setSelectedProducts] = useState([]);
  const [filterVisible, setFilterVisible] = useState(false);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [onlyFavorites, setOnlyFavorites] = useState(false);
  const [sortByPrice, setSortByPrice] = useState(false);
  const [categories, setCategories] = useState([]);

  const extractProdutos = (response) => {
    if (Array.isArray(response?.data)) return response.data;
    if (Array.isArray(response?.data?.content)) return response.data.content;
    return [];
  };

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

  useEffect(() => {
    const fetchFilteredProducts = async () => {
      try {
        setLoading(true);
        let endpoint = '/products/active';

        if (onlyFavorites) {
          endpoint = '/products/favorites';
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
  }, [selectedCategory, onlyFavorites]);

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

  const handleProductSelect = (product) => {
    setSelectedProducts(prev => {
      const existingIndex = prev.findIndex(p => p.id === product.id);
      if (existingIndex >= 0) {
        return prev.filter(p => p.id !== product.id);
      } else {
        return [...prev, product];
      }
    });
  };

  const handleStartSale = (isSelfService = false) => {
    if (selectedProducts.length === 0) {
      Alert.alert('Atenção', 'Selecione pelo menos um produto para iniciar a venda');
      return;
    }

    // Navega para a tela apropriada com os produtos selecionados
    navigation.navigate(isSelfService ? 'AutoAtendimento' : 'Vendedor', {
      selectedProducts: selectedProducts.map(product => ({
        id: product.id,
        name: product.name,
        preco: parseFloat(product.sale_price),
        categoria: product.category,
        img_product: product.img_product
      }))
    });
  };

  const handleEditProduct = (productId) => {
    navigation.navigate('EditarProduto', { produtoId: productId });
  };

  const resetFilters = () => {
    setSelectedCategory(null);
    setOnlyFavorites(false);
    setSortByPrice(false);
    setSearchText('');
  };

  const calculateTotal = () => {
    return selectedProducts.reduce((total, product) => {
      return total + parseFloat(product.sale_price);
    }, 0).toFixed(2);
  };

  const renderProductItem = ({ item }) => (
    <TouchableOpacity
      style={[
        styles.productItem,
        selectedProducts.some(p => p.id === item.id) && styles.selectedProduct
      ]}
      onPress={() => handleProductSelect(item)}
    >
      {item.is_favorite === '1' && (
        <Ionicons name="heart" size={16} color="#FF6B6B" style={styles.favoriteIcon} />
      )}

      {item.img_product ? (
        <Image
          source={{ uri: item.img_product }}
          style={styles.productImage}
          resizeMode="contain"
        />
      ) : (
        <Text style={styles.noImageText}>Sem imagem</Text>
      )}

      <Text style={styles.productName}>{item.name}</Text>
      <Text style={styles.productPrice}>R$ {parseFloat(item.sale_price).toFixed(2)}</Text>
      <Text style={styles.productCategory}>{item.category}</Text>

      <MaterialIcons
        name="edit"
        size={18}
        color="#555"
        style={styles.editIcon}
        onPress={(e) => {
          e.stopPropagation();
          handleEditProduct(item.id);
        }}
      />

      {selectedProducts.some(p => p.id === item.id) && (
        <Ionicons name="checkmark-circle" size={24} color="#4CAF50" style={styles.checkIcon} />
      )}
    </TouchableOpacity>
  );

  if (loading && products.length === 0) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#333" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <Text style={styles.headerTitle}>Produtos</Text>
        <Text style={styles.subtitle}>Seus Produtos</Text>
        
        <View style={styles.searchContainer}>
          <TextInput
            placeholder="Buscar produtos..."
            style={styles.searchInput}
            value={searchText}
            onChangeText={setSearchText}
          />
          <TouchableOpacity 
            style={styles.filterButton}
            onPress={() => setFilterVisible(true)}
          >
            <Feather name="filter" size={20} color="#333" />
          </TouchableOpacity>
        </View>

        <View style={styles.divider} />

        <FlatList
          data={sortedProducts}
          renderItem={renderProductItem}
          keyExtractor={item => item.id.toString()}
          numColumns={2}
          columnWrapperStyle={styles.row}
          scrollEnabled={false}
        />

        <View style={styles.divider} />

        <TouchableOpacity
          style={styles.saleButton}
          onPress={() => handleStartSale(false)}
          disabled={selectedProducts.length === 0 || loading}
        >
          <Text style={styles.saleButtonText}>Iniciar Venda</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.selfServiceButton}
          onPress={() => handleStartSale(true)}
          disabled={selectedProducts.length === 0 || loading}
        >
          <Text style={styles.selfServiceButtonText}>Iniciar Autoatendimento</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.historyButton}
          onPress={() => navigation.navigate('Vendas')}
        >
          <Text style={styles.historyButtonText}>Histórico de Venda</Text>
        </TouchableOpacity>
      </ScrollView>

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
    </View>
  );
};

// Estilos permanecem os mesmos
const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#fff',
    paddingTop: 40,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  scrollContent: {
    paddingBottom: 20,
  },
  headerTitle: {
    fontSize: 24,
    fontWeight: 'bold',
    textAlign: 'center',
    marginBottom: 10,
  },
  subtitle: {
    fontSize: 18,
    fontWeight: 'bold',
    marginLeft: 20,
    marginBottom: 10,
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: 20,
    marginBottom: 10,
  },
  searchInput: {
    flex: 1,
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    padding: 10,
    fontSize: 16,
  },
  filterButton: {
    marginLeft: 10,
    padding: 8,
  },
  divider: {
    height: 1,
    backgroundColor: '#ccc',
    marginVertical: 10,
    marginHorizontal: 20,
  },
  row: {
    justifyContent: 'space-between',
    paddingHorizontal: 15,
  },
  productItem: {
    width: '48%',
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    padding: 15,
    marginBottom: 10,
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
  productName: {
    fontSize: 16,
    fontWeight: 'bold',
    marginBottom: 5,
  },
  productPrice: {
    fontSize: 16,
    color: '#333',
    marginBottom: 5,
  },
  productCategory: {
    fontSize: 14,
    color: '#666',
  },
  favoriteIcon: {
    position: 'absolute',
    top: 5,
    left: 5,
  },
  editIcon: {
    position: 'absolute',
    bottom: 5,
    left: 5,
  },
  checkIcon: {
    position: 'absolute',
    top: 5,
    right: 5,
  },
  saleButton: {
    backgroundColor: '#4CAF50',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginHorizontal: 20,
    marginBottom: 10,
  },
  saleButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  selfServiceButton: {
    backgroundColor: '#2196F3',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginHorizontal: 20,
    marginBottom: 10,
  },
  selfServiceButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  historyButton: {
    backgroundColor: '#333',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginHorizontal: 20,
  },
  historyButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  modalOverlay: {
    flex: 1,
    justifyContent: 'flex-end',
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
  modalContent: {
    backgroundColor: '#fff',
    padding: 20,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
    maxHeight: '80%',
  },
  filterTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    marginBottom: 15,
    textAlign: 'center',
  },
  clearFiltersButton: {
    alignSelf: 'flex-end',
    marginBottom: 15,
  },
  clearFiltersText: {
    color: '#333',
    textDecorationLine: 'underline',
  },
  filterSubtitle: {
    fontSize: 16,
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
    backgroundColor: '#4CAF50',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginTop: 20,
  },
  applyButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
});

export default ProductsScreen;