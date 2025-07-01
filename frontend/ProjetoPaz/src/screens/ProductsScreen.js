<<<<<<< HEAD
import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Modal, ScrollView
=======
import React, { useState, useEffect } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Modal, ScrollView, ActivityIndicator, Alert, Image, FlatList
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
} from 'react-native';
import { Ionicons, Feather, MaterialIcons } from '@expo/vector-icons';
import api from '../services/api';

<<<<<<< HEAD
// Dados de exemplo para produtos
const productsData = [
  { id: 1, name: 'Garrafa de Água', category: 'Bebida', price: 5.5, highlight: true },
  { id: 2, name: 'Brigadeiro', category: 'Doce', price: 3.2, highlight: false },
  { id: 3, name: 'Coxinha', category: 'Salgado', price: 7.8, highlight: true },
  { id: 4, name: 'Refrigerante', category: 'Bebida', price: 6.0, highlight: false },
];

const ProductsScreen = ({ navigation }) => {
  const [selected, setSelected] = useState(productsData.map(() => false));
  const [searchText, setSearchText] = useState('');
  const [filterVisible, setFilterVisible] = useState(false);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [onlyHighlights, setOnlyHighlights] = useState(false);
  const [sortByPrice, setSortByPrice] = useState(false);

  const categories = ['Doce', 'Bebida', 'Salgado', 'Outro'];

  // Função para filtrar produtos
  const filteredProducts = productsData.filter(product => {
    const matchesSearch = product.name.toLowerCase().includes(searchText.toLowerCase());
    const matchesCategory = !selectedCategory || product.category === selectedCategory;
    const matchesHighlight = !onlyHighlights || product.highlight;
    
    return matchesSearch && matchesCategory && matchesHighlight;
  });

  // Ordenar por preço se ativado
  const sortedProducts = sortByPrice 
    ? [...filteredProducts].sort((a, b) => a.price - b.price) 
    : filteredProducts;

  const toggleSelection = (index) => {
    const newSelected = [...selected];
    newSelected[index] = !newSelected[index];
    setSelected(newSelected);
=======
const ProductsScreen = ({ navigation }) => {
  const [products, setProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchText, setSearchText] = useState('');
  const [selectedProducts, setSelectedProducts] = useState([]);
  const [isSaleActive, setIsSaleActive] = useState(false);
  const [currentSaleId, setCurrentSaleId] = useState(null);
  const [filterVisible, setFilterVisible] = useState(false);
  const [selectedCategory, setSelectedCategory] = useState(null);
  const [onlyFavorites, setOnlyFavorites] = useState(false);
  const [sortByPrice, setSortByPrice] = useState(false);
  const [categories, setCategories] = useState([]);

  const extractProdutos = (response) => {
    if (Array.isArray(response?.data)) return response.data;
    if (Array.isArray(response?.data?.content)) return response.data.content;
    return [];
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
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
        if (onlyFavorites) endpoint = '/products/favorites';
        else if (selectedCategory) endpoint = `/products/category/${selectedCategory}`;
        const response = await api.get(endpoint);
        setProducts(extractProdutos(response));
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
          setProducts(extractProdutos(response));
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
      const exists = prev.find(p => p.id === product.id);
      return exists ? prev.filter(p => p.id !== product.id) : [...prev, product];
    });
  };

  const handleStartSale = async (isSelfService = false) => {

    if (!selectedProducts.length) {
      Alert.alert('Atenção', 'Selecione pelo menos um produto para iniciar a venda');
      return;
    }
    setLoading(true);
    
    try {
      const response = await api.post('/sales', {
        method: isSelfService ? "auto" : "manual",
      });
      const newSale = response.data;
      if (newSale?.content?.id) {
        const saleId = newSale.content.id;
        setCurrentSaleId(saleId);
        setIsSaleActive(true);
        navigation.navigate(isSelfService ? 'AutoAtendimento' : 'Vendedor', {
          selectedProducts: selectedProducts.map(product => ({
            id: product.id,
            name: product.name,
            preco: parseFloat(product.sale_price),
            categoria: product.category,
            img_product: product.img_product
          })),
          saleId
        });
        Alert.alert('Venda Iniciada', `Venda #${newSale.content.code} iniciada.`);
      } else {
        Alert.alert('Erro', 'Não foi possível obter o ID da venda.');
        console.error('ID da venda não encontrado:', newSale);
      }
    } catch (error) {
      Alert.alert('Erro', 'Não foi possível iniciar a venda.');
      console.error('Erro ao iniciar venda:', error);
    } finally {
      setLoading(false);
    }
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
    return selectedProducts.reduce((total, product) => total + parseFloat(product.sale_price), 0).toFixed(2);
  };

  const renderProductItem = ({ item }) => {
    const isSelected = selectedProducts.some(p => p.id === item.id);
    return (
      <TouchableOpacity
        style={[styles.productItem, isSelected && styles.selectedProduct]}
        onPress={() => handleProductSelect(item)}
      >
        {item.is_favorite === '1' && (
          <Ionicons name="heart" size={16} color="#FF6B6B" style={styles.favoriteIcon} />
        )}
        {item.img_product ? (
          <Image source={{ uri: item.img_product }} style={styles.productImage} resizeMode="contain" />
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
        {isSelected && (
          <Ionicons name="checkmark-circle" size={24} color="#4CAF50" style={styles.checkIcon} />
        )}
      </TouchableOpacity>
    );
  };

  if (loading && !products.length) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#333" />
      </View>
    );
  }

  return (
<<<<<<< HEAD
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Cabeçalho */}
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

        {/* Campo de busca */}
        <View style={styles.searchContainer}>
          <Ionicons name="search" size={18} color="#999" style={styles.searchIcon} />
          <TextInput
            placeholder="Exemplo: garrafa de água"
            placeholderTextColor="rgba(0,0,0,0.6)"
=======
    <View style={styles.container}>
      <ScrollView contentContainerStyle={styles.scrollContent}>
        <Text style={styles.headerTitle}>Produtos</Text>
        <Text style={styles.subtitle}>Seus Produtos</Text>
        <View style={styles.searchContainer}>
          <TextInput
            placeholder="Buscar produtos..."
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
            style={styles.searchInput}
            value={searchText}
            onChangeText={setSearchText}
          />
<<<<<<< HEAD
          <TouchableOpacity onPress={() => setFilterVisible(true)}>
            <Feather name="filter" size={22} color="black" style={styles.filterIcon} />
          </TouchableOpacity>
        </View>

        {/* Lista de produtos */}
        <View style={styles.productsGrid}>
          {sortedProducts.map((product, index) => (
            <View key={product.id} style={styles.productBox}>
              <TouchableOpacity 
                onPress={() => toggleSelection(index)} 
                style={styles.radioButton}
              >
                {selected[index] && <View style={styles.radioInner} />}
              </TouchableOpacity>
              <Text style={styles.productName}>{product.name}</Text>
              <Text style={styles.productPrice}>R$ {product.price.toFixed(2)}</Text>
              <MaterialIcons 
                name="edit" 
                size={18} 
                color="#555" 
                style={styles.editIcon} 
              />
            </View>
          ))}
        </View>

        {/* Botões */}
        <TouchableOpacity 
          style={styles.button} 
          onPress={() => navigation.navigate('AutoAtendimento')}
        >
          <Text style={styles.buttonText}>Iniciar Autoatendimento</Text>
        </TouchableOpacity>

        <TouchableOpacity style={styles.button}>
          <Text style={styles.buttonText}>Iniciar Venda</Text>
        </TouchableOpacity>

        {/* Botão de Histórico que leva para VerPedidos.js */}
        <TouchableOpacity 
          style={styles.button}
          onPress={() => navigation.navigate('VerPedidos')}
        >
          <Text style={styles.buttonText}>Histórico de Pedidos</Text>
        </TouchableOpacity>
      </ScrollView>

      {/* Rodapé */}
      <View style={styles.footer}>
        <Ionicons name="home-outline" size={24} color="white" />
      </View>

      {/* Modal de Filtro */}
=======
          <TouchableOpacity style={styles.filterButton} onPress={() => setFilterVisible(true)}>
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
          disabled={!selectedProducts.length || loading}
        >
          <Text style={styles.saleButtonText}>Iniciar Venda</Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={styles.selfServiceButton}
          onPress={() => handleStartSale(true)}
          disabled={!selectedProducts.length || loading}
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

>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
      <Modal
        visible={filterVisible}
        animationType="slide"
        transparent
        onRequestClose={() => setFilterVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.filterTitle}>Filtros</Text>
<<<<<<< HEAD
            
            <Text style={styles.filterSubtitle}>Categoria</Text>
            {categories.map((cat, index) => (
              <TouchableOpacity 
                key={index} 
                onPress={() => setSelectedCategory(cat === selectedCategory ? null : cat)}
=======

            <TouchableOpacity style={styles.clearFiltersButton} onPress={resetFilters}>
              <Text style={styles.clearFiltersText}>Limpar Filtros</Text>
            </TouchableOpacity>

            <Text style={styles.filterSubtitle}>Categoria</Text>
            {categories.map((category, index) => (
              <TouchableOpacity
                key={index}
                onPress={() => setSelectedCategory(category === selectedCategory ? null : category)}
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
                style={styles.filterItem}
              >
                <Text style={[
                  styles.filterItemText,
<<<<<<< HEAD
                  selectedCategory === cat && styles.selectedFilterItemText
                ]}>
                  {cat}
                </Text>
=======
                  selectedCategory === category && styles.selectedFilterItemText
                ]}>{category}</Text>
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
              </TouchableOpacity>
            ))}

            <View style={styles.checkboxContainer}>
<<<<<<< HEAD
              <TouchableOpacity onPress={() => setOnlyHighlights(!onlyHighlights)}>
                <Feather 
                  name={onlyHighlights ? 'check-square' : 'square'} 
                  size={24} 
                  color="#333" 
                />
              </TouchableOpacity>
              <Text style={styles.checkboxLabel}>Ver apenas destaques</Text>
=======
              <TouchableOpacity onPress={() => setOnlyFavorites(!onlyFavorites)}>
                <Feather name={onlyFavorites ? 'check-square' : 'square'} size={24} color="#333" />
              </TouchableOpacity>
              <Text style={styles.checkboxLabel}>Apenas favoritos</Text>
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
            </View>

            <View style={styles.checkboxContainer}>
              <TouchableOpacity onPress={() => setSortByPrice(!sortByPrice)}>
<<<<<<< HEAD
                <Feather 
                  name={sortByPrice ? 'check-square' : 'square'} 
                  size={24} 
                  color="#333" 
                />
=======
                <Feather name={sortByPrice ? 'check-square' : 'square'} size={24} color="#333" />
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
              </TouchableOpacity>
              <Text style={styles.checkboxLabel}>Ordenar por preço</Text>
            </View>

<<<<<<< HEAD
            <TouchableOpacity 
              style={styles.applyButton}
              onPress={() => setFilterVisible(false)}
            >
=======
            <TouchableOpacity style={styles.applyButton} onPress={() => setFilterVisible(false)}>
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
              <Text style={styles.applyButtonText}>Aplicar Filtros</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
<<<<<<< HEAD
    </LinearGradient>
=======
    </View>
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  );
};

