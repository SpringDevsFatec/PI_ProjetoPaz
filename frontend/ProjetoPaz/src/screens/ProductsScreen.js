import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, Image } from 'react-native';
import { Ionicons, Feather, MaterialIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

const ProductsScreen = () => {
  const [selected, setSelected] = useState([false, false, false, false]);

  // Alternar ativação do radio button
  const toggleSelection = (index) => {
    const newSelected = [...selected];
    newSelected[index] = !newSelected[index];
    setSelected(newSelected);
  };

  return (
    <LinearGradient colors={['#FFFFFF', '#F5F5F5', '#E0E0E0']} style={styles.container}>
      {/* Cabeçalho com logo/menu, título e perfil */}
      <View style={styles.header}>
        <TouchableOpacity>
          <Ionicons name="menu" size={26} color="black" />
        </TouchableOpacity>
        <Text style={styles.title}>Seus Produtos</Text>
        <Ionicons name="person-outline" size={26} color="black" />
      </View>

      {/* Campo de busca */}
      <View style={styles.searchContainer}>
        <Ionicons name="search" size={18} color="#999" style={styles.searchIcon} />
        <TextInput placeholder="Exemplo: garrafa de água" placeholderTextColor="rgba(0,0,0,0.6)" style={styles.searchInput} />
        <Feather name="filter" size={22} color="black" style={styles.filterIcon} />
      </View>

      {/* Lista de produtos (vazia) */}
      <View style={styles.productsGrid}>
        {[...Array(4)].map((_, index) => (
          <View key={index} style={styles.productBox}>
            <TouchableOpacity onPress={() => toggleSelection(index)} style={styles.radioButton}>
              {selected[index] && <View style={styles.radioInner} />}
            </TouchableOpacity>
            <MaterialIcons name="edit" size={18} color="#555" style={styles.editIcon} />
          </View>
        ))}
      </View>

      {/* Botões */}
      <TouchableOpacity style={styles.button}>
        <Text style={styles.buttonText}>Iniciar Autoatendimento</Text>
      </TouchableOpacity>

      <TouchableOpacity style={styles.button}>
        <Text style={styles.buttonText}>Iniciar Venda</Text>
      </TouchableOpacity>

      {/* Barra inferior */}
      <View style={styles.footer}>
        <Ionicons name="home-outline" size={24} color="white" />
      </View>
    </LinearGradient>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    paddingTop: 40,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    width: '90%',
    marginBottom: 15,
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
    marginBottom: 20,
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
    justifyContent: 'space-between',
    width: '90%',
    marginBottom: 20,
  },
  productBox: {
    width: '45%',
    height: 120,
    borderWidth: 1,
    borderColor: '#ccc',
    borderRadius: 8,
    marginBottom: 15,
    position: 'relative',
    justifyContent: 'center',
    alignItems: 'center',
  },
  radioButton: {
    position: 'absolute',
    top: 5,
    left: 5,
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
    backgroundColor: 'black',
  },
  editIcon: {
    position: 'absolute',
    bottom: 5,
    right: 5,
  },
  button: {
    backgroundColor: '#333',
    width: '90%',
    padding: 12,
    borderRadius: 8,
    alignItems: 'center',
    marginBottom: 10,
  },
  buttonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: 'bold',
  },
  footer: {
    position: 'absolute',
    bottom: 0,
    width: '100%',
    backgroundColor: '#333',
    paddingVertical: 10,
    alignItems: 'center',
  },
});

export default ProductsScreen;
