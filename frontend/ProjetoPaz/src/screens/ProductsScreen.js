import React, { useState } from 'react';
import {
  View, Text, TextInput, TouchableOpacity, StyleSheet,
  Modal, ScrollView
} from 'react-native';
import { Ionicons, Feather, MaterialIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

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
  };

  return (
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
            style={styles.searchInput}
            value={searchText}
            onChangeText={setSearchText}
          />
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
      <Modal
        visible={filterVisible}
        animationType="slide"
        transparent
        onRequestClose={() => setFilterVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.filterTitle}>Filtros</Text>
            
            <Text style={styles.filterSubtitle}>Categoria</Text>
            {categories.map((cat, index) => (
              <TouchableOpacity 
                key={index} 
                onPress={() => setSelectedCategory(cat === selectedCategory ? null : cat)}
                style={styles.filterItem}
              >
                <Text style={[
                  styles.filterItemText,
                  selectedCategory === cat && styles.selectedFilterItemText
                ]}>
                  {cat}
                </Text>
              </TouchableOpacity>
            ))}

            <View style={styles.checkboxContainer}>
              <TouchableOpacity onPress={() => setOnlyHighlights(!onlyHighlights)}>
                <Feather 
                  name={onlyHighlights ? 'check-square' : 'square'} 
                  size={24} 
                  color="#333" 
                />
              </TouchableOpacity>
              <Text style={styles.checkboxLabel}>Ver apenas destaques</Text>
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
  },
  radioButton: {
    position: 'absolute',
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
});

export default ProductsScreen;