// Estilos permanecem os mesmos
const styles = StyleSheet.create({
  container: {
    flex: 1,
<<<<<<< HEAD
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
=======
    backgroundColor: '#fff',
    paddingTop: 40,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
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
<<<<<<< HEAD
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
  productsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    width: '90%',
    justifyContent: 'space-between',
    marginBottom: 20,
    alignSelf: 'center',
  },
  productBox: {
    width: '45%',
    height: 140,
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
=======
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
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
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
    marginBottom: 15,
    color: '#666',
  },
  favoriteIcon: {
    position: 'absolute',
<<<<<<< HEAD
    top: 10,
    left: 10,
    width: 15,
    height: 15,
    borderRadius: 7.5,
    borderWidth: 1,
    borderColor: '#000',
    justifyContent: 'center',
    alignItems: 'center',
  },
  radioInner: {
    width: 9,
    height: 9,
    borderRadius: 4.5,
    backgroundColor: '#000',
  },
  editIcon: {
    position: 'absolute',
    bottom: 10,
    right: 10,
=======
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
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  },
  saleButton: {
    backgroundColor: '#4CAF50',
    padding: 15,
    borderRadius: 8,
    alignItems: 'center',
    marginHorizontal: 20,
    marginBottom: 10,
    alignSelf: 'center',
  },
  saleButtonText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
<<<<<<< HEAD
  footer: {
    position: 'absolute',
    bottom: 0,
    backgroundColor: '#333',
    width: '100%',
    alignItems: 'center',
    paddingVertical: 10,
=======
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
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  },
  modalOverlay: {
    flex: 1,
    justifyContent: 'flex-end',
<<<<<<< HEAD
    backgroundColor: 'rgba(0,0,0,0.3)',
=======
    backgroundColor: 'rgba(0,0,0,0.5)',
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
  },
  modalContent: {
    backgroundColor: '#fff',
    padding: 20,
    borderTopLeftRadius: 16,
    borderTopRightRadius: 16,
<<<<<<< HEAD
    minHeight: 300,
  },
  filterTitle: {
    fontWeight: 'bold',
    fontSize: 16,
    marginBottom: 10,
  },
  filterSubtitle: {
=======
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
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
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
<<<<<<< HEAD
    backgroundColor: '#333',
    padding: 14,
=======
    backgroundColor: '#4CAF50',
    padding: 15,
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
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

<<<<<<< HEAD
export default ProductsScreen;
=======
export default ProductsScreen;
>>>>>>> d9092e3e84143ccf21f64e577d25bc57f0d17430